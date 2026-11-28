<?php
require_once __DIR__ . '/../includes/bootstrap.php';
$pdo = db();

function get_complaint_public_remarks(PDO $pdo, int $complaintId): array
{
    // Retrieve complaint info
    $compStmt = $pdo->prepare("
        SELECT c.*, o.officer_name as current_officer_name, o.designation as current_officer_designation
        FROM complaints c
        LEFT JOIN officers o ON o.id = c.assigned_officer_id
        WHERE c.id = ? LIMIT 1
    ");
    $compStmt->execute([$complaintId]);
    $complaint = $compStmt->fetch(PDO::FETCH_ASSOC);
    if (!$complaint) return [];

    // Query status history
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
    
    $events = [];
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $rawRemark = trim((string)($row['remarks'] ?? ''));
        
        // Clean and make remarks citizen friendly
        $citizenRemark = $rawRemark;
        
        // If it's an intelligent reassignment or batch rebalancing note, present it cleanly to citizens:
        if (preg_match('/Reassigned to (.*?) \((.*?)\) via/i', $rawRemark, $m)) {
            $citizenRemark = "Transferred and assigned to {$m[1]} ({$m[2]}) for resolution.";
        } elseif (preg_match('/PDF policy escalation to level (\d+) \((.*?)\)/i', $rawRemark, $m)) {
            $citizenRemark = "Grievance escalated to Stage {$m[1]} ({$m[2]}) according to municipal timeline policy.";
        } elseif (preg_match('/Automatically allocated to (.*?)\./i', $rawRemark, $m)) {
            $citizenRemark = "Automatically assigned to {$m[1]} based on ward jurisdiction.";
        }
        
        $events[] = [
            'id' => (int)$row['id'],
            'status' => (string)$row['new_status'],
            'remarks' => $citizenRemark,
            'officer_name' => (string)$row['officer_name'],
            'designation' => (string)$row['designation'],
            'changed_at' => (string)$row['changed_at']
        ];
    }
    return $events;
}

echo "Testing get_complaint_public_remarks on complaint 10184:\n";
print_r(get_complaint_public_remarks($pdo, 10184));

echo "\nTesting on complaint 46592:\n";
$comp46592 = $pdo->query("SELECT id FROM complaints WHERE reference_no = '46592'")->fetchColumn();
print_r(get_complaint_public_remarks($pdo, (int)$comp46592));

echo "\nTesting on complaint 46700:\n";
$comp46700 = $pdo->query("SELECT id FROM complaints WHERE reference_no = '46700'")->fetchColumn();
print_r(get_complaint_public_remarks($pdo, (int)$comp46700));
