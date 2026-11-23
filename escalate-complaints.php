<?php
declare(strict_types=1);

/**
 * Smart City Tiruppur complaint escalation processor.
 *
 * Preview (no writes):
 *   php escalate-complaints.php --dry-run
 *   php escalate-complaints.php --dry-run --complaint-id=8
 *
 * Execute only after reviewing the preview and confirming the target database:
 *   php escalate-complaints.php --execute --expect-db=smartcitytiruppur --complaint-id=8
 *   php escalate-complaints.php --execute --expect-db=smartcitytiruppur
 *
 * Staff, officer mappings and escalation rules come exclusively from the
 * configured Smart City database. Nothing is read or imported from PGP.
 */
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('CLI execution only.');
}

require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/officer-authority.php';

$args = array_slice($argv, 1);
$execute = false;
$requestedId = null;
$expectedDb = null;

foreach ($args as $arg) {
    if ($arg === '--help') {
        echo "Usage:\n";
        echo "  php escalate-complaints.php --dry-run [--complaint-id=ID]\n";
        echo "  php escalate-complaints.php --execute --expect-db=DATABASE [--complaint-id=ID]\n";
        exit(0);
    }
    if ($arg === '--dry-run') {
        continue;
    }
    if ($arg === '--execute') {
        $execute = true;
        continue;
    }
    if (preg_match('/^--complaint-id=([1-9][0-9]*)$/D', $arg, $matches)) {
        $requestedId = (int) $matches[1];
        continue;
    }
    if (preg_match('/^--expect-db=([a-zA-Z0-9_]+)$/D', $arg, $matches)) {
        $expectedDb = $matches[1];
        continue;
    }
    fwrite(STDERR, "Unknown or invalid argument: {$arg}\n");
    exit(2);
}

// Never run the superseded fixed-four-stage worker. Only the new
// pdf-escalate-complaints.php can make stage-aware decisions, and execution
// of that worker is restricted to an isolated test DB.
if ($execute) {
    fwrite(STDERR,"LEGACY WORKER DISABLED: use pdf-escalate-complaints.php on the test DB after review.\n");
    exit(2);
}
if ($execute && $expectedDb === null) {
    fwrite(STDERR, "Execution requires --expect-db=DATABASE. Run --dry-run first.\n");
    exit(2);
}

$pdo = db();
$actualDb = (string) $pdo->query('SELECT DATABASE()')->fetchColumn();
if ($actualDb === '') {
    fwrite(STDERR, "No database selected.\n");
    exit(2);
}
if ($expectedDb !== null && $actualDb !== $expectedDb) {
    fwrite(STDERR, "Database mismatch: connected to {$actualDb}, expected {$expectedDb}. Nothing changed.\n");
    exit(2);
}

// Hard stop: never execute this incomplete authority rollout on the main DB.
if ($execute && !preg_match('/_test$/i', $actualDb)) {
    fwrite(STDERR, "SAFETY BLOCK: execution is restricted to an isolated *_test database until approval review.\n");
    exit(2);
}

// Keep the CLI's timestamps consistent with config/app.php (Asia/Kolkata).
// This changes only the current connection's time zone.
$pdo->exec("SET SESSION time_zone = '+05:30'");

/**
 * Return a non-mutating escalation decision for one complaint.
 * Complaint row must be read from the configured Smart City connection.
 */
function escalationDecision(PDO $pdo, array $complaint, DateTimeImmutable $now, bool $nullableLevelId): array
{
    $skip = static function (string $reason): array {
        return ['ready' => false, 'reason' => $reason];
    };

    if ((int) ($complaint['assigned_officer_id'] ?? 0) <= 0) {
        return $skip('UNASSIGNED: requires initial officer allocation, not escalation');
    }

    if (in_array($complaint['status'], ['Resolved', 'Closed', 'Rejected'], true)
        || (int) $complaint['is_deleted'] !== 0) {
        return $skip('NOT_OPEN');
    }

    $history = $pdo->prepare(
        'SELECT level_no, escalated_at FROM complaint_escalations
         WHERE complaint_id = ? ORDER BY escalated_at DESC, id DESC LIMIT 1'
    );
    $history->execute([(int) $complaint['id']]);
    $last = $history->fetch(PDO::FETCH_ASSOC);
    $currentLevel = $last ? (int) $last['level_no'] : 1;

    if ($currentLevel < 1) return $skip('INVALID_CURRENT_LEVEL');
    // Department allocation supports stages 5-7; this four-stage engine does not.
    if ($currentLevel >= 4) return $skip('NO_ACTIVE_NEXT_STAGE_OFFICER');

    if (!valid_complaint_routing_context(
        $pdo,(int)$complaint['department_id'],(int)$complaint['category_id'],
        (int)$complaint['zone_id'],(int)$complaint['ward_id']
    )) return $skip('INVALID_DEPARTMENT_OR_LOCATION');

    if (!officer_stage_authorized(
        $pdo,(int)$complaint['category_id'],$currentLevel,
        (int)$complaint['assigned_officer_id'],(int)$complaint['department_id'],
        (int)$complaint['ward_id']
    )) return $skip('UNVERIFIED_CURRENT_STAGE_AUTHORITY');

    if ($last) {
        $lastOfficer=$pdo->prepare(
            'SELECT officer_id FROM complaint_escalations
             WHERE complaint_id=? ORDER BY escalated_at DESC,id DESC LIMIT 1'
        );
        $lastOfficer->execute([(int)$complaint['id']]);
        if ((int)$lastOfficer->fetchColumn()!==(int)$complaint['assigned_officer_id']) {
            return $skip('ASSIGNMENT_STAGE_MISMATCH: manual review needed');
        }
    }

    $locationParams = [
        (int) $complaint['department_id'],
        (int) $complaint['category_id'],
        (int) $complaint['zone_id'],
        (int) $complaint['ward_id'],
    ];

    // Ward/department-specific PGP-style stage rule takes precedence if configured.
    $currentRule = $pdo->prepare(
        'SELECT escalation_days AS days FROM department_allocations
         WHERE department_id = ? AND category_id = ? AND zone_id = ?
           AND ward_id = ? AND stage_no = ? AND is_active = 1 LIMIT 1'
    );
    $currentRule->execute(array_merge($locationParams, [$currentLevel]));
    $period = $currentRule->fetch(PDO::FETCH_ASSOC);

    if (!$period) {
        $currentRule = $pdo->prepare(
            'SELECT escalation_period_days AS days FROM escalation_officer_levels
             WHERE category_id = ? AND level_no = ? AND is_active = 1 LIMIT 1'
        );
        $currentRule->execute([(int) $complaint['category_id'], $currentLevel]);
        $period = $currentRule->fetch(PDO::FETCH_ASSOC);
    }

    if (!$period || (int) $period['days'] <= 0) {
        return $skip('NO_ACTIVE_CURRENT_STAGE_RULE');
    }

    try {
        // Honor the existing category/assignment deadline. Older code could
        // escalate after 3 days even when due_at was 7 or 34 days ahead.
        if (!empty($complaint['due_at'])) {
            $deadline = new DateTimeImmutable((string) $complaint['due_at']);
        } else {
            $start = $last ? $last['escalated_at'] : $complaint['created_at'];
            $deadline = (new DateTimeImmutable((string) $start))
                ->modify('+' . (int) $period['days'] . ' days');
        }
    } catch (Throwable $error) {
        return $skip('INVALID_DEADLINE');
    }

    if ($deadline > $now) {
        return $skip('WITHIN_SLA: due ' . $deadline->format('Y-m-d H:i:s'));
    }

    $nextNumber = $currentLevel + 1;
    $nextAllocation = $pdo->prepare(
        "SELECT da.stage_no AS level_no, da.officer_id,
                da.escalation_days AS days,
                COALESCE(NULLIF(o.designation, ''), o.officer_name) AS label
         FROM department_allocations da
         INNER JOIN officers o ON o.id = da.officer_id AND o.is_active = 1
         WHERE da.department_id = ? AND da.category_id = ? AND da.zone_id = ?
           AND da.ward_id = ? AND da.stage_no = ? AND da.is_active = 1 LIMIT 1"
    );
    $nextAllocation->execute(array_merge($locationParams, [$nextNumber]));
    $next = $nextAllocation->fetch(PDO::FETCH_ASSOC);
    $levelId = null;
    $ruleSource = 'department_allocations';

    if ($next) {
        // Only link an escalation_level_id when it describes the SAME officer.
        // Otherwise it is null, which requires the compatible nullable schema.
        $linked = $pdo->prepare(
            'SELECT id FROM escalation_officer_levels
             WHERE category_id = ? AND level_no = ? AND officer_id = ? AND is_active = 1 LIMIT 1'
        );
        $linked->execute([(int) $complaint['category_id'], $nextNumber, (int) $next['officer_id']]);
        $matchedLevel = $linked->fetchColumn();
        $levelId = $matchedLevel === false ? null : (int) $matchedLevel;
    } else {
        $nextRule = $pdo->prepare(
            'SELECT el.id, el.level_no, el.officer_id, el.escalation_period_days AS days,
                    el.designation AS label
             FROM escalation_officer_levels el
             INNER JOIN officers o ON o.id = el.officer_id AND o.is_active = 1
             WHERE el.category_id = ? AND el.level_no = ? AND el.is_active = 1 LIMIT 1'
        );
        $nextRule->execute([(int) $complaint['category_id'], $nextNumber]);
        $next = $nextRule->fetch(PDO::FETCH_ASSOC);
        $ruleSource = 'escalation_officer_levels';
        if ($next) {
            $levelId = (int) $next['id'];
        }
    }

    if (!$next) {
        return $skip('NO_ACTIVE_NEXT_STAGE_OFFICER');
    }

    if ((int) $next['officer_id'] === (int) $complaint['assigned_officer_id']) {
        return $skip('SAME_OFFICER_AT_NEXT_STAGE: mapping requires review');
    }
    if (!officer_stage_authorized(
        $pdo,(int)$complaint['category_id'],$nextNumber,(int)$next['officer_id'],
        (int)$complaint['department_id'],(int)$complaint['ward_id']
    )) return $skip('UNVERIFIED_NEXT_STAGE_AUTHORITY');

    if ((int) $next['days'] <= 0) {
        return $skip('INVALID_NEXT_STAGE_PERIOD');
    }

    if ($levelId === null && !$nullableLevelId) {
        return $skip('SCHEMA_REQUIRES_NULLABLE_ESCALATION_LEVEL_ID');
    }

    return [
        'ready' => true,
        'reason' => 'ELIGIBLE',
        'current_level' => $currentLevel,
        'next_level' => (int) $next['level_no'],
        'next_officer_id' => (int) $next['officer_id'],
        'next_period_days' => (int) $next['days'],
        'next_label' => (string) $next['label'],
        'level_id' => $levelId,
        'rule_source' => $ruleSource,
        'deadline' => $deadline->format('Y-m-d H:i:s'),
    ];
}

$levelColumn = $pdo->query("SHOW COLUMNS FROM complaint_escalations LIKE 'escalation_level_id'")
    ->fetch(PDO::FETCH_ASSOC);
if (!$levelColumn) {
    fwrite(STDERR, "Missing complaint_escalations.escalation_level_id column.\n");
    exit(2);
}
$nullableLevelId = strtoupper((string) $levelColumn['Null']) === 'YES';

$sql = "SELECT id, reference_no, status, is_deleted, assigned_officer_id,
               department_id, category_id, zone_id, ward_id, created_at, due_at
        FROM complaints WHERE is_deleted = 0
          AND status NOT IN ('Resolved', 'Closed', 'Rejected')";
$params = [];
if ($requestedId !== null) {
    $sql .= ' AND id = ?';
    $params[] = $requestedId;
}
$sql .= ' ORDER BY id ASC';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$complaints = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo ($execute ? '[EXECUTE]' : '[DRY RUN]') . " database={$actualDb}, complaints=" . count($complaints) . PHP_EOL;

$locked = false;
if ($execute) {
    // MySQL named lock prevents two scheduled jobs from escalating the same
    // complaint concurrently. SELECT ... FOR UPDATE below also protects rows.
    $getLock = $pdo->query("SELECT GET_LOCK('smartcity_tiruppur_escalations', 0)")->fetchColumn();
    if ((int) $getLock !== 1) {
        fwrite(STDERR, "Another escalation job is active. Nothing changed.\n");
        exit(2);
    }
    $locked = true;
}

$eligible = 0;
$applied = 0;
$failed = 0;

try {
    foreach ($complaints as $complaint) {
        $now = new DateTimeImmutable('now');

        if (!$execute) {
            $decision = escalationDecision($pdo, $complaint, $now, $nullableLevelId);
            if (!$decision['ready']) {
                echo "[SKIP] #{$complaint['id']} {$complaint['reference_no']}: {$decision['reason']}\n";
                continue;
            }
            $eligible++;
            echo "[WOULD ESCALATE] #{$complaint['id']} {$complaint['reference_no']} "
                . "L{$decision['current_level']} -> L{$decision['next_level']}, "
                . "officer={$decision['next_officer_id']}, source={$decision['rule_source']}, "
                . "deadline={$decision['deadline']}\n";
            continue;
        }

        try {
            $pdo->beginTransaction();

            // Re-fetch under row lock; a citizen/officer may have resolved or
            // reassigned the complaint since the candidate list was retrieved.
            $rowStmt = $pdo->prepare(
                'SELECT id, reference_no, status, is_deleted, assigned_officer_id,
                        department_id, category_id, zone_id, ward_id, created_at, due_at
                 FROM complaints WHERE id = ? FOR UPDATE'
            );
            $rowStmt->execute([(int) $complaint['id']]);
            $fresh = $rowStmt->fetch(PDO::FETCH_ASSOC);
            if (!$fresh) {
                $pdo->rollBack();
                continue;
            }

            $now = new DateTimeImmutable('now');
            $decision = escalationDecision($pdo, $fresh, $now, $nullableLevelId);
            if (!$decision['ready']) {
                $pdo->rollBack();
                echo "[SKIP] #{$fresh['id']} {$fresh['reference_no']}: {$decision['reason']}\n";
                continue;
            }

            $eligible++;
            $at = $now->format('Y-m-d H:i:s');
            $nextDueAt = $now->modify('+' . $decision['next_period_days'] . ' days')
                ->format('Y-m-d H:i:s');

            $update = $pdo->prepare(
                'UPDATE complaints SET assigned_officer_id = ?, due_at = ?, updated_at = ?
                 WHERE id = ?'
            );
            $update->execute([$decision['next_officer_id'], $nextDueAt, $at, (int) $fresh['id']]);

            $close = $pdo->prepare(
                'UPDATE complaint_assignments SET is_current = 0, unassigned_at = ?
                 WHERE complaint_id = ? AND is_current = 1'
            );
            $close->execute([$at, (int) $fresh['id']]);

            $add = $pdo->prepare(
                'INSERT INTO complaint_assignments
                 (complaint_id, officer_id, assigned_by, assigned_at, is_current)
                 VALUES (?, ?, NULL, ?, 1)'
            );
            $add->execute([(int) $fresh['id'], $decision['next_officer_id'], $at]);

            // Retain the workflow status, but make the escalation visible to citizens.
            $note = sprintf(
                'Auto-escalated to Stage %d (%s) after SLA deadline %s.',
                $decision['next_level'], $decision['next_label'], $decision['deadline']
            );
            $history = $pdo->prepare(
                'INSERT INTO complaint_status_history
                 (complaint_id, old_status, new_status, remarks, changed_by, changed_at)
                 VALUES (?, ?, ?, ?, NULL, ?)'
            );
            $history->execute([
                (int) $fresh['id'], $fresh['status'], $fresh['status'], $note, $at,
            ]);

            $escalation = $pdo->prepare(
                'INSERT INTO complaint_escalations
                 (complaint_id, escalation_level_id, level_no, officer_id, escalated_at)
                 VALUES (?, ?, ?, ?, ?)'
            );
            $escalation->execute([
                (int) $fresh['id'], $decision['level_id'], $decision['next_level'],
                $decision['next_officer_id'], $at,
            ]);

            $pdo->commit();
            $applied++;
            echo "[ESCALATED] #{$fresh['id']} {$fresh['reference_no']} "
                . "to level {$decision['next_level']} officer {$decision['next_officer_id']}\n";
        } catch (Throwable $error) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $failed++;
            log_message('Escalation failed for complaint ' . $complaint['id'] . ': ' . $error->getMessage());
            fwrite(STDERR, "[ERROR] #{$complaint['id']}: {$error->getMessage()}\n");
        }
    }
} finally {
    if ($locked) {
        $pdo->query("SELECT RELEASE_LOCK('smartcity_tiruppur_escalations')");
    }
}

echo ($execute
    ? "Escalation complete: {$applied} applied, {$failed} errors.\n"
    : "Preview complete: {$eligible} eligible, no database changes.\n");

exit($failed === 0 ? 0 : 1);
