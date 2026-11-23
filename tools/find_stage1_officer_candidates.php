<?php
declare(strict_types=1);
/**
 * Read-only search for candidate officer records, including officers that may
 * be missing department/category/ward mappings. NEVER grants authorization.
 *
 * php tools/find_stage1_officer_candidates.php --expect-db=smartcity_escalation_test --reference=TCMC-2026-950324
 */
if (PHP_SAPI !== 'cli') { http_response_code(403); exit("CLI only\n"); }
$expected = null;
$reference = null;
foreach (array_slice($argv, 1) as $arg) {
    if (preg_match('/^--expect-db=([A-Za-z0-9_]+)$/D', $arg, $m)) {
        $expected = $m[1]; continue;
    }
    if (preg_match('/^--reference=([A-Za-z0-9-]{1,40})$/D', $arg, $m)) {
        $reference = $m[1]; continue;
    }
    fwrite(STDERR, "Unknown argument: {$arg}\n"); exit(2);
}
if (!$expected || !$reference) {
    fwrite(STDERR, "Required --expect-db=DATABASE --reference=COMPLAINT_REFERENCE\n");
    exit(2);
}
require_once dirname(__DIR__).'/includes/bootstrap.php';
$pdo = db();
$actual = (string)$pdo->query('SELECT DATABASE()')->fetchColumn();
if ($actual !== $expected) {
    fwrite(STDERR, "DB mismatch {$actual} != {$expected}. No changes.\n"); exit(2);
}
$stmt = $pdo->prepare(
    "SELECT c.id,c.reference_no,c.category_id,c.department_id,c.ward_id,c.zone_id,
            cc.category_name,d.department_name
     FROM complaints c
     JOIN complaint_categories cc ON cc.id=c.category_id
     JOIN departments d ON d.id=c.department_id
     WHERE c.reference_no=? AND c.is_deleted=0 LIMIT 1"
);
$stmt->execute([$reference]);
$c = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$c) { fwrite(STDERR, "Complaint not found. No changes.\n"); exit(2); }
$cat = (int)$c['category_id'];
$dep = (int)$c['department_id'];
$ward = (int)$c['ward_id'];
$policy = pdf_policy_reviewed($pdo,$cat);
if (!$policy) {
    fwrite(STDERR, "Category policy not fully approved; cannot verify Stage 1 role.\n");
    exit(3);
}
$stage = pdf_policy_stage($policy,1);
if (!$stage) { fwrite(STDERR, "Missing PDF Stage 1.\n"); exit(3); }
$role = (string)$stage['role_label'];
echo "READ ONLY: {$actual}; complaint {$c['reference_no']}\n";
echo "Category {$c['department_name']} / {$c['category_name']}; ward ID={$ward}; Stage 1 role={$role}\n";
echo "Searching other active officer records, not just the six existing category-mapped officers.\n";
echo "This output is NOT proof of identity, appointment, or authority.\n";
$query = $pdo->prepare(
    "SELECT o.id,o.officer_name,o.designation,
       EXISTS(SELECT 1 FROM officer_department_map dm
              WHERE dm.officer_id=o.id AND dm.department_id=?) AS dept_ok,
       EXISTS(SELECT 1 FROM officer_category_map cm
              WHERE cm.officer_id=o.id AND cm.category_id=?) AS cat_ok,
       EXISTS(SELECT 1 FROM officer_ward_map wm
              WHERE wm.officer_id=o.id) AS has_ward_scope,
       EXISTS(SELECT 1 FROM officer_ward_map wm
              WHERE wm.officer_id=o.id AND wm.ward_id=?) AS ward_ok
     FROM officers o
     WHERE o.is_active=1 AND o.designation IS NOT NULL
       AND TRIM(o.designation)<>''
       AND (
         UPPER(o.designation) LIKE '%ELECTR%'
         OR UPPER(o.designation) LIKE '%ASSISTANT ENGINEER%'
         OR UPPER(o.designation) LIKE '%JUNIOR ENGINEER%'
         OR UPPER(o.designation) REGEXP '(^|[^A-Z])AE([^A-Z]|$)'
         OR UPPER(o.designation) REGEXP '(^|[^A-Z])JE([^A-Z]|$)'
       )
     ORDER BY o.id LIMIT 500"
);
$query->execute([$dep,$cat,$ward]);
$shown = 0; $possible = 0; $strong = 0;
while ($o = $query->fetch(PDO::FETCH_ASSOC)) {
    $id = (int)$o['id'];
    $designation = (string)$o['designation'];
    $roleMatch = pdf_policy_role_matches($role,$designation);
    if (!$roleMatch) continue;
    $possible++;
    // A generic "AE" designation is not evidence of Electrical specialty.
    $specialty = !preg_match('/ELECTR/i',$role)
        || (bool)preg_match('/ELECTR/i',$designation);
    if ($specialty) $strong++;
    $wardScope = !(int)$o['has_ward_scope'] ? 'UNSPECIFIED/CITYWIDE'
        : ((int)$o['ward_ok'] ? 'MAPPED_TO_WARD' : 'OTHER_WARD');
    $approved = officer_stage_authorized($pdo,$cat,1,$id,$dep,$ward);
    if ($shown >= 80) continue;
    $shown++;
    echo sprintf(
        "#%d %s | title=%s | electrical_specialty=%s | department=%s | category=%s | ward=%s | stage1_authorized=%s\n",
        $id, (string)$o['officer_name'], $designation, $specialty?'IN_TITLE':'NOT_CONFIRMED',
        (int)$o['dept_ok']?'MAPPED':'MISSING',
        (int)$o['cat_ok']?'MAPPED':'MISSING',
        $wardScope, $approved?'YES':'NO'
    );
}
echo "Role-string matches examined: {$possible}; with electrical specialty in title: {$strong}.\n";
if ($shown < $possible) echo "Only first {$shown} matches shown. Review data directly with authorized staff.\n";
if ($possible===0) echo "No candidates with a populated Stage 1-compatible designation were found in the bounded search.\n";
echo "No approvals, mappings, designations, complaints or other records were changed.\n";
