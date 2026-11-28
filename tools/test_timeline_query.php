<?php
require_once __DIR__ . '/../includes/bootstrap.php';
$pdo = db();

echo "Testing combined timeline query for complaint 10184:\n";
$cid = 10184;

// Let's test a query that retrieves all status history rows along with the best officer info
$sql = "
    SELECT h.id, h.complaint_id, h.old_status, h.new_status, h.remarks, h.changed_at,
           COALESCE(
               NULLIF(o_dir.officer_name, ''),
               NULLIF(o_usr.officer_name, ''),
               NULLIF(u.full_name, ''),
               NULLIF(o_lc.officer_name, ''),
               NULLIF(o_cur.officer_name, ''),
               'Municipal Authority'
           ) AS actor_name,
           COALESCE(
               NULLIF(o_dir.designation, ''),
               NULLIF(o_usr.designation, ''),
               NULLIF(o_lc.designation, ''),
               NULLIF(o_cur.designation, ''),
               'Corporation Staff'
           ) AS actor_designation
    FROM complaint_status_history h
    JOIN complaints c ON c.id = h.complaint_id
    LEFT JOIN users u ON u.id = h.changed_by
    LEFT JOIN officers o_usr ON o_usr.user_id = u.id
    LEFT JOIN officers o_dir ON o_dir.id = h.changed_by
    LEFT JOIN (
        SELECT rcl.ComplaintID, rcl.StatusDate, o.officer_name, o.designation
        FROM raw_complaintlifecycle rcl
        JOIN raw_userdesignationmapper udm ON udm.UserDesignationMapperID = rcl.UserDesignationMapperID
        JOIN officers o ON o.id = udm.UserMasterID
    ) o_lc ON o_lc.ComplaintID = c.id AND ABS(TIMESTAMPDIFF(SECOND, o_lc.StatusDate, h.changed_at)) <= 60
    LEFT JOIN officers o_cur ON o_cur.id = c.assigned_officer_id
    WHERE h.complaint_id = ?
    ORDER BY h.changed_at ASC, h.id ASC
";

$stmt = $pdo->prepare($sql);
$stmt->execute([$cid]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
print_r($rows);
