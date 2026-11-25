<?php
declare(strict_types=1);

/**
 * Read-only PDF escalation reporting. Never executes, schedules or modifies routing.
 * Actual transfers come from complaint_assignments + complaint_escalations;
 * upcoming recipients are tentative eligibility previews, not assignments.
 */
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/officer-authority.php';
require_once __DIR__ . '/../includes/escalation-report-checks.php';

$user = require_roles(['Super Admin', 'Admin', 'Commissioner', 'Department Head']);
require_permission('escalation.view');
$pageTitle = 'Escalation Reports';
$pdo = db();
$departmentScope = $user['role'] === 'Department Head' ? (int)($user['department_id'] ?? 0) : null;
$tab = (string)($_GET['tab'] ?? 'overview');
if (!in_array($tab, ['overview', 'journey', 'stages'], true)) $tab = 'overview';
$query = trim((string)($_GET['q'] ?? ''));
$query = substr($query, 0, 70);
$department = max(0, (int)($_GET['department_id'] ?? 0));
if ($departmentScope !== null) $department = $departmentScope;
$zone = max(0, (int)($_GET['zone_id'] ?? 0));
$stage = max(0, min(6, (int)($_GET['stage'] ?? 0)));
$filter = (string)($_GET['filter'] ?? 'all');
if (!in_array($filter, ['all', 'open', 'overdue', 'escalated', 'unassigned', 'completed'], true)) $filter = 'all';
$from = (string)($_GET['from'] ?? '');
$to = (string)($_GET['to'] ?? '');
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/D', $from) || !strtotime($from)) $from = '';
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/D', $to) || !strtotime($to)) $to = '';
$page = max(1, min(2000, (int)($_GET['page'] ?? 1)));
$perPage = 25;
$openSql = "c.status NOT IN ('Resolved','Closed','Rejected')";
$stageSql = '(SELECT COALESCE(MAX(ce.level_no),1) FROM complaint_escalations ce WHERE ce.complaint_id=c.id)';

function er_link(array $changes = []): string
{
    $values = array_merge($_GET, $changes);
    foreach ($values as $key => $value) {
        if ($value === null || $value === '') unset($values[$key]);
    }
    return url('employee/escalation-reports.php') . '?' . http_build_query($values);
}
function er_csv_safe($value): string
{
    $value = (string)($value ?? '');
    return preg_match('/^\s*[=+\-@\t\r]/u', $value) ? "'" . $value : $value;
}
function er_date($value): string
{
    if (!$value) return '—';
    $time = strtotime((string)$value);
    return $time === false ? '—' : date('d M Y, h:i A', $time);
}
function er_status_label(array $complaint): string
{
    if (in_array((string)$complaint['status'], ['Resolved','Closed','Rejected'], true)) return 'Completed / terminal';
    if (!$complaint['assigned_officer_id']) return 'Awaiting allocation';
    if (!empty($complaint['due_at']) && strtotime((string)$complaint['due_at']) < time()) return 'Past stage deadline';
    return 'Within stage SLA';
}
function er_next_preview(PDO $pdo, array $c, array $policy, int $level, bool $consistent): array
{
    if (!$consistent) return ['label'=>'History or assignment needs review', 'candidate'=>null];
    if (in_array((string)$c['status'], ['Closed','Rejected','Resolved'], true))
        return ['label'=>'Complaint is completed', 'candidate'=>null];
    if (!$c['assigned_officer_id']) return ['label'=>'Initial allocation pending', 'candidate'=>null];
    if ($level >= (int)$policy['stage_count']) return ['label'=>'Final PDF stage reached', 'candidate'=>null];
    $current = pdf_policy_stage($policy, $level);
    $next = pdf_policy_stage($policy, $level + 1);
    if (!$current || !$next) return ['label'=>'PDF stage missing', 'candidate'=>null];
    try {
        $candidates = array_values(array_filter(
            pdf_routing_candidates($pdo,(int)$c['category_id'],$level+1,
                (int)$c['department_id'],(int)$c['ward_id']),
            static fn(array $o):bool => (int)$o['id'] !== (int)$c['assigned_officer_id']
        ));
        $exact=$pdo->prepare(
            'SELECT officer_id,escalation_days FROM department_allocations
             WHERE department_id=? AND category_id=? AND zone_id=? AND ward_id=?
               AND stage_no=? AND is_active=1 LIMIT 1'
        );
        $exact->execute([
            (int)$c['department_id'],(int)$c['category_id'],(int)$c['zone_id'],
            (int)$c['ward_id'],$level+1
        ]);
        $rule=$exact->fetch(PDO::FETCH_ASSOC);
        if ($rule) {
            if ((int)$rule['escalation_days'] !== (int)$next['period_days'])
                return ['label'=>'Exact allocation rule conflicts with PDF SLA', 'candidate'=>null];
            $candidates=array_values(array_filter($candidates,
                static fn(array $o):bool=>(int)$o['id']===(int)$rule['officer_id']));
            if (count($candidates)!==1)
                return ['label'=>'Exact next-stage officer is ineligible', 'candidate'=>null];
            $pick=$candidates[0];
        } else {
            if (!$candidates) return ['label'=>'No eligible next-stage officer mapped', 'candidate'=>null];
            $rank=(int)$candidates[0]['ward_rank'];
            $candidates=array_values(array_filter($candidates,
                static fn(array $o):bool=>(int)$o['ward_rank']===$rank));
            $pick=pdf_pick_least_loaded_officer($pdo,$candidates);
        }
        if (!$pick) return ['label'=>'No eligible next-stage officer mapped', 'candidate'=>null];
        return ['label'=>'Tentative eligible candidate (not assigned)',
            'candidate'=>$pick,'count'=>count($candidates)];
    } catch (Throwable $error) {
        log_message('Escalation report preview failed: '.$error->getMessage());
        return ['label'=>'Eligibility preview unavailable', 'candidate'=>null];
    }
}

$departments = $pdo->query(
    'SELECT id,department_name FROM departments WHERE is_active=1 ORDER BY department_name'
)->fetchAll(PDO::FETCH_ASSOC);
$zones = $pdo->query(
    'SELECT id,zone_no FROM zones WHERE is_active=1 ORDER BY zone_no'
)->fetchAll(PDO::FETCH_ASSOC);

$where=['c.is_deleted=0'];
$params=[];
if ($department > 0) {$where[]='c.department_id=?';$params[]=$department;}
if ($zone > 0) {$where[]='c.zone_id=?';$params[]=$zone;}
if ($query !== '') {
    $where[]='(c.reference_no LIKE ? OR d.department_name LIKE ? OR cc.category_name LIKE ?)';
    $like='%'.$query.'%';array_push($params,$like,$like,$like);
}
if ($from !== '') {$where[]='c.created_at>=?';$params[]=$from.' 00:00:00';}
if ($to !== '') {$where[]='c.created_at<=?';$params[]=$to.' 23:59:59';}
if ($stage > 0) {$where[]=$stageSql.'=?';$params[]=$stage;}
switch ($filter) {
    case 'open':$where[]=$openSql;break;
    case 'overdue':$where[]=$openSql.' AND c.due_at<NOW() AND c.assigned_officer_id IS NOT NULL';break;
    case 'escalated':$where[]='EXISTS (SELECT 1 FROM complaint_escalations ce WHERE ce.complaint_id=c.id)';break;
    case 'unassigned':$where[]=$openSql.' AND c.assigned_officer_id IS NULL';break;
    case 'completed':$where[]="c.status IN ('Resolved','Closed','Rejected')";break;
}
$base=' FROM complaints c
    JOIN departments d ON d.id=c.department_id
    JOIN complaint_categories cc ON cc.id=c.category_id
    JOIN zones z ON z.id=c.zone_id
    LEFT JOIN officers o ON o.id=c.assigned_officer_id
    WHERE '.implode(' AND ',$where);
$totalStmt=$pdo->prepare('SELECT COUNT(*)'.$base);
$totalStmt->execute($params);
$total=(int)$totalStmt->fetchColumn();
$pages=max(1,(int)ceil($total/$perPage));
$page=min($page,$pages);

$statement="SELECT c.id,c.reference_no,c.status,c.created_at,c.due_at,c.assigned_officer_id,
    c.department_id,c.category_id,c.zone_id,c.ward_id,
    d.department_name,cc.category_name,z.zone_no,o.officer_name,
    {$stageSql} AS current_stage".$base.' ORDER BY c.created_at DESC,c.id DESC';
$loadRows=static function(int $limit,int $offset) use ($pdo,$statement,$params):array {
    $stmt=$pdo->prepare($statement.' LIMIT ? OFFSET ?');
    $index=1;
    foreach ($params as $value) $stmt->bindValue($index++,$value);
    $stmt->bindValue($index++,$limit,PDO::PARAM_INT);
    $stmt->bindValue($index,$offset,PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
};

if (($_GET['export'] ?? '')==='csv' && $tab==='overview') {
    $exportLimit=5000;
    $rows=$loadRows($exportLimit,0);
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="tiruppur_escalation_report_'.date('Ymd').'.csv"');
    header('X-Content-Type-Options: nosniff');
    header('Cache-Control: no-store');
    $out=fopen('php://output','wb');
    if($out===false)exit;
    fwrite($out,"\xEF\xBB\xBF");
    fputcsv($out,['Reference','Department','Category','Zone','Status','Current PDF Level',
        'Current Officer','Stage Deadline','Report Status','Created At']);
    foreach ($rows as $r) fputcsv($out,array_map('er_csv_safe',[
        $r['reference_no'],$r['department_name'],$r['category_name'],$r['zone_no'],$r['status'],
        $r['assigned_officer_id']?$r['current_stage']:'Not allocated',
        $r['officer_name']?:'Unassigned',$r['due_at'],er_status_label($r),$r['created_at']
    ]));
    if ($total>$exportLimit) fputcsv($out,['Export limited to first '.$exportLimit.' matching complaints']);
    fclose($out);
    exit;
}

$rows=$tab==='overview'?$loadRows($perPage,($page-1)*$perPage):[];
$summaryWhere=['c.is_deleted=0'];
$summaryParams=[];
if ($department>0){$summaryWhere[]='c.department_id=?';$summaryParams[]=$department;}
if ($from!==''){$summaryWhere[]='c.created_at>=?';$summaryParams[]=$from.' 00:00:00';}
if ($to!==''){$summaryWhere[]='c.created_at<=?';$summaryParams[]=$to.' 23:59:59';}
$summaryStmt=$pdo->prepare("SELECT COUNT(*) AS total,
 COALESCE(SUM({$openSql} AND c.assigned_officer_id IS NOT NULL AND c.due_at<NOW()),0) AS overdue,
 COALESCE(SUM({$openSql} AND c.assigned_officer_id IS NULL),0) AS unassigned,
 COALESCE(SUM(EXISTS(SELECT 1 FROM complaint_escalations ce WHERE ce.complaint_id=c.id)),0) AS escalated
 FROM complaints c WHERE ".implode(' AND ',$summaryWhere));
$summaryStmt->execute($summaryParams);
$summary=$summaryStmt->fetch(PDO::FETCH_ASSOC)?:['total'=>0,'overdue'=>0,'unassigned'=>0,'escalated'=>0];

$selected=null;$events=[];$assignments=[];$stageRows=[];$policy=null;$currentLevel=1;$stagePeople=[];
$stageIssue='';$nextPreview=['label'=>'No complaint selected','candidate'=>null];
$stageOfficerPreviews=[];$routingReadiness=null;
$complaintId=max(0,(int)($_GET['complaint_id']??0));
$referenceSearch=trim((string)($_GET['reference_no']??''));
$referenceSearch=substr($referenceSearch,0,45);
if ($tab==='journey' && $referenceSearch!=='' && $complaintId===0) {
    $resolveSql='SELECT id FROM complaints WHERE reference_no=? AND is_deleted=0';
    $resolveArgs=[$referenceSearch];
    if ($departmentScope!==null) {
        $resolveSql.=' AND department_id=?';
        $resolveArgs[]=$departmentScope;
    }
    $resolveSql.=' LIMIT 1';
    $resolve=$pdo->prepare($resolveSql);
    $resolve->execute($resolveArgs);
    $complaintId=(int)$resolve->fetchColumn();
}
if ($tab==='journey' && $complaintId>0) {
    $detailSql='SELECT c.*,d.department_name,cc.category_name,z.zone_no,
           o.officer_name AS current_officer_name,o.designation AS current_designation
         FROM complaints c
         JOIN departments d ON d.id=c.department_id
         JOIN complaint_categories cc ON cc.id=c.category_id
         JOIN zones z ON z.id=c.zone_id
         LEFT JOIN officers o ON o.id=c.assigned_officer_id
         WHERE c.id=? AND c.is_deleted=0';
    $detailArgs=[$complaintId];
    if($departmentScope!==null){$detailSql.=' AND c.department_id=?';$detailArgs[]=$departmentScope;}
    $detailStmt=$pdo->prepare($detailSql);
    $detailStmt->execute($detailArgs);
    $selected=$detailStmt->fetch(PDO::FETCH_ASSOC)?:null;
    if($selected) {
        $history=$pdo->prepare(
            'SELECT ce.id,ce.level_no,ce.officer_id,ce.escalated_at,o.officer_name,o.designation
             FROM complaint_escalations ce
             LEFT JOIN officers o ON o.id=ce.officer_id
             WHERE ce.complaint_id=? ORDER BY ce.escalated_at,ce.id'
        );
        $history->execute([$complaintId]);
        $events=$history->fetchAll(PDO::FETCH_ASSOC);
        $assignmentStmt=$pdo->prepare(
            'SELECT ca.id,ca.officer_id,ca.assigned_at,ca.unassigned_at,ca.is_current,
                    o.officer_name,o.designation
             FROM complaint_assignments ca
             LEFT JOIN officers o ON o.id=ca.officer_id
             WHERE ca.complaint_id=? ORDER BY ca.assigned_at,ca.id'
        );
        $assignmentStmt->execute([$complaintId]);
        $assignments=$assignmentStmt->fetchAll(PDO::FETCH_ASSOC);
        $policy=pdf_policy_routing_ready($pdo,(int)$selected['category_id']);
        $rawStage=$pdo->prepare(
            'SELECT stage_no,role_label,period_days
             FROM pdf_escalation_policy_stages WHERE category_id=? ORDER BY stage_no'
        );
        $rawStage->execute([(int)$selected['category_id']]);
        $stageRows=$policy['stages']??$rawStage->fetchAll(PDO::FETCH_ASSOC);
        $initial=$assignments[0]??null;
        if($initial) $stagePeople[1]=$initial;
        foreach ($events as $event) {
            $n=(int)$event['level_no'];
            if($n>1 && $n<=6)$stagePeople[$n]=$event;
            $currentLevel=max($currentLevel,$n);
        }
        $consistent=true;
        if (!$policy) { $stageIssue='PDF routing policy missing or invalid. Showing saved stage rows only.';$consistent=false; }
        if ((int)$selected['assigned_officer_id']===0) {$stageIssue='No current officer is assigned.';$consistent=false;}
        if($events && (int)end($events)['officer_id'] !== (int)$selected['assigned_officer_id']){
            $stageIssue='The latest escalation recipient differs from the current assigned officer. Review manual reassignment history.';
            $consistent=false;
        }
        $activeAssignments=array_values(array_filter($assignments,
            static fn(array $a):bool=>(int)$a['is_current']===1));
        if(count($activeAssignments)!==1 ||
           (int)($activeAssignments[0]['officer_id']??0)!==(int)$selected['assigned_officer_id']){
            $stageIssue='Current officer and assignment history do not match. Routing needs review.';
            $consistent=false;
        }
        if($consistent && $policy && !in_array($selected['status'],['Closed','Rejected','Resolved'],true)) {
            $currentStep=pdf_policy_stage($policy,$currentLevel);
            $started=$events?(string)end($events)['escalated_at']:(string)($initial['assigned_at']??'');
            if(!$currentStep || !$started || !$selected['due_at']) {
                $stageIssue='Current PDF SLA deadline cannot be verified.';$consistent=false;
            } else {
                try {
                    $expected=(new DateTimeImmutable($started))->modify('+'.(int)$currentStep['period_days'].' days');
                    $due=new DateTimeImmutable((string)$selected['due_at']);
                    if(abs($expected->getTimestamp()-$due->getTimestamp())>120){
                        $stageIssue='Stored stage deadline differs from PDF SLA. Escalation worker requires review.';$consistent=false;
                    }
                } catch(Throwable $e) {
                    $stageIssue='Current PDF deadline is invalid.';$consistent=false;
                }
            }
        }
        if($policy) {
            $nextPreview=er_next_preview($pdo,$selected,$policy,$currentLevel,$consistent);
            // Preview every remaining PDF stage, not only the immediate successor.
            // Predictions are recomputed; no future recipient is already assigned.
            $previousCandidate=(int)$selected['assigned_officer_id'];
            foreach($stageRows as $step){
                $futureLevel=(int)$step['stage_no'];
                if($futureLevel<=$currentLevel)continue;
                $preview=er_stage_officer_preview(
                    $pdo,$selected,$policy,$futureLevel,$previousCandidate
                );
                $stageOfficerPreviews[$futureLevel]=$preview;
                if($preview['candidate'])$previousCandidate=(int)$preview['candidate']['id'];
            }
            // Use same validated immediate-next preview as the routing worker.
            // This may differ from speculative later-stage candidates.
            if(isset($stageOfficerPreviews[$currentLevel+1]) && $consistent) {
                $nextPreview=$stageOfficerPreviews[$currentLevel+1];
            }
        }
        $routingReadiness=er_escalation_readiness(
            $pdo,$selected,$policy,$currentLevel,$events,$assignments,
            $stageOfficerPreviews[$currentLevel+1]??$nextPreview
        );
    }
}

$categoryRows=[];$categoryPages=1;$categoryTotal=0;
if($tab==='stages') {
    $parts=['cc.is_active=1'];$args=[];
    if($department>0){$parts[]='cc.department_id=?';$args[]=$department;}
    if($query!==''){$parts[]='(cc.category_name LIKE ? OR d.department_name LIKE ?)';$like='%'.$query.'%';$args[]=$like;$args[]=$like;}
    $fromCategory=' FROM complaint_categories cc JOIN departments d ON d.id=cc.department_id
           LEFT JOIN pdf_escalation_policies p ON p.category_id=cc.id WHERE '.implode(' AND ',$parts);
    $stmt=$pdo->prepare('SELECT COUNT(*)'.$fromCategory);
    $stmt->execute($args);$categoryTotal=(int)$stmt->fetchColumn();
    $categoryPages=max(1,(int)ceil($categoryTotal/$perPage));$page=min($page,$categoryPages);
    $stmt=$pdo->prepare('SELECT cc.id,cc.category_name,d.department_name,p.stage_count,p.total_days'.$fromCategory.' ORDER BY d.department_name,cc.category_name LIMIT ? OFFSET ?');
    $i=1;foreach($args as $v)$stmt->bindValue($i++,$v);
    $stmt->bindValue($i++,$perPage,PDO::PARAM_INT);
    $stmt->bindValue($i,($page-1)*$perPage,PDO::PARAM_INT);
    $stmt->execute();$categoryRows=$stmt->fetchAll(PDO::FETCH_ASSOC);
    $rawStageStmt=$pdo->prepare(
        'SELECT stage_no,role_label,period_days FROM pdf_escalation_policy_stages
         WHERE category_id=? ORDER BY stage_no'
    );
    foreach($categoryRows as &$r){
        $p=pdf_policy_routing_ready($pdo,(int)$r['id']);
        if ($p) {
            $r['stages']=$p['stages'];
        } else {
            // Still display recorded rows, clearly marked as NOT routable;
            // this helps the administrator identify incomplete PDF policies.
            $rawStageStmt->execute([(int)$r['id']]);
            $r['stages']=$rawStageStmt->fetchAll(PDO::FETCH_ASSOC);
        }
        $r['ready']=$p!==null;
    }
    unset($r);
}
include __DIR__.'/../includes/employee-header.php';
?>
<div class="er">
  <div class="admin-heading er-heading">
    <div>
      <span class="admin-eyebrow">Complaint Management / PDF Escalation</span>
      <h1 class="fw-bold mb-1">Escalation Reports</h1>
      <p>Follow every assigned stage, recorded officer transfer, and the next PDF responsibility.</p>
    </div>
    <div class="er-heading-actions">
      <a class="btn btn-outline-primary" href="<?php echo e(url('employee/escalation.php')); ?>"><i class="bi bi-diagram-3 me-1"></i> Monitor</a>
      <?php if($tab==='overview'): ?><a class="btn btn-primary" href="<?php echo e(er_link(['export'=>'csv','page'=>null])); ?>"><i class="bi bi-file-earmark-arrow-down me-1"></i> CSV Export</a><?php endif; ?>
    </div>
  </div>
  <div class="alert alert-info er-notice"><i class="bi bi-shield-check me-2"></i><strong>Read-only report.</strong> Historical transfers are recorded facts; upcoming officer names are eligibility previews and may change when escalation actually runs. This page never performs escalation.</div>
  <div class="er-kpis">
    <div class="er-kpi"><div class="er-kpi-top">Escalated complaints <i class="bi bi-arrow-up-right"></i></div><strong><?php echo number_format((int)$summary['escalated']); ?></strong><small>At least one recorded transfer</small></div>
    <div class="er-kpi"><div class="er-kpi-top">Overdue open <i class="bi bi-clock-history"></i></div><strong><?php echo number_format((int)$summary['overdue']); ?></strong><small>Past recorded deadline</small></div>
    <div class="er-kpi"><div class="er-kpi-top">Awaiting allocation <i class="bi bi-person-exclamation"></i></div><strong><?php echo number_format((int)$summary['unassigned']); ?></strong><small>Open complaints without an officer</small></div>
    <div class="er-kpi"><div class="er-kpi-top">Total complaints <i class="bi bi-inboxes"></i></div><strong><?php echo number_format((int)$summary['total']); ?></strong><small>Department and date scope</small></div>
  </div>
  <nav class="er-tabs" aria-label="Escalation report views">
    <?php foreach (['overview'=>'Overview','journey'=>'Complaint Journey','stages'=>'Stage Report'] as $key=>$name): ?>
      <a class="<?php echo $tab===$key?'is-current':''; ?>" href="<?php echo e(er_link(['tab'=>$key,'page'=>1,'export'=>null])); ?>"<?php echo $tab===$key?' aria-current="page"':''; ?>><?php echo e($name); ?></a>
    <?php endforeach; ?>
  </nav>
  <?php if($tab!=='journey'): ?>
    <form class="er-filters" method="get">
      <input type="hidden" name="tab" value="<?php echo e($tab); ?>">
      <div class="er-filter-search"><label for="er-search">Search</label><input class="form-control" id="er-search" type="search" name="q" maxlength="70" placeholder="Reference, department, category" value="<?php echo e($query); ?>"></div>
      <?php if($departmentScope===null): ?>
      <div><label for="er-dept">Department</label><select class="form-select" id="er-dept" name="department_id"><option value="0">All departments</option><?php foreach($departments as $d): ?><option value="<?php echo (int)$d['id']; ?>" <?php echo $department===(int)$d['id']?'selected':''; ?>><?php echo e($d['department_name']); ?></option><?php endforeach; ?></select></div>
      <?php endif; ?>
      <?php if($tab==='overview'): ?>
      <div><label for="er-zone">Zone</label><select class="form-select" id="er-zone" name="zone_id"><option value="0">All zones</option><?php foreach($zones as $z): ?><option value="<?php echo (int)$z['id']; ?>" <?php echo $zone===(int)$z['id']?'selected':''; ?>>Zone <?php echo e($z['zone_no']); ?></option><?php endforeach; ?></select></div>
      <div><label for="er-stage">Current stage</label><select class="form-select" id="er-stage" name="stage"><option value="0">All stages</option><?php for($s=1;$s<=6;$s++): ?><option value="<?php echo $s; ?>" <?php echo $stage===$s?'selected':''; ?>>Stage <?php echo $s; ?></option><?php endfor; ?></select></div>
      <div><label for="er-state">Report status</label><select class="form-select" id="er-state" name="filter"><?php foreach(['all'=>'All','open'=>'Open','overdue'=>'Overdue','escalated'=>'Escalated','unassigned'=>'Unassigned','completed'=>'Completed / terminal'] as $key=>$title): ?><option value="<?php echo e($key); ?>" <?php echo $filter===$key?'selected':''; ?>><?php echo e($title); ?></option><?php endforeach; ?></select></div>
      <div><label for="er-from">From</label><input class="form-control" id="er-from" type="date" name="from" value="<?php echo e($from); ?>"></div>
      <div><label for="er-to">To</label><input class="form-control" id="er-to" type="date" name="to" value="<?php echo e($to); ?>"></div>
      <?php endif; ?>
      <div class="er-filter-actions"><button class="btn btn-primary" type="submit"><i class="bi bi-funnel me-1"></i> Apply</button><a class="btn btn-outline-secondary" href="<?php echo e(url('employee/escalation-reports.php?tab='.$tab)); ?>">Reset</a></div>
    </form>
  <?php endif; ?>
  <?php if($tab==='overview'): ?>
    <div class="er-card">
      <div class="er-card-head"><div><h2>Complaint escalation overview</h2><p><?php echo number_format($total); ?> matching complaints · page <?php echo $page; ?> of <?php echo $pages; ?></p></div></div>
      <div class="table-responsive"><table class="table table-hover align-middle er-table">
        <thead><tr><th>Complaint</th><th>Department / category</th><th>Current stage</th><th>Assigned officer</th><th>Due date</th><th>Report status</th><th>Details</th></tr></thead>
        <tbody>
        <?php if(!$rows): ?><tr><td colspan="7" class="text-center text-secondary py-5">No matching complaints found.</td></tr><?php endif; ?>
        <?php foreach($rows as $c): ?>
          <tr><td><strong><?php echo e($c['reference_no']??('#'.$c['id'])); ?></strong><small class="d-block text-secondary"><?php echo e(er_date($c['created_at'])); ?></small></td>
          <td><?php echo e($c['department_name']); ?><small class="d-block text-secondary"><?php echo e($c['category_name']); ?> · Zone <?php echo e($c['zone_no']); ?></small></td>
          <td><?php echo $c['assigned_officer_id']?'Stage '.(int)$c['current_stage']:'Not assigned'; ?></td>
          <td><?php echo e($c['officer_name']?:'Unassigned'); ?></td>
          <td><?php echo e(er_date($c['due_at'])); ?></td>
          <td><span class="er-state"><?php echo e(er_status_label($c)); ?></span><small class="d-block text-secondary"><?php echo e($c['status']); ?></small></td>
          <td><a class="btn btn-sm btn-outline-primary" href="<?php echo e(er_link(['tab'=>'journey','complaint_id'=>(int)$c['id'],'page'=>1])); ?>">View journey</a></td></tr>
        <?php endforeach; ?>
        </tbody></table></div>
    </div>
    <?php if($pages>1): ?><div class="er-pages"><a class="btn btn-sm btn-outline-secondary <?php echo $page<=1?'disabled':''; ?>" href="<?php echo e(er_link(['page'=>max(1,$page-1)])); ?>">Previous</a><span>Page <?php echo $page; ?> / <?php echo $pages; ?></span><a class="btn btn-sm btn-outline-secondary <?php echo $page>=$pages?'disabled':''; ?>" href="<?php echo e(er_link(['page'=>min($pages,$page+1)])); ?>">Next</a></div><?php endif; ?>
  <?php elseif($tab==='journey'): ?>
    <form class="er-journey-search" method="get">
      <input type="hidden" name="tab" value="journey">
      <label class="form-label fw-bold" for="er-journey-ref">Complaint reference number</label>
      <div class="input-group">
        <input id="er-journey-ref" class="form-control" type="search" name="reference_no" maxlength="45"
          value="<?php echo e($referenceSearch ?: ($selected['reference_no'] ?? '')); ?>"
          placeholder="TCMC-2026-970103" required>
        <button class="btn btn-primary" type="submit"><i class="bi bi-search me-1"></i> View journey</button>
      </div>
      <div class="form-text">Enter the exact reference number, or click View journey beside a result in Overview.</div>
    </form>
    <?php if(($complaintId>0 || $referenceSearch!=='') && !$selected): ?>
      <div class="alert alert-warning">Complaint not found or outside your authorized department.</div>
    <?php endif; ?>
    <?php if($selected): ?>
      <div class="er-journey-head er-card">
        <div><small>REFERENCE NUMBER</small><h2><?php echo e($selected['reference_no']??('#'.$selected['id'])); ?></h2><p><?php echo e($selected['department_name']); ?> · <?php echo e($selected['category_name']); ?> · Zone <?php echo e($selected['zone_no']); ?></p></div>
        <div><span class="er-state"><?php echo e($selected['status']); ?></span><div class="mt-2"><a class="btn btn-sm btn-outline-primary" href="<?php echo e(url('employee/complaint-view.php?id='.(int)$selected['id'])); ?>">Open complaint</a></div></div>
      </div>
      <?php if($stageIssue!==''): ?><div class="alert alert-warning mt-3"><i class="bi bi-exclamation-triangle me-1"></i><?php echo e($stageIssue); ?></div><?php endif; ?>
      <?php if($routingReadiness): ?>
      <div class="er-readiness er-readiness-<?php echo e($routingReadiness['state']); ?>" role="status">
        <div class="er-readiness-title"><i class="bi bi-clipboard2-pulse" aria-hidden="true"></i><strong>Escalation readiness check</strong><span class="er-readiness-state"><?php echo e($routingReadiness['title']); ?></span></div>
        <p><?php echo e($routingReadiness['detail']); ?></p>
        <p class="er-readiness-foot"><i class="bi bi-shield-lock me-1"></i>This diagnostic only reads the database. It does not check job scheduling or execute escalation.</p>
      </div>
      <?php endif; ?>
      <div class="er-journey-layout">
        <div class="er-card">
          <div class="er-card-head"><div><h2>Full escalation ladder</h2><p>Stages are specific to this complaint category.</p></div><span class="er-state"><?php echo count($stageRows); ?> stages</span></div>
          <?php if(!$stageRows): ?><p class="text-secondary">No PDF stages recorded for this category.</p><?php endif; ?>
          <div class="er-timeline">
            <?php foreach($stageRows as $step): ?>
              <?php $n=(int)$step['stage_no'];$past=$n<$currentLevel;$active=$n===$currentLevel;$person=$stagePeople[$n]??null; ?>
              <div class="er-timeline-item <?php echo $active?'is-active':($past?'is-past':'is-future'); ?>">
                <div class="er-timeline-num"><?php echo $n; ?></div>
                <div class="er-timeline-content">
                  <div class="er-timeline-heading"><strong>Stage <?php echo $n; ?> — <?php echo e($step['role_label']); ?></strong><span class="er-state"><?php echo $active?'Current stage':($past?($person?'Recorded earlier':'Missing historical record'):'Upcoming'); ?></span></div>
                  <p>PDF stage SLA: <strong><?php echo (int)$step['period_days']; ?> day(s)</strong></p>
                  <?php $preview=$stageOfficerPreviews[$n]??null; ?>
                  <?php if($active): ?>
                    <p>Current assigned officer: <strong><?php echo e($selected['current_officer_name']?:'Unassigned'); ?></strong></p>
                    <small class="er-officer-label er-officer-recorded">Assigned now</small>
                  <?php elseif($person): ?>
                    <p>Recorded recipient: <strong><?php echo e($person['officer_name']?:('#'.$person['officer_id'])); ?></strong></p>
                    <small class="er-officer-label er-officer-recorded">Actual historical assignment</small>
                  <?php elseif($preview && $preview['candidate']): ?>
                    <p>Eligible officer: <strong><?php echo e($preview['candidate']['officer_name']); ?></strong>
                    <span class="text-secondary">(<?php echo e($preview['candidate']['designation']); ?>)</span></p>
                    <small class="er-officer-label er-officer-preview">Future candidate — not assigned</small>
                    <small class="d-block text-secondary mt-1"><?php echo e($preview['label']); ?></small>
                  <?php elseif($preview): ?>
                    <p class="er-officer-missing">Officer not confirmed: <?php echo e($preview['label']); ?></p>
                    <small class="er-officer-label er-officer-unavailable">Stage mapping needs review</small>
                  <?php else: ?>
                    <p>Officer not assigned to this stage</p>
                  <?php endif; ?>
                  <?php if($person): ?><small class="text-secondary">Recorded at: <?php echo e(er_date($person['escalated_at']??$person['assigned_at']??null)); ?></small><?php endif; ?>
                  <?php if($active): ?><small class="d-block text-secondary">Current recorded deadline: <?php echo e(er_date($selected['due_at'])); ?></small><?php endif; ?>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
        <div class="er-aside">
          <div class="er-card er-next-card"><div class="er-card-head"><div><h2>Next responsible stage</h2><p>Verified candidates are previewed, never pre-assigned.</p></div></div>
            <?php $upcoming=$policy?pdf_policy_stage($policy,$currentLevel+1):null; ?>
            <?php if($upcoming): ?>
              <div class="er-next-role">Stage <?php echo $currentLevel+1; ?> · <?php echo e($upcoming['role_label']); ?></div>
              <p class="text-secondary">Next-stage SLA: <?php echo (int)$upcoming['period_days']; ?> day(s) after transfer</p>
            <?php else: ?><div class="er-next-role">No confirmed next stage</div><?php endif; ?>
            <?php if($nextPreview['candidate']): ?><p><strong><?php echo e($nextPreview['candidate']['officer_name']); ?></strong><br><span class="text-secondary"><?php echo e($nextPreview['candidate']['designation']); ?></span></p><?php endif; ?>
            <div class="er-next-note"><?php echo e($nextPreview['label']); ?></div>
            <p class="small text-secondary mt-3 mb-0">All later-stage candidate names are shown in the escalation ladder. Only recorded transfers represent actual assignments.</p>
          </div>
          <div class="er-card"><div class="er-card-head"><h2>Actual officer movements</h2></div>
            <?php if(!$assignments): ?><p class="text-secondary">No assignment history found.</p><?php endif; ?>
            <?php foreach($assignments as $i=>$a): ?>
              <div class="er-transfer"><small><?php echo $i===0?'Initial assignment':'Assignment transfer'; ?> · <?php echo e(er_date($a['assigned_at'])); ?></small>
                <div><?php echo e($i===0?'Intake / unassigned':($assignments[$i-1]['officer_name']??'Unknown previous officer')); ?> <i class="bi bi-arrow-right"></i> <strong><?php echo e($a['officer_name']?:('#'.$a['officer_id'])); ?></strong></div></div>
            <?php endforeach; ?>
          </div>
          <div class="er-card"><div class="er-card-head"><h2>Recorded escalations</h2></div>
            <?php if(!$events): ?><p class="text-secondary">No higher-stage transfers recorded yet.</p><?php endif; ?>
            <?php foreach($events as $ev): ?><div class="er-transfer"><small><?php echo e(er_date($ev['escalated_at'])); ?></small><div>Stage <?php echo (int)$ev['level_no']; ?> received by <strong><?php echo e($ev['officer_name']?:('#'.$ev['officer_id'])); ?></strong></div></div><?php endforeach; ?>
          </div>
        </div>
      </div>
    <?php endif; ?>
  <?php else: ?>
    <div class="er-card"><div class="er-card-head"><div><h2>Category-wise PDF escalation stages</h2><p><?php echo number_format($categoryTotal); ?> categories · all stages are shown in PDF order.</p></div></div>
      <div class="table-responsive"><table class="table table-hover align-middle er-table"><thead><tr><th>Department</th><th>Category</th><th>Escalation ladder</th><th>Total PDF SLA</th><th>Policy</th></tr></thead><tbody>
      <?php if(!$categoryRows): ?><tr><td colspan="5" class="text-center py-5 text-secondary">No categories match these filters.</td></tr><?php endif; ?>
      <?php foreach($categoryRows as $c): ?><tr><td><?php echo e($c['department_name']); ?></td><td><strong><?php echo e($c['category_name']); ?></strong></td>
        <td><div class="er-stage-tags"><?php foreach($c['stages'] as $step): ?><span title="Stage <?php echo (int)$step['stage_no']; ?>, <?php echo (int)$step['period_days']; ?> days">L<?php echo (int)$step['stage_no']; ?> · <?php echo e($step['role_label']); ?> · <?php echo (int)$step['period_days']; ?>d</span><?php endforeach; ?><?php if(!$c['stages']): ?>No validated stages<?php endif; ?></div></td>
        <td><?php echo $c['ready']?(int)$c['total_days'].' days':'—'; ?></td>
        <td><span class="er-state"><?php echo $c['ready']?'Routable PDF':'Needs review'; ?></span></td></tr><?php endforeach; ?>
      </tbody></table></div>
    </div>
    <?php if($categoryPages>1): ?><div class="er-pages"><a class="btn btn-sm btn-outline-secondary <?php echo $page<=1?'disabled':''; ?>" href="<?php echo e(er_link(['page'=>max(1,$page-1)])); ?>">Previous</a><span>Page <?php echo $page; ?> / <?php echo $categoryPages; ?></span><a class="btn btn-sm btn-outline-secondary <?php echo $page>=$categoryPages?'disabled':''; ?>" href="<?php echo e(er_link(['page'=>min($categoryPages,$page+1)])); ?>">Next</a></div><?php endif; ?>
  <?php endif; ?>
</div>
<?php include __DIR__.'/../includes/employee-footer.php'; ?>
