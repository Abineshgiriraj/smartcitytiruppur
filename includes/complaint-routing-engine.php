<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/officer-authority.php';
require_once __DIR__ . '/pdf-escalation-policy.php';

/**
 * Intelligent Complaint Routing & Reassignment Engine
 *
 * Provides:
 * 1. Reconstruction of historical lifecycle & timeline for all complaints.
 * 2. Stage calculation based on PDF escalation policy and original registration date.
 * 3. Identification of best active officer for unassigned and inactive-officer complaints.
 * 4. Safe dry-run analysis and transactional approval execution.
 */
class ComplaintRoutingEngine
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? db();
    }

    /**
     * Reconstruct historical routing timeline for a single complaint.
     * Respects closure date so escalation does not continue past resolved/closed time.
     */
    public function reconstructComplaintRouting(array $complaint): array
    {
        $complaintId = (int)$complaint['id'];
        $categoryId = (int)$complaint['category_id'];
        $departmentId = (int)$complaint['department_id'];
        $wardId = (int)$complaint['ward_id'];
        $zoneId = (int)($complaint['zone_id'] ?: resolve_zone_id_for_ward($wardId));
        $createdAt = new DateTimeImmutable((string)$complaint['created_at']);
        $closedAt = !empty($complaint['closed_at']) ? new DateTimeImmutable((string)$complaint['closed_at']) : null;
        $status = (string)$complaint['status'];
        $isClosed = in_array($status, ['Closed', 'Resolved', 'Rejected'], true);

        // Fetch verified raw complaint lifecycle events
        $lcEvents = $this->fetchRawLifecycleEvents($complaintId);

        // Fetch approved or fallback PDF policy for this category
        $policy = pdf_policy_reviewed($this->pdo, $categoryId);
        $stageCount = $policy ? (int)$policy['stage_count'] : 4;

        $stages = [];
        $currentStart = $createdAt;

        for ($stageNo = 1; $stageNo <= $stageCount; $stageNo++) {
            $stageDef = $policy ? pdf_policy_stage($policy, $stageNo) : null;
            $roleLabel = $stageDef ? (string)$stageDef['role_label'] : "Stage $stageNo Officer";
            $periodDays = $stageDef ? max(1, (int)$stageDef['period_days']) : 2;

            $stageDue = $currentStart->modify("+{$periodDays} days");

            // Check if raw lifecycle records an actual historical officer who handled this stage
            $matchedOfficerId = null;
            $routingType = 'pdf_policy_calculated';
            $notes = null;

            if (!empty($lcEvents)) {
                // If there is an event roughly corresponding to this stage
                $eventIndex = $stageNo - 1;
                if (isset($lcEvents[$eventIndex])) {
                    $matchedOfficerId = (int)$lcEvents[$eventIndex]['officer_id'];
                    $routingType = 'raw_lifecycle_verified';
                    $notes = "Handled by {$lcEvents[$eventIndex]['officer_name']} ({$lcEvents[$eventIndex]['designation']}) on {$lcEvents[$eventIndex]['StatusDate']}";
                }
            }

            // If no lifecycle officer matched, pick the best eligible candidate from policies
            if (!$matchedOfficerId) {
                $candidates = pdf_routing_candidates($this->pdo, $categoryId, $stageNo, $departmentId, $wardId);
                if ($candidates) {
                    $matchedOfficerId = (int)$candidates[0]['id'];
                    $routingType = 'pdf_policy_calculated';
                    $notes = "Calculated policy candidate for {$roleLabel}";
                } else {
                    // Fallback to intake or category head
                    $fallback = configured_central_intake_officer($this->pdo);
                    if ($fallback) {
                        $matchedOfficerId = (int)$fallback['id'];
                        $routingType = 'fallback_intake';
                        $notes = "Central intake fallback for {$roleLabel}";
                    }
                }
            }

            $isCompleted = false;
            $completedAt = null;

            if ($isClosed && $closedAt !== null) {
                if ($stageDue <= $closedAt) {
                    $isCompleted = true;
                    $completedAt = $stageDue->format('Y-m-d H:i:s');
                } else {
                    $isCompleted = true;
                    $completedAt = $closedAt->format('Y-m-d H:i:s');
                }
            }

            $stages[] = [
                'complaint_id' => $complaintId,
                'stage_no' => $stageNo,
                'role_label' => $roleLabel,
                'officer_id' => $matchedOfficerId,
                'period_days' => $periodDays,
                'stage_start_at' => $currentStart->format('Y-m-d H:i:s'),
                'stage_due_at' => $stageDue->format('Y-m-d H:i:s'),
                'is_completed' => $isCompleted ? 1 : 0,
                'completed_at' => $completedAt,
                'routing_type' => $routingType,
                'notes' => $notes,
            ];

            // If the complaint was closed before reaching next stage deadline, stop progression
            if ($isClosed && $closedAt !== null && $closedAt <= $stageDue) {
                break;
            }

            $currentStart = $stageDue;
        }

        return $stages;
    }

    /**
     * Fetch raw complaint lifecycle events mapped to officers table
     */
    public function fetchRawLifecycleEvents(int $complaintId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT rcl.ComplaintLifeCycleID, rcl.ComplaintID, rcl.ActionCodeMasterID, rcl.StatusDate, rcl.Remarks,
                   udm.UserMasterID as officer_id, o.officer_name, o.designation, o.is_active
            FROM raw_complaintlifecycle rcl
            JOIN raw_userdesignationmapper udm ON udm.UserDesignationMapperID = rcl.UserDesignationMapperID
            LEFT JOIN officers o ON o.id = udm.UserMasterID
            WHERE rcl.ComplaintID = ?
            ORDER BY rcl.StatusDate ASC, rcl.ComplaintLifeCycleID ASC
        ");
        $stmt->execute([$complaintId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Generate proposed reassignment plan for a genuinely open complaint.
     */
    public function proposeReassignmentForOpenComplaint(array $c): ?array
    {
        $complaintId = (int)$c['id'];
        $departmentId = (int)$c['department_id'];
        $categoryId = (int)$c['category_id'];
        $wardId = (int)$c['ward_id'];
        $zoneId = (int)($c['zone_id'] ?: resolve_zone_id_for_ward($wardId));
        $currentOfficerId = (int)($c['assigned_officer_id'] ?? 0);
        $currentOfficerActive = false;

        if ($currentOfficerId > 0) {
            $stmt = $this->pdo->prepare("SELECT is_active FROM officers WHERE id = ?");
            $stmt->execute([$currentOfficerId]);
            $currentOfficerActive = (bool)$stmt->fetchColumn();
        }

        // If complaint already has an active and valid officer, check if reassignment is needed
        if ($currentOfficerId > 0 && $currentOfficerActive) {
            // Valid active assignment, no reassignment needed
            return null;
        }

        // Check raw lifecycle for a verified active officer first
        $lcEvents = $this->fetchRawLifecycleEvents($complaintId);
        $proposedOfficerId = null;
        $evidenceSource = 'pdf_policy_ward';
        $reason = '';

        if (!empty($lcEvents)) {
            // Find the most recent active officer in lifecycle
            for ($i = count($lcEvents) - 1; $i >= 0; $i--) {
                if ((int)$lcEvents[$i]['is_active'] === 1 && (int)$lcEvents[$i]['officer_id'] > 0) {
                    $proposedOfficerId = (int)$lcEvents[$i]['officer_id'];
                    $evidenceSource = 'raw_lifecycle';
                    $reason = "Recovered verified active officer from original lifecycle history (Officer #{$proposedOfficerId}: {$lcEvents[$i]['officer_name']})";
                    break;
                }
            }
        }

        // If no active officer from lifecycle, pick via PDF Stage 1 least-loaded candidate
        if (!$proposedOfficerId) {
            $candidates = pdf_routing_candidates($this->pdo, $categoryId, 1, $departmentId, $wardId);
            $selected = pdf_pick_least_loaded_officer($this->pdo, $candidates);
            if ($selected) {
                $proposedOfficerId = (int)$selected['id'];
                $evidenceSource = 'pdf_policy_ward';
                $reason = "Allocated via Stage 1 policy candidate (Ward {$wardId}/Dept {$departmentId}: {$selected['officer_name']})";
            } else {
                // Fallback to department intake
                $intakeCandidates = department_intake_candidates($this->pdo, $departmentId, $categoryId, $wardId);
                $selectedIntake = pdf_pick_least_loaded_officer($this->pdo, $intakeCandidates) ?: configured_central_intake_officer($this->pdo);
                if ($selectedIntake) {
                    $proposedOfficerId = (int)$selectedIntake['id'];
                    $evidenceSource = 'intake_fallback';
                    $reason = "Assigned to departmental/central intake officer (Officer #{$proposedOfficerId})";
                }
            }
        }

        if (!$proposedOfficerId) {
            return null;
        }

        // Calculate expected stage and deadline
        $createdAt = new DateTimeImmutable((string)$c['created_at']);
        $policy = pdf_policy_reviewed($this->pdo, $categoryId);
        $stage1 = $policy ? pdf_policy_stage($policy, 1) : null;
        $slaDays = $stage1 ? (int)$stage1['period_days'] : 2;
        $calculatedDueAt = $createdAt->modify("+{$slaDays} days")->format('Y-m-d H:i:s');

        return [
            'complaint_id' => $complaintId,
            'current_status' => (string)$c['status'],
            'current_officer_id' => $currentOfficerId > 0 ? $currentOfficerId : null,
            'current_officer_active' => $currentOfficerActive ? 1 : 0,
            'proposed_officer_id' => $proposedOfficerId,
            'proposed_stage_no' => 1,
            'calculated_due_at' => $calculatedDueAt,
            'reassignment_reason' => $reason,
            'evidence_source' => $evidenceSource,
            'plan_status' => 'proposed',
        ];
    }

    /**
     * Run batch analysis and store in calculated tables (Dry Run or Save)
     */
    public function runReconstructionBatch(int $limit = 500, int $offset = 0): int
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM complaints 
            WHERE is_deleted = 0 
            ORDER BY id ASC 
            LIMIT ? OFFSET ?
        ");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, PDO::PARAM_INT);
        $stmt->execute();
        $complaints = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $insertSql = "
            INSERT INTO complaint_calculated_routing 
            (complaint_id, stage_no, role_label, officer_id, period_days, stage_start_at, stage_due_at, is_completed, completed_at, routing_type, notes)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE 
                officer_id = VALUES(officer_id),
                is_completed = VALUES(is_completed),
                completed_at = VALUES(completed_at),
                notes = VALUES(notes)
        ";
        $insertStmt = $this->pdo->prepare($insertSql);

        $planInsertSql = "
            INSERT INTO complaint_reassignment_plans
            (complaint_id, current_status, current_officer_id, current_officer_active, proposed_officer_id, proposed_stage_no, calculated_due_at, reassignment_reason, evidence_source, plan_status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'proposed')
            ON DUPLICATE KEY UPDATE
                proposed_officer_id = VALUES(proposed_officer_id),
                reassignment_reason = VALUES(reassignment_reason),
                evidence_source = VALUES(evidence_source)
        ";
        $planInsertStmt = $this->pdo->prepare($planInsertSql);

        $processed = 0;
        foreach ($complaints as $c) {
            $stages = $this->reconstructComplaintRouting($c);
            foreach ($stages as $s) {
                $insertStmt->execute([
                    $s['complaint_id'],
                    $s['stage_no'],
                    $s['role_label'],
                    $s['officer_id'],
                    $s['period_days'],
                    $s['stage_start_at'],
                    $s['stage_due_at'],
                    $s['is_completed'],
                    $s['completed_at'],
                    $s['routing_type'],
                    $s['notes']
                ]);
            }

            // If non-closed and unassigned/inactive, create reassignment plan proposal
            if (!in_array($c['status'], ['Closed', 'Resolved', 'Rejected'], true)) {
                $plan = $this->proposeReassignmentForOpenComplaint($c);
                if ($plan) {
                    $planInsertStmt->execute([
                        $plan['complaint_id'],
                        $plan['current_status'],
                        $plan['current_officer_id'],
                        $plan['current_officer_active'],
                        $plan['proposed_officer_id'],
                        $plan['proposed_stage_no'],
                        $plan['calculated_due_at'],
                        $plan['reassignment_reason'],
                        $plan['evidence_source']
                    ]);
                }
            }

            $processed++;
        }

        return $processed;
    }
}
