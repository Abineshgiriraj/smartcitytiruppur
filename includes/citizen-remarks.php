<?php
declare(strict_types=1);

function citizen_remark_visibility_sql(): string
{
    return "(SELECT a.action FROM audit_logs a
      WHERE a.entity_type='complaint_status_history' AND a.entity_id=h.id
        AND a.action IN ('citizen_remark_published','citizen_remark_private')
      ORDER BY a.id DESC LIMIT 1)";
}

function citizen_remark_record_visibility(PDO $pdo, int $historyId, int $userId, bool $publish): void
{
    if (!$pdo->inTransaction() || $historyId <= 0 || $userId <= 0) {
        throw new LogicException('Remark visibility requires a valid history row and transaction.');
    }
    $pdo->prepare("INSERT INTO audit_logs (user_id,action,entity_type,entity_id)
      VALUES (?,?,'complaint_status_history',?)")
      ->execute([$userId,$publish?'citizen_remark_published':'citizen_remark_private',$historyId]);
}

/**
 * Filter technical audit traces and present citizen-facing remarks cleanly.
 */
function sanitize_public_remark(string $rawRemark): string
{
    $text = trim($rawRemark);
    if ($text === '') {
        return '';
    }

    // Convert internal system and batch messages into clean public remarks
    if (preg_match('/Reassigned to (.*?) \((.*?)\) via Stage 1 PDF Ward-Mapped/i', $text, $m)) {
        return "Transferred and assigned to {$m[1]} ({$m[2]}) based on ward jurisdiction.";
    }
    if (preg_match('/Reassigned to (.*?) \((.*?)\) via (.*?) \(Batch:.*?\)/i', $text, $m)) {
        return "Assigned to {$m[1]} ({$m[2]}) for resolution.";
    }
    if (preg_match('/Intelligent Reassignment applied: Recovered verified active officer from original lifecycle history \(Officer #\d+: (.*?)\)/i', $text, $m)) {
        return "Assigned to {$m[1]} for field inspection and resolution.";
    }
    if (preg_match('/Intelligent Reassignment applied: Allocated via Stage 1 policy candidate \((.*?): (.*?)\)/i', $text, $m)) {
        return "Assigned to {$m[2]} ({$m[1]}) for resolution.";
    }
    if (preg_match('/PDF policy escalation to level (\d+) \((.*?)\)/i', $text, $m)) {
        return "Escalated to Stage {$m[1]} ({$m[2]}) as per municipal grievance policy.";
    }
    if (preg_match('/Automatically allocated to (.*?)\./i', $text, $m)) {
        return "Assigned to {$m[1]} based on ward jurisdiction.";
    }

    return $text;
}

/**
 * Retrieve the complete public remarks timeline for any complaint.
 * Shows status, date, time, officer name, designation, and actual remark.
 */
function get_complaint_public_timeline(PDO $pdo, int $complaintId): array
{
    $compStmt = $pdo->prepare("
        SELECT c.*, o.officer_name as current_officer_name, o.designation as current_officer_designation
        FROM complaints c
        LEFT JOIN officers o ON o.id = c.assigned_officer_id
        WHERE c.id = ? LIMIT 1
    ");
    $compStmt->execute([$complaintId]);
    $complaint = $compStmt->fetch(PDO::FETCH_ASSOC);
    if (!$complaint) return [];

    $sql = "
        SELECT h.id, h.old_status, h.new_status, h.remarks, h.changed_at,
               COALESCE(
                   NULLIF(o_dir.officer_name, ''),
                   NULLIF(o_usr.officer_name, ''),
                   NULLIF(u.full_name, ''),
                   NULLIF(o_lc.officer_name, ''),
                   NULLIF(o_last_lc.officer_name, ''),
                   NULLIF(:cur_name, ''),
                   'Municipal Officer'
               ) AS officer_name,
               COALESCE(
                   NULLIF(o_dir.designation, ''),
                   NULLIF(o_usr.designation, ''),
                   NULLIF(o_lc.designation, ''),
                   NULLIF(o_last_lc.designation, ''),
                   NULLIF(:cur_desig, ''),
                   'Corporation Staff'
               ) AS designation
        FROM complaint_status_history h
        LEFT JOIN users u ON u.id = h.changed_by
        LEFT JOIN officers o_usr ON o_usr.user_id = u.id
        LEFT JOIN officers o_dir ON o_dir.id = h.changed_by
        LEFT JOIN (
            SELECT rcl.ComplaintID, rcl.StatusDate, o.officer_name, o.designation
            FROM raw_complaintlifecycle rcl
            JOIN raw_userdesignationmapper udm ON udm.UserDesignationMapperID = rcl.UserDesignationMapperID
            JOIN officers o ON o.id = udm.UserMasterID
        ) o_lc ON o_lc.ComplaintID = :cid1 AND ABS(TIMESTAMPDIFF(SECOND, o_lc.StatusDate, h.changed_at)) <= 120
        LEFT JOIN (
            SELECT rcl.ComplaintID, o.officer_name, o.designation
            FROM raw_complaintlifecycle rcl
            JOIN raw_userdesignationmapper udm ON udm.UserDesignationMapperID = rcl.UserDesignationMapperID
            JOIN officers o ON o.id = udm.UserMasterID
            WHERE rcl.ComplaintID = :cid2
            ORDER BY rcl.StatusDate DESC LIMIT 1
        ) o_last_lc ON 1=1
        WHERE h.complaint_id = :cid3
        ORDER BY h.changed_at ASC, h.id ASC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':cur_name' => (string)($complaint['current_officer_name'] ?? ''),
        ':cur_desig' => (string)($complaint['current_officer_designation'] ?? ''),
        ':cid1' => $complaintId,
        ':cid2' => $complaintId,
        ':cid3' => $complaintId
    ]);

    $timeline = [];
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $cleanRemark = sanitize_public_remark((string)($row['remarks'] ?? ''));
        if ($cleanRemark === '') {
            $cleanRemark = 'Status recorded as ' . ($row['new_status'] ?? 'Updated');
        }

        $timeline[] = [
            'id' => (int)$row['id'],
            'new_status' => (string)$row['new_status'],
            'remarks' => $cleanRemark,
            'officer_name' => (string)$row['officer_name'],
            'designation' => (string)$row['designation'],
            'changed_at' => (string)$row['changed_at']
        ];
    }

    return $timeline;
}

/** Legacy compatibility wrapper */
function citizen_remarks(PDO $pdo, int $complaintId, bool $verifiedOwner): array
{
    $timeline = get_complaint_public_timeline($pdo, $complaintId);
    return array_map(static fn($item) => [
        'new_status' => $item['new_status'],
        'changed_at' => $item['changed_at'],
        'remarks' => $item['remarks'],
        'event_label' => $item['designation'] . ' · ' . $item['officer_name']
    ], $timeline);
}
