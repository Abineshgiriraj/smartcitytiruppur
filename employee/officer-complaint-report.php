<?php
declare(strict_types=1);

/**
 * Officer Complaint Report — read-only, for authorized complaint managers.
 * Current workload comes from complaints.assigned_officer_id.
 * Previous handling comes from complaint_assignments, never from a guessed
 * officer name or a prospective escalation candidate.
 */
require_once __DIR__ . '/../includes/bootstrap.php';

$user = require_roles(['Super Admin','Admin','Commissioner','Department Head']);
require_permission('complaints.view');
$pdo = db();
$pageTitle = 'Officer Complaint Report';

function ocr_date(?string $value): string
{
    if (!$value) return '—';
    $time = strtotime($value);
    return $time === false ? '—' : date('d M Y, h:i A', $time);
}
function ocr_link(array $changes = []): string
{
    $parts = array_merge($_GET, $changes);
    foreach ($parts as $key=>$value) {
        if ($value === '' || $value === null) unset($parts[$key]);
    }
    unset($parts['export']);
    return url('employee/officer-complaint-report.php') . '?' . http_build_query($parts);
}
function ocr_csv_field($value): string
{
    $value = (string)($value ?? '');
    // Prevent spreadsheet formula injection, including leading control chars.
    return preg_match('/^\s*[=+@\-]/u', $value) ? "'".$value : $value;
}
function ocr_csv_download(string $filename, array $headers, array $rows): never
{
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="'.$filename.'"');
    header('X-Content-Type-Options: nosniff');
    header('Cache-Control: no-store, private');
    $stream=fopen('php://output','wb');
    if ($stream === false) exit;
    fwrite($stream,"\xEF\xBB\xBF");
    fputcsv($stream,$headers);
    foreach ($rows as $row) fputcsv($stream,array_map('ocr_csv_field',$row));
    fclose($stream);
    exit;
}
function ocr_query(PDO $pdo, string $sql, array $params): PDOStatement
{
    $stmt=$pdo->prepare($sql);
    foreach (array_values($params) as $i=>$value) {
        $stmt->bindValue($i+1,$value,is_int($value)?PDO::PARAM_INT:PDO::PARAM_STR);
    }
    $stmt->execute();
    return $stmt;
}

$scopeDepartment = $user['role']==='Department Head'
    ? (int)($user['department_id']??0) : null;
if ($scopeDepartment!==null && $scopeDepartment<1) {
    http_response_code(403);
    exit('Your account does not have a department scope.');
}
$search=trim((string)($_GET['q']??''));
$search=function_exists('mb_substr') ? mb_substr($search,0,75) : substr($search,0,75);
$departmentId=max(0,(int)($_GET['department_id']??0));
if ($scopeDepartment!==null) $departmentId=$scopeDepartment;
$zoneId=max(0,(int)($_GET['zone_id']??0));
$wardId=max(0,(int)($_GET['ward_id']??0));
$from=trim((string)($_GET['from_date']??''));
$to=trim((string)($_GET['to_date']??''));
if (($from!==''&&!valid_date($from))||($to!==''&&!valid_date($to))
    ||($from!==''&&$to!==''&&$from>$to)) {
    http_response_code(400);
    exit('Invalid report date range.');
}
$roster=(string)($_GET['roster']??'active');
if (!in_array($roster,['active','historical','all'],true)) $roster='active';
$workload=(string)($_GET['workload']??'all');
if (!in_array($workload,['all','open','overdue','none','previous'],true)) $workload='all';
$complaintStatus=trim((string)($_GET['status']??''));
if ($complaintStatus!==''&&!in_array($complaintStatus,complaint_statuses(),true)) {
    http_response_code(400);
    exit('Invalid complaint status.');
}
$officerId=max(0,(int)($_GET['officer_id']??0));
$view=(string)($_GET['view']??'current');
if (!in_array($view,['current','history'],true)) $view='current';
$pageParam=filter_var($_GET['page']??1,FILTER_VALIDATE_INT);
$page=max(1,min(100000,$pageParam===false?1:(int)$pageParam));
$perPage=25;
$export=(string)($_GET['export']??'');

$departments=$pdo->query('SELECT id,department_name FROM departments WHERE is_active=1 ORDER BY department_name')->fetchAll(PDO::FETCH_ASSOC);
$zones=$pdo->query('SELECT id,zone_no FROM zones WHERE is_active=1 ORDER BY zone_no')->fetchAll(PDO::FETCH_ASSOC);
$wardSql='SELECT w.id,w.ward_no,w.zone_id FROM wards w WHERE w.is_active=1';
$wardParams=[];
if ($zoneId>0) { $wardSql.=' AND w.zone_id=?'; $wardParams[]=$zoneId; }
$wardSql.=' ORDER BY CAST(w.ward_no AS UNSIGNED),w.ward_no';
$wards=ocr_query($pdo,$wardSql,$wardParams)->fetchAll(PDO::FETCH_ASSOC);

// All complaint metrics use precisely the same record/date/location scope.
$cWhere=['c.is_deleted=0'];
$cParams=[];
if ($departmentId>0) { $cWhere[]='c.department_id=?';$cParams[]=$departmentId; }
if ($zoneId>0) { $cWhere[]='c.zone_id=?';$cParams[]=$zoneId; }
if ($wardId>0) { $cWhere[]='c.ward_id=?';$cParams[]=$wardId; }
if ($from!=='') { $cWhere[]='c.created_at>=?';$cParams[]=$from.' 00:00:00'; }
if ($to!=='') { $cWhere[]='c.created_at<?';$cParams[]=(new DateTimeImmutable($to))->modify('+1 day')->format('Y-m-d').' 00:00:00'; }
if ($complaintStatus!=='') { $cWhere[]='c.status=?';$cParams[]=$complaintStatus; }
$cFilter=implode(' AND ',$cWhere);

// Group before joining officers to avoid multiplying an officer's counts by
// the number of departments, wards, or separate assignment-history rows.
$currentAgg="SELECT c.assigned_officer_id officer_id,
    COUNT(*) current_total,
    SUM(CASE WHEN c.status NOT IN ('Resolved','Closed','Rejected') THEN 1 ELSE 0 END) open_total,
    SUM(CASE WHEN c.status IN ('Resolved','Closed') THEN 1 ELSE 0 END) completed_total,
    SUM(CASE WHEN c.status NOT IN ('Resolved','Closed','Rejected') AND c.due_at IS NOT NULL AND c.due_at<NOW() THEN 1 ELSE 0 END) overdue_total
    FROM complaints c WHERE {$cFilter} AND c.assigned_officer_id IS NOT NULL
    GROUP BY c.assigned_officer_id";
$historyAgg="SELECT h.officer_id,
    COUNT(DISTINCT CASE WHEN c.assigned_officer_id IS NULL OR c.assigned_officer_id<>h.officer_id THEN c.id END) previously_handled,
    COUNT(DISTINCT CASE WHEN h.is_current=0 AND h.unassigned_at IS NOT NULL THEN c.id END) transferred_out,
    COUNT(DISTINCT CASE WHEN h.is_current=0 AND h.unassigned_at IS NOT NULL
      AND EXISTS (
         SELECT 1 FROM complaint_escalations ce
         WHERE ce.complaint_id=c.id AND ce.officer_id<>h.officer_id
           AND ABS(TIMESTAMPDIFF(SECOND,ce.escalated_at,h.unassigned_at))<=120
      ) THEN c.id END) escalated_out
    FROM complaint_assignments h JOIN complaints c ON c.id=h.complaint_id
    WHERE {$cFilter} GROUP BY h.officer_id";
$rosterActive="(o.is_active=1 AND EXISTS (
    SELECT 1 FROM officer_department_map m JOIN departments d ON d.id=m.department_id AND d.is_active=1
    WHERE m.officer_id=o.id))";
$rosterHistorical="(o.is_active=0 AND (
    EXISTS(SELECT 1 FROM complaints hx WHERE hx.assigned_officer_id=o.id AND hx.is_deleted=0)
    OR EXISTS(SELECT 1 FROM complaint_assignments hx WHERE hx.officer_id=o.id)))";
$rWhere=[];
if ($roster==='active') $rWhere[]=$rosterActive;
elseif ($roster==='historical') $rWhere[]=$rosterHistorical;
else $rWhere[]="({$rosterActive} OR {$rosterHistorical})";
$rParams=[];
// Limit the available officer identities to the selected or authorized dept.
// A head may view historical officers who handled one of that department's
// complaints even if their current department mapping has since been removed.
if ($departmentId>0) {
    $rWhere[]='(EXISTS(SELECT 1 FROM officer_department_map om WHERE om.officer_id=o.id AND om.department_id=?)
      OR EXISTS(SELECT 1 FROM complaints dc WHERE dc.assigned_officer_id=o.id AND dc.department_id=? AND dc.is_deleted=0)
      OR EXISTS(SELECT 1 FROM complaint_assignments da JOIN complaints dc ON dc.id=da.complaint_id
         WHERE da.officer_id=o.id AND dc.department_id=? AND dc.is_deleted=0))';
    array_push($rParams,$departmentId,$departmentId,$departmentId);
}
if ($search!=='') {
    $rWhere[]='(o.officer_name LIKE ? OR o.designation LIKE ? OR CAST(o.id AS CHAR)=?)';
    $like='%'.$search.'%';
    array_push($rParams,$like,$like,$search);
}
switch($workload) {
    case 'open': $rWhere[]='COALESCE(cur.open_total,0)>0';break;
    case 'overdue': $rWhere[]='COALESCE(cur.overdue_total,0)>0';break;
    case 'none': $rWhere[]='COALESCE(cur.current_total,0)=0';break;
    case 'previous': $rWhere[]='COALESCE(hist.previously_handled,0)>0';break;
}
$joins=" FROM officers o
    LEFT JOIN ({$currentAgg}) cur ON cur.officer_id=o.id
    LEFT JOIN ({$historyAgg}) hist ON hist.officer_id=o.id
    WHERE ".implode(' AND ',$rWhere);
$args=array_merge($cParams,$cParams,$rParams);
$stats=ocr_query($pdo,"SELECT COUNT(*) officers,
    COALESCE(SUM(cur.open_total),0) open_total,
    COALESCE(SUM(cur.current_total),0) current_total,
    COALESCE(SUM(cur.completed_total),0) completed_total,
    COALESCE(SUM(cur.overdue_total),0) overdue_total,
    COALESCE(SUM(hist.previously_handled),0) previous_total,
    COALESCE(SUM(hist.escalated_out),0) escalated_total".$joins,$args)->fetch(PDO::FETCH_ASSOC);
$totalOfficers=(int)($stats['officers']??0);
$pages=max(1,(int)ceil($totalOfficers/$perPage));
$page=min($page,$pages);

$rosterSelect="SELECT o.id,o.officer_name,o.designation,o.is_active,
    (SELECT GROUP_CONCAT(DISTINCT d.department_name ORDER BY d.department_name SEPARATOR ', ')
       FROM officer_department_map dm JOIN departments d ON d.id=dm.department_id
       WHERE dm.officer_id=o.id) departments,
    COALESCE(cur.current_total,0) current_total,
    COALESCE(cur.open_total,0) open_total,
    COALESCE(cur.completed_total,0) completed_total,
    COALESCE(cur.overdue_total,0) overdue_total,
    COALESCE(hist.previously_handled,0) previously_handled,
    COALESCE(hist.transferred_out,0) transferred_out,
    COALESCE(hist.escalated_out,0) escalated_out".$joins;
$rosterOrder=' ORDER BY o.is_active DESC,o.officer_name,o.id';
if($export==='csv' && $officerId===0) {
    $limit=5000;
    $all=ocr_query($pdo,$rosterSelect.$rosterOrder.' LIMIT '.$limit,$args)->fetchAll(PDO::FETCH_ASSOC);
    $data=[];
    foreach($all as $r) {
        $data[]=[
            $r['id'],$r['officer_name'],$r['designation'],$r['departments'],
            (int)$r['is_active']===1?'Active':'Inactive',
            $r['current_total'],$r['open_total'],$r['completed_total'],
            $r['overdue_total'],$r['previously_handled'],$r['transferred_out'],$r['escalated_out']
        ];
    }
    if($totalOfficers>$limit) $data[]=['Export capped at 5,000 officers; narrow your filters'];
    ocr_csv_download('officer_complaint_overview_'.date('Ymd_His').'.csv',
        ['Officer ID','Officer','Designation','Department','Roster Status','Currently Owned','Open','Completed','Overdue','Previously Handled','Transferred Out','Escalated Out'], $data);
}
$officers=ocr_query($pdo,$rosterSelect.$rosterOrder.' LIMIT '.$perPage.' OFFSET '.(($page-1)*$perPage),
    $args)->fetchAll(PDO::FETCH_ASSOC);

$selected=null;$detailRows=[];$detailTotal=0;$detailPages=1;
$detailSummary=['current_total'=>0,'open_total'=>0,'completed_total'=>0,'overdue_total'=>0,
    'previously_handled'=>0,'transferred_out'=>0,'escalated_out'=>0];
if($officerId>0) {
    // Officer lookup is separately scoped and independent of the overview's
    // search/workload filter. This also prevents crafted officer_id bypasses.
    $allowed="(o.is_active=1 AND EXISTS (
         SELECT 1 FROM officer_department_map dm WHERE dm.officer_id=o.id))
       OR EXISTS(SELECT 1 FROM complaint_assignments ah WHERE ah.officer_id=o.id)
       OR EXISTS(SELECT 1 FROM complaints ac WHERE ac.assigned_officer_id=o.id)";
    $selectedWhere=['o.id=?','('.$allowed.')'];
    $selectedArgs=[$officerId];
    if($departmentId>0) {
        $selectedWhere[]='(EXISTS(SELECT 1 FROM officer_department_map dm WHERE dm.officer_id=o.id AND dm.department_id=?)
           OR EXISTS(SELECT 1 FROM complaint_assignments ah JOIN complaints ac ON ac.id=ah.complaint_id
               WHERE ah.officer_id=o.id AND ac.department_id=? AND ac.is_deleted=0)
           OR EXISTS(SELECT 1 FROM complaints ac WHERE ac.assigned_officer_id=o.id
               AND ac.department_id=? AND ac.is_deleted=0))';
        array_push($selectedArgs,$departmentId,$departmentId,$departmentId);
    }
    $officerStmt=ocr_query($pdo,
        'SELECT o.id,o.officer_name,o.designation,o.is_active,
          (SELECT GROUP_CONCAT(DISTINCT d.department_name ORDER BY d.department_name SEPARATOR \', \')
            FROM officer_department_map dm JOIN departments d ON d.id=dm.department_id
            WHERE dm.officer_id=o.id) departments
         FROM officers o WHERE '.implode(' AND ',$selectedWhere).' LIMIT 1',
         $selectedArgs);
    $selected=$officerStmt->fetch(PDO::FETCH_ASSOC)?:null;
    if(!$selected) {
        http_response_code(404);
        exit('Officer not found or not available in your authorized scope.');
    }
    // Individual metrics match the overview's date, status, and area scope.
    $detailStatsSql="SELECT
        (SELECT COUNT(*) FROM complaints c WHERE {$cFilter} AND c.assigned_officer_id=?) current_total,
        (SELECT COUNT(*) FROM complaints c WHERE {$cFilter} AND c.assigned_officer_id=?
            AND c.status NOT IN ('Resolved','Closed','Rejected')) open_total,
        (SELECT COUNT(*) FROM complaints c WHERE {$cFilter} AND c.assigned_officer_id=?
            AND c.status IN ('Resolved','Closed')) completed_total,
        (SELECT COUNT(*) FROM complaints c WHERE {$cFilter} AND c.assigned_officer_id=?
            AND c.status NOT IN ('Resolved','Closed','Rejected') AND c.due_at IS NOT NULL AND c.due_at<NOW()) overdue_total,
        (SELECT COUNT(DISTINCT c.id) FROM complaint_assignments h JOIN complaints c ON c.id=h.complaint_id
           WHERE {$cFilter} AND h.officer_id=? AND (c.assigned_officer_id IS NULL OR c.assigned_officer_id<>h.officer_id)) previously_handled,
        (SELECT COUNT(DISTINCT c.id) FROM complaint_assignments h JOIN complaints c ON c.id=h.complaint_id
           WHERE {$cFilter} AND h.officer_id=? AND h.is_current=0 AND h.unassigned_at IS NOT NULL) transferred_out,
        (SELECT COUNT(DISTINCT c.id) FROM complaint_assignments h JOIN complaints c ON c.id=h.complaint_id
           WHERE {$cFilter} AND h.officer_id=? AND h.is_current=0 AND h.unassigned_at IS NOT NULL
             AND EXISTS(SELECT 1 FROM complaint_escalations ce WHERE ce.complaint_id=c.id AND ce.officer_id<>h.officer_id
               AND ABS(TIMESTAMPDIFF(SECOND,ce.escalated_at,h.unassigned_at))<=120)) escalated_out";
    $detailStatsArgs=[];
    for($i=0;$i<7;$i++){
        foreach($cParams as $param) $detailStatsArgs[]=$param;
        $detailStatsArgs[]=$officerId;
    }
    $detailSummary=ocr_query($pdo,$detailStatsSql,$detailStatsArgs)->fetch(PDO::FETCH_ASSOC)?:$detailSummary;

    // Current owner and the last recorded former owner belong to the
    // complaint, not to an inferred future escalation candidate.
    $handoverSelect=",
        (SELECT co.officer_name FROM officers co WHERE co.id=c.assigned_officer_id LIMIT 1) current_owner_name,
        (SELECT po.officer_name FROM complaint_assignments pa
         JOIN officers po ON po.id=pa.officer_id
         WHERE pa.complaint_id=c.id AND pa.is_current=0
         ORDER BY pa.unassigned_at DESC,pa.id DESC LIMIT 1) previous_owner_name";

    if($view==='current') {
        $detailFrom="FROM complaints c
          LEFT JOIN departments d ON d.id=c.department_id
          LEFT JOIN complaint_categories cat ON cat.id=c.category_id
          LEFT JOIN wards w ON w.id=c.ward_id
          LEFT JOIN zones z ON z.id=c.zone_id
          WHERE {$cFilter} AND c.assigned_officer_id=?";
        $detailArgs=[...$cParams,$officerId];
        $detailSelect="SELECT c.id,c.reference_no,c.created_at,c.status,c.due_at,
           d.department_name,cat.category_name,w.ward_no,z.zone_no,
           (SELECT MAX(h.assigned_at) FROM complaint_assignments h WHERE h.complaint_id=c.id AND h.officer_id=?
             AND h.is_current=1) handled_at,
           (SELECT COALESCE(MAX(ce.level_no),1) FROM complaint_escalations ce WHERE ce.complaint_id=c.id) last_stage,
           'Current' assignment_type {$handoverSelect} ";
        $detailSelectArgs=[$officerId];
        $detailOrder=' ORDER BY c.due_at IS NULL,c.due_at,c.created_at DESC,c.id DESC';
    } else {
        $historyQuery="(SELECT h.complaint_id,MIN(h.assigned_at) first_assigned,MAX(h.unassigned_at) last_unassigned
             FROM complaint_assignments h WHERE h.officer_id=? GROUP BY h.complaint_id) hist";
        $detailFrom="FROM {$historyQuery} JOIN complaints c ON c.id=hist.complaint_id
          LEFT JOIN departments d ON d.id=c.department_id
          LEFT JOIN complaint_categories cat ON cat.id=c.category_id
          LEFT JOIN wards w ON w.id=c.ward_id
          LEFT JOIN zones z ON z.id=c.zone_id
          WHERE {$cFilter} AND (c.assigned_officer_id IS NULL OR c.assigned_officer_id<>?)";
        $detailArgs=[$officerId,...$cParams,$officerId];
        $detailSelect="SELECT c.id,c.reference_no,c.created_at,c.status,c.due_at,
           d.department_name,cat.category_name,w.ward_no,z.zone_no,
           COALESCE(hist.last_unassigned,hist.first_assigned) handled_at,
           (SELECT COALESCE(MAX(ce.level_no),1) FROM complaint_escalations ce WHERE ce.complaint_id=c.id) last_stage,
           'Previous' assignment_type {$handoverSelect} ";
        $detailSelectArgs=[];
        $detailOrder=' ORDER BY handled_at DESC,c.id DESC';
    }
    $detailTotal=(int)ocr_query($pdo,'SELECT COUNT(*) '.$detailFrom,$detailArgs)->fetchColumn();
    $detailPages=max(1,(int)ceil($detailTotal/$perPage));
    $page=min($page,$detailPages);
    if($export==='csv') {
        $limit=5000;
        $data=ocr_query($pdo,$detailSelect.$detailFrom.$detailOrder.' LIMIT '.$limit,
            array_merge($detailSelectArgs,$detailArgs))->fetchAll(PDO::FETCH_ASSOC);
        $out=[];
        foreach($data as $r) $out[]=[
            $r['reference_no'],$r['department_name'],$r['category_name'],
            $r['zone_no'],$r['ward_no'],$r['status'],$r['assignment_type'],
            $r['handled_at'],$r['due_at'],$r['last_stage'],$r['current_owner_name'],$r['previous_owner_name'],$r['created_at']
        ];
        if($detailTotal>$limit) $out[]=['Export capped at 5,000 complaints; narrow your filters'];
        ocr_csv_download('officer_'.(int)$selected['id'].'_complaints_'.date('Ymd_His').'.csv',
            ['Reference','Department','Category','Zone','Ward','Status','Assignment','Assigned/Last handled','Due at','Last recorded escalation level','Current owner','Previous owner','Created'], $out);
    }
    $detailRows=ocr_query($pdo,$detailSelect.$detailFrom.$detailOrder.' LIMIT '.$perPage.' OFFSET '.(($page-1)*$perPage),
        array_merge($detailSelectArgs,$detailArgs))->fetchAll(PDO::FETCH_ASSOC);
}
include __DIR__.'/../includes/employee-header.php';
?>
<div class="ocr">
  <div class="admin-heading ocr-heading">
    <div><span class="admin-eyebrow">COMPLAINT MANAGEMENT / OFFICER REPORTS</span>
      <h1 class="fw-bold mb-1">Officer Complaint Report</h1>
      <p class="text-muted mb-0">Actual officer workloads and recorded complaint history, from your municipal database.</p>
    </div>
    <div class="ocr-heading-actions no-print">
      <?php if ($scopeDepartment===null): ?><a class="btn btn-outline-primary" href="<?=e(url('employee/legacy-officer-report.php'))?>">Historical officer report</a><?php endif; ?>
      <?php if($selected): ?><a class="btn btn-outline-primary" href="<?php echo e(ocr_link(['officer_id'=>null,'view'=>null,'page'=>1])); ?>"><i class="bi bi-arrow-left me-1"></i>All Officers</a><?php endif; ?>
      <a class="btn btn-outline-secondary" href="<?php echo e(ocr_link()); ?>" onclick="window.print();return false;"><i class="bi bi-printer me-1"></i>Print / Save PDF</a>
      <a class="btn btn-primary" href="<?php echo e(ocr_link()); ?><?php echo strpos(ocr_link(),'?')!==false?'&':'?'; ?>export=csv"><i class="bi bi-download me-1"></i>Export CSV</a>
    </div>
  </div>
  <div class="ocr-note no-print d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
      <i class="bi bi-shield-check me-2"></i><strong>Read-only.</strong>
      Current workload is counted by the complaint's current officer. Historical handling is counted from actual assignment records.
    </div>
    <div>
      <a href="routing-analysis.php" class="btn btn-sm btn-outline-primary">
        <i class="bi bi-diagram-3 me-1"></i>View Routing & Reassignment Analysis (24,592 Unassigned)
      </a>
    </div>
  </div>
  <div class="ocr-kpi-grid">
    <?php
    $cards=$selected?[
      ['Currently owned',(int)$detailSummary['current_total'],'bi-inboxes','Includes open and terminal statuses'],
      ['Open workload',(int)$detailSummary['open_total'],'bi-hourglass-split','Currently assigned and not terminal'],
      ['Completed',(int)$detailSummary['completed_total'],'bi-check2-circle','Resolved or closed, currently assigned'],
      ['Overdue',(int)$detailSummary['overdue_total'],'bi-clock-history','Open and past the current deadline']
    ]:[
      ['Officers',(int)$stats['officers'],'bi-people','Matching the selected roster'],
      ['Open workload',(int)$stats['open_total'],'bi-hourglass-split','Currently assigned open complaints'],
      ['Completed',(int)$stats['completed_total'],'bi-check2-circle','Current-owner resolved and closed'],
      ['Overdue',(int)$stats['overdue_total'],'bi-clock-history','Open cases beyond deadline']
    ];
    ?>
    <?php foreach($cards as $card): ?>
      <div class="ocr-kpi">
        <div class="ocr-kpi-label"><?php echo e($card[0]); ?><i class="bi <?php echo e($card[2]); ?>"></i></div>
        <strong><?php echo number_format($card[1]); ?></strong><small><?php echo e($card[3]); ?></small>
      </div>
    <?php endforeach; ?>
  </div>
  <form method="get" class="ocr-filters no-print" aria-label="Officer report filters">
    <?php if($selected): ?><input type="hidden" name="officer_id" value="<?php echo (int)$selected['id']; ?>">
      <input type="hidden" name="view" value="<?php echo e($view); ?>"><?php endif; ?>
    <?php if(!$selected): ?>
      <div class="ocr-field ocr-field-search"><label for="ocr-search">Officer name or ID</label>
        <input id="ocr-search" class="form-control" type="search" name="q" maxlength="75" value="<?php echo e($search); ?>" placeholder="Search officers"></div>
      <?php if($scopeDepartment===null): ?>
        <div class="ocr-field"><label for="ocr-department">Department</label>
        <select class="form-select" id="ocr-department" name="department_id">
          <option value="0">All departments</option>
          <?php foreach($departments as $d): ?><option value="<?php echo (int)$d['id']; ?>" <?php echo $departmentId===(int)$d['id']?'selected':''; ?>><?php echo e($d['department_name']); ?></option><?php endforeach; ?>
        </select></div>
      <?php endif; ?>
      <div class="ocr-field"><label for="ocr-roster">Officer roster</label>
        <select class="form-select" id="ocr-roster" name="roster">
          <option value="active" <?php echo $roster==='active'?'selected':''; ?>>Active department-mapped</option>
          <option value="historical" <?php echo $roster==='historical'?'selected':''; ?>>Inactive with history</option>
          <option value="all" <?php echo $roster==='all'?'selected':''; ?>>Active + historical</option>
        </select></div>
      <div class="ocr-field"><label for="ocr-workload">Workload</label>
        <select class="form-select" id="ocr-workload" name="workload">
          <?php foreach(['all'=>'All','open'=>'Has open cases','overdue'=>'Has overdue cases','none'=>'Zero currently owned','previous'=>'Previously handled'] as $key=>$label): ?>
          <option value="<?php echo e($key); ?>" <?php echo $workload===$key?'selected':''; ?>><?php echo e($label); ?></option>
          <?php endforeach; ?>
        </select></div>
    <?php endif; ?>
    <div class="ocr-field"><label for="ocr-zone">Zone</label><select class="form-select" id="ocr-zone" name="zone_id">
      <option value="0">All zones</option><?php foreach($zones as $z): ?><option value="<?php echo (int)$z['id']; ?>" <?php echo $zoneId===(int)$z['id']?'selected':''; ?>>Zone <?php echo e($z['zone_no']); ?></option><?php endforeach; ?>
    </select></div>
    <div class="ocr-field"><label for="ocr-ward">Ward</label><select class="form-select" id="ocr-ward" name="ward_id">
      <option value="0">All wards</option><?php foreach($wards as $w): ?><option value="<?php echo (int)$w['id']; ?>" <?php echo $wardId===(int)$w['id']?'selected':''; ?>>Ward <?php echo e($w['ward_no']); ?></option><?php endforeach; ?>
    </select></div>
    <div class="ocr-field"><label for="ocr-status">Complaint status</label><select class="form-select" id="ocr-status" name="status">
      <option value="">All statuses</option><?php foreach(complaint_statuses() as $statusName): ?>
      <option value="<?php echo e($statusName); ?>" <?php echo $complaintStatus===$statusName?'selected':''; ?>><?php echo e($statusName); ?></option><?php endforeach; ?>
    </select></div>
    <div class="ocr-field"><label for="ocr-from">Registered from</label><input class="form-control" id="ocr-from" type="date" name="from_date" value="<?php echo e($from); ?>"></div>
    <div class="ocr-field"><label for="ocr-to">Registered to</label><input class="form-control" id="ocr-to" type="date" name="to_date" value="<?php echo e($to); ?>"></div>
    <div class="ocr-filter-buttons"><button class="btn btn-primary" type="submit"><i class="bi bi-funnel me-1"></i>Apply</button>
      <a class="btn btn-outline-secondary" href="<?php echo e(url('employee/officer-complaint-report.php').($selected?'?officer_id='.(int)$selected['id']:'')); ?>">Reset</a></div>
    <p class="ocr-filter-note">Dates filter when the complaint was <strong>registered</strong>; current and historical counts use the same filters.</p>
  </form>

  <?php if(!$selected): ?>
  <section class="ocr-panel" aria-labelledby="ocr-summary-title">
    <div class="ocr-panel-heading"><div><h2 id="ocr-summary-title">All officer workloads</h2>
      <p><?php echo number_format($totalOfficers); ?> officers match · Page <?php echo $page; ?> / <?php echo $pages; ?> · Includes officers with zero complaints</p>
    </div></div>
    <div class="table-responsive">
      <table class="table table-hover align-middle ocr-table">
        <thead><tr><th>Officer</th><th>Department</th><th>Currently owned</th><th>Open</th><th>Completed</th><th>Overdue</th><th>Previously handled</th><th>Escalated out</th><th class="no-print">Report</th></tr></thead>
        <tbody>
        <?php if(!$officers): ?><tr><td colspan="9" class="text-center py-5 text-muted">No officers match these filters.</td></tr><?php endif; ?>
        <?php foreach($officers as $item): ?>
        <tr>
          <td><div class="ocr-officer-cell"><span class="ocr-avatar" aria-hidden="true"><i class="bi bi-person-badge"></i></span><div><strong><?php echo e($item['officer_name']); ?></strong><small>#<?php echo (int)$item['id']; ?> · <?php echo e($item['designation']?:'Designation not recorded'); ?></small>
            <?php if(!(int)$item['is_active']): ?><span class="ocr-inactive">Inactive / historical</span><?php endif; ?></div></div></td>
          <td><?php echo e($item['departments']?:'No current department mapping'); ?></td>
          <td><strong><?php echo (int)$item['current_total']; ?></strong></td>
          <td><?php echo (int)$item['open_total']; ?></td>
          <td><?php echo (int)$item['completed_total']; ?></td>
          <td><?php if((int)$item['overdue_total']>0): ?><span class="ocr-warn"><?php echo (int)$item['overdue_total']; ?></span><?php else: ?>0<?php endif; ?></td>
          <td><?php echo (int)$item['previously_handled']; ?></td>
          <td><?php echo (int)$item['escalated_out']; ?></td>
          <td class="no-print"><a class="btn btn-sm btn-outline-primary" href="<?php echo e(ocr_link(['officer_id'=>(int)$item['id'],'view'=>'current','page'=>1])); ?>">View <i class="bi bi-arrow-up-right"></i></a></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </section>
  <?php else: ?>
    <div class="ocr-profile ocr-panel">
      <div class="ocr-profile-main"><div class="ocr-profile-icon"><i class="bi bi-person-lines-fill"></i></div>
        <div><span class="ocr-profile-label">OFFICER PROFILE · #<?php echo (int)$selected['id']; ?></span>
          <h2><?php echo e($selected['officer_name']); ?></h2>
          <p><?php echo e($selected['designation']?:'Designation not recorded'); ?> · <?php echo e($selected['departments']?:'Department mapping unavailable'); ?></p></div>
      </div><span class="ocr-profile-status"><?php echo (int)$selected['is_active']===1?'Active':'Historical / inactive'; ?></span>
    </div>
    <div class="ocr-secondary-kpis">
      <span><strong><?php echo (int)$detailSummary['previously_handled']; ?></strong> Previously handled</span>
      <span><strong><?php echo (int)$detailSummary['transferred_out']; ?></strong> Transferred out</span>
      <span><strong><?php echo (int)$detailSummary['escalated_out']; ?></strong> Recorded escalations out</span>
    </div>
    <nav class="ocr-tabs no-print" aria-label="Officer complaint views">
      <a href="<?php echo e(ocr_link(['view'=>'current','page'=>1])); ?>" class="<?php echo $view==='current'?'active':''; ?>" <?php echo $view==='current'?'aria-current="page"':''; ?>>Current assignments <span><?php echo (int)$detailSummary['current_total']; ?></span></a>
      <a href="<?php echo e(ocr_link(['view'=>'history','page'=>1])); ?>" class="<?php echo $view==='history'?'active':''; ?>" <?php echo $view==='history'?'aria-current="page"':''; ?>>Previously handled <span><?php echo (int)$detailSummary['previously_handled']; ?></span></a>
    </nav>
    <section class="ocr-panel" aria-label="Complaint list">
      <div class="ocr-panel-heading"><div><h2><?php echo $view==='current'?'Complaints currently owned':'Previously handled complaints'; ?></h2>
        <p><?php echo number_format($detailTotal); ?> distinct complaints · Page <?php echo $page; ?> / <?php echo $detailPages; ?></p>
      </div></div>
      <div class="table-responsive">
        <table class="table table-hover align-middle ocr-table">
          <thead><tr><th>Complaint reference</th><th>Department / category</th><th>Zone / ward</th><th>Complaint status</th><th><?php echo $view==='current'?'Assigned at':'Last handled'; ?></th><th>Deadline</th><th>Last recorded stage</th><th>Ownership</th><th class="no-print">Actions</th></tr></thead>
          <tbody>
          <?php if(!$detailRows): ?><tr><td colspan="9" class="text-center py-5 text-muted">No complaints in this view for the selected filters.</td></tr><?php endif; ?>
          <?php foreach($detailRows as $case): ?>
            <tr>
              <td><strong><?php echo e($case['reference_no']?:('#'.$case['id'])); ?></strong><small class="d-block text-muted">Registered <?php echo e(ocr_date($case['created_at'])); ?></small></td>
              <td><?php echo e($case['department_name']?:'Unmapped'); ?><small class="d-block text-muted"><?php echo e($case['category_name']?:'Unmapped'); ?></small></td>
              <td>Zone <?php echo e($case['zone_no']??'—'); ?> / Ward <?php echo e($case['ward_no']??'—'); ?></td>
              <td><span class="ocr-status"><?php echo e($case['status']); ?></span></td>
              <td><?php echo e(ocr_date($case['handled_at'])); ?></td>
              <td><?php echo e(ocr_date($case['due_at'])); ?></td>
              <td>L<?php echo (int)$case['last_stage']; ?></td>
              <td><strong><?php echo e($case['current_owner_name']?:'Unassigned'); ?></strong>
                <?php if($case['previous_owner_name']): ?><small class="d-block text-muted">From <?php echo e($case['previous_owner_name']); ?></small><?php endif; ?>
                <small class="d-block text-muted"><?php echo $view==='current'?'Current responsibility':'Transferred / previously handled'; ?></small>
              </td>
              <td class="ocr-actions no-print">
                <a class="btn btn-sm btn-outline-primary" href="<?php echo e(url('employee/complaint-view.php?id='.(int)$case['id'])); ?>" aria-label="View complaint <?php echo e($case['reference_no']?:('#'.$case['id'])); ?>">Complaint</a>
                <a class="btn btn-sm btn-outline-secondary" href="<?php echo e(url('employee/escalation-reports.php?tab=journey&complaint_id='.(int)$case['id'])); ?>">Escalation</a>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>
  <?php endif; ?>

  <?php $totalPages=$selected?$detailPages:$pages; ?>
  <?php if($totalPages>1): ?>
  <nav class="ocr-pagination no-print" aria-label="Report pagination">
    <?php if($page>1): ?><a class="btn btn-outline-secondary btn-sm" href="<?php echo e(ocr_link(['page'=>$page-1])); ?>">Previous</a><?php endif; ?>
    <span>Page <?php echo $page; ?> of <?php echo $totalPages; ?></span>
    <?php if($page<$totalPages): ?><a class="btn btn-outline-secondary btn-sm" href="<?php echo e(ocr_link(['page'=>$page+1])); ?>">Next</a><?php endif; ?>
  </nav>
  <?php endif; ?>
  <p class="ocr-footnote">Current counts reflect each complaint's <strong>current</strong> assigned officer. Previous handling and escalations use recorded history. A complaint may appear in multiple officers' histories, but only once in the current workload totals. A future eligible escalation officer is not an assignment.</p>
</div>
<?php include __DIR__.'/../includes/employee-footer.php'; ?>
