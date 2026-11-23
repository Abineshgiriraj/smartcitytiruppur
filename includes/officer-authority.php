<?php
declare(strict_types=1);
require_once __DIR__.'/pdf-escalation-policy.php';

/**
 * A deliberately conservative negative title check.
 * The approval table is the POSITIVE authority source. Neither officer IDs
 * nor names nor legacy category-level designation labels establish authority.
 */
function officer_stage_forbidden_title(?string $designation, int $stage, int $officerId): bool
{
    if ($officerId === 1 || $officerId < 1 || $stage < 1 || $stage > 6) return true;
    $title = strtoupper(trim((string)$designation));
    $title = trim(preg_replace('/[^A-Z0-9]+/', ' ', $title) ?? '');
    if (preg_match('/\b(SUPER ADMIN|SYSTEM ADMINISTRATOR)\b/', $title)) return true;
    // Forbid the Municipal Commissioner as a field/intermediate officer.
    $deputy = (bool)preg_match('/\b(DEPUTY|DY)\s+(COMMISSIONER|COMMR)\b/', $title);
    if ($deputy && $stage === 1) return true;
    if (!$deputy && preg_match('/\b(COMMR|COMMISSIONER)\b/', $title) && $stage < 4) return true;
    if ($stage === 1 && preg_match('/\b(SUPERINTENDING ENGINEER|CITY ENGINEER)\b/', $title)) return true;
    return false;
}

/**
 * Explicit, non-revoked departmental authorization for this exact category,
 * officer and stage. Missing table, missing approval or bad mapping = DENY.
 * Read-only; no automatic approval or officer reassignment.
 */
/**
 * Routing eligibility from existing officer master maps.
 * No additional municipal approval record is required, but an explicit
 * REVOKED stage approval is a routing veto.
 */
function officer_stage_authorized(
    PDO $pdo, int $categoryId, int $stage, int $officerId,
    int $departmentId, int $wardId = 0
): bool {
    if (min($categoryId,$officerId,$departmentId) < 1 || $stage < 1 || $stage > 6) return false;
    try {
        $query = $pdo->prepare(
            "SELECT o.designation FROM officers o
             JOIN complaint_categories c ON c.id=? AND c.is_active=1
             JOIN departments d ON d.id=c.department_id AND d.is_active=1
             WHERE o.id=? AND o.is_active=1 AND c.department_id=?
               AND EXISTS (SELECT 1 FROM officer_department_map dm
                           WHERE dm.officer_id=o.id AND dm.department_id=c.department_id)
               AND EXISTS (SELECT 1 FROM officer_category_map cm
                           WHERE cm.officer_id=o.id AND cm.category_id=c.id)
               AND NOT EXISTS (SELECT 1 FROM officer_stage_approvals a
                               WHERE a.category_id=c.id AND a.stage_no=? AND a.officer_id=o.id
                                 AND a.revoked_at IS NOT NULL)
             LIMIT 1"
        );
        $query->execute([$categoryId,$officerId,$departmentId,$stage]);
        $title=$query->fetchColumn();
        if($title===false) return false;
        $policy=pdf_policy_routing_ready($pdo,$categoryId);
        $required=$policy===null?null:pdf_policy_stage($policy,$stage);
        if(!$required || !pdf_policy_role_matches((string)$required['role_label'],(string)$title))return false;
        $forbidden=officer_stage_forbidden_title((string)$title,$stage,$officerId);
        if($forbidden && strtoupper(trim((string)$required['role_label']))==='COMMR'
           && $stage>1 && $officerId!==1 && pdf_policy_role_matches('COMMR',(string)$title)) {
            $forbidden=false;
        }
        if($forbidden)return false;
        if($wardId>0){
            $scope=$pdo->prepare(
                'SELECT EXISTS(SELECT 1 FROM officer_ward_map WHERE officer_id=?) AS mapped_any,
                        EXISTS(SELECT 1 FROM officer_ward_map WHERE officer_id=? AND ward_id=?) AS mapped_here'
            );
            $scope->execute([$officerId,$officerId,$wardId]);
            $row=$scope->fetch(PDO::FETCH_ASSOC);
            if((int)($row['mapped_any']??0)===1 && (int)($row['mapped_here']??0)!==1)return false;
        }
        return true;
    } catch(Throwable $error){
        if(function_exists('log_message'))log_message('Officer routing eligibility unavailable: '.$error->getMessage());
        return false;
    }
}

/** Return all eligible officer candidates, ordered by explicit ward scope. */
function pdf_routing_candidates(PDO $pdo,int $categoryId,int $stage,int $departmentId,int $wardId): array
{
    $sql="SELECT o.id,o.officer_name,o.designation,
          CASE WHEN EXISTS(SELECT 1 FROM officer_ward_map w WHERE w.officer_id=o.id AND w.ward_id=?) THEN 0
               WHEN NOT EXISTS(SELECT 1 FROM officer_ward_map w WHERE w.officer_id=o.id) THEN 1
               ELSE 2 END AS ward_rank
          FROM officers o
          JOIN officer_department_map dm ON dm.officer_id=o.id AND dm.department_id=?
          JOIN officer_category_map cm ON cm.officer_id=o.id AND cm.category_id=?
          WHERE o.is_active=1
          ORDER BY ward_rank,o.id";
    $stmt=$pdo->prepare($sql);
    $stmt->execute([$wardId,$departmentId,$categoryId]);
    $eligible=[];
    foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $row){
        if(officer_stage_authorized($pdo,$categoryId,$stage,(int)$row['id'],$departmentId,$wardId)){
            $eligible[]=$row;
        }
    }
    return $eligible;
}

/** Reject mismatched positive zone/ward or department/category data. */
function valid_complaint_routing_context(
    PDO $pdo, int $departmentId, int $categoryId, int $zoneId, int $wardId
): bool {
    if (min($departmentId,$categoryId,$zoneId,$wardId)<1) return false;
    $stmt=$pdo->prepare(
        'SELECT 1 FROM complaint_categories c
         JOIN departments d ON d.id=c.department_id AND d.is_active=1
         JOIN wards w ON w.id=? AND w.zone_id=? AND w.is_active=1
         JOIN zones z ON z.id=w.zone_id AND z.is_active=1
         WHERE c.id=? AND c.department_id=? AND c.is_active=1 LIMIT 1'
    );
    $stmt->execute([$wardId,$zoneId,$categoryId,$departmentId]);
    return (bool)$stmt->fetchColumn();
}

/** Read the current escalation stage. Does not mutate complaint history. */
function complaint_last_stage(PDO $pdo, int $complaintId): int
{
    $stmt=$pdo->prepare(
        'SELECT level_no FROM complaint_escalations
         WHERE complaint_id=? ORDER BY escalated_at DESC,id DESC LIMIT 1'
    );
    $stmt->execute([$complaintId]);
    $stage=$stmt->fetchColumn();
    return $stage===false ? 1 : (int)$stage;
}
