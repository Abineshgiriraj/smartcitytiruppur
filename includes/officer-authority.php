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
        $policy=pdf_policy_routing_ready($pdo,$categoryId);
        $required=$policy===null?null:pdf_policy_stage($policy,$stage);
        if(!$required) return false;
        $reqRole = (string)$required['role_label'];
        $cleanRole = strtoupper(trim(preg_replace('/[^A-Z0-9]+/',' ',$reqRole) ?? ''));
        $isCorporateExec = in_array($cleanRole, ['COMMR', 'DEPUTY COMMR', 'COMMISSIONER', 'DEPUTY COMMISSIONER'], true);

        $query = $pdo->prepare(
            "SELECT o.designation FROM officers o
             JOIN complaint_categories c ON c.id=? AND c.is_active=1
             JOIN departments d ON d.id=c.department_id AND d.is_active=1
             WHERE o.id=? AND o.is_active=1 AND o.designation != 'Corporation Officer' AND c.department_id=?
               AND (
                   ? = 1
                   OR EXISTS (SELECT 1 FROM officer_department_map dm
                              WHERE dm.officer_id=o.id AND dm.department_id=c.department_id)
               )
               AND (
                   ? > 1
                   OR EXISTS (SELECT 1 FROM officer_category_map cm
                              WHERE cm.officer_id=o.id AND cm.category_id=c.id)
                   OR NOT EXISTS (SELECT 1 FROM officer_category_map cm_any
                                  WHERE cm_any.category_id=c.id)
               )
               AND NOT EXISTS (SELECT 1 FROM officer_stage_approvals a
                               WHERE a.category_id=c.id AND a.stage_no=? AND a.officer_id=o.id
                                 AND a.revoked_at IS NOT NULL)
             LIMIT 1"
        );
        $query->execute([$categoryId, $officerId, $departmentId, $isCorporateExec ? 1 : 0, $stage, $stage]);
        $title=$query->fetchColumn();
        if($title===false) return false;

        if(!pdf_policy_role_matches($reqRole, (string)$title)) return false;

        $forbidden=officer_stage_forbidden_title((string)$title,$stage,$officerId);
        if($forbidden && strtoupper(trim($reqRole))==='COMMR'
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
    $policy = pdf_policy_routing_ready($pdo, $categoryId);
    $required = $policy === null ? null : pdf_policy_stage($policy, $stage);
    $roleLabel = $required ? (string)$required['role_label'] : '';
    $cleanRole = strtoupper(trim(preg_replace('/[^A-Z0-9]+/',' ',$roleLabel) ?? ''));
    $isCorporateExec = in_array($cleanRole, ['COMMR', 'DEPUTY COMMR', 'COMMISSIONER', 'DEPUTY COMMISSIONER'], true);

    if ($stage > 1) {
        if ($isCorporateExec) {
            $sql = "SELECT o.id, o.officer_name, o.designation, 0 AS ward_rank
                    FROM officers o
                    WHERE o.is_active = 1 AND o.designation IN ('COMMR', 'DEPUTY COMMR', 'COMMISSIONER', 'DEPUTY COMMISSIONER')
                    ORDER BY o.id";
            $stmt = $pdo->query($sql);
        } else {
            $sql = "SELECT o.id, o.officer_name, o.designation,
                           CASE WHEN EXISTS(SELECT 1 FROM officer_ward_map w WHERE w.officer_id=o.id AND w.ward_id=?) THEN 0
                                WHEN NOT EXISTS(SELECT 1 FROM officer_ward_map w WHERE w.officer_id=o.id) THEN 1
                                ELSE 2 END AS ward_rank
                    FROM officers o
                    JOIN officer_department_map dm ON dm.officer_id=o.id AND dm.department_id=?
                    WHERE o.is_active=1 AND o.designation != 'Corporation Officer'
                    ORDER BY ward_rank, o.id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$wardId, $departmentId]);
        }
    } else {
        $hasCatMap = (bool)$pdo->query("SELECT 1 FROM officer_category_map WHERE category_id = {$categoryId} LIMIT 1")->fetchColumn();
        if ($hasCatMap) {
            $sql="SELECT o.id,o.officer_name,o.designation,
                  CASE WHEN EXISTS(SELECT 1 FROM officer_ward_map w WHERE w.officer_id=o.id AND w.ward_id=?) THEN 0
                       WHEN NOT EXISTS(SELECT 1 FROM officer_ward_map w WHERE w.officer_id=o.id) THEN 1
                       ELSE 2 END AS ward_rank
                  FROM officers o
                  JOIN officer_department_map dm ON dm.officer_id=o.id AND dm.department_id=?
                  JOIN officer_category_map cm ON cm.officer_id=o.id AND cm.category_id=?
                  WHERE o.is_active=1 AND o.designation != 'Corporation Officer'
                  ORDER BY ward_rank,o.id";
            $stmt=$pdo->prepare($sql);
            $stmt->execute([$wardId,$departmentId,$categoryId]);
        } else {
            $sql="SELECT o.id,o.officer_name,o.designation,
                  CASE WHEN EXISTS(SELECT 1 FROM officer_ward_map w WHERE w.officer_id=o.id AND w.ward_id=?) THEN 0
                       WHEN NOT EXISTS(SELECT 1 FROM officer_ward_map w WHERE w.officer_id=o.id) THEN 1
                       ELSE 2 END AS ward_rank
                  FROM officers o
                  JOIN officer_department_map dm ON dm.officer_id=o.id AND dm.department_id=?
                  WHERE o.is_active=1 AND o.designation != 'Corporation Officer'
                  ORDER BY ward_rank,o.id";
            $stmt=$pdo->prepare($sql);
            $stmt->execute([$wardId,$departmentId]);
        }
    }

    $eligible=[];
    foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $row){
        if(officer_stage_authorized($pdo,$categoryId,$stage,(int)$row['id'],$departmentId,$wardId)){
            $row['ward_rank']=(int)$row['ward_rank'];
            $eligible[]=$row;
        }
    }
    return $eligible;
}

/**
 * Select an eligible officer using ward specificity, then current open workload.
 * Candidate eligibility is checked by pdf_routing_candidates() before this call.
 * An exact active department allocation always takes precedence in the caller.
 * Returns null only when no eligible officer exists; ID is the stable tie-break.
 */
function pdf_pick_least_loaded_officer(PDO $pdo, array $candidates): ?array
{
    if (!$candidates) return null;
    $rank = min(array_map(static fn(array $c):int => (int)$c['ward_rank'], $candidates));
    $load = $pdo->prepare(
        "SELECT COUNT(*) FROM complaints
         WHERE assigned_officer_id=? AND is_deleted=0
           AND status NOT IN ('Resolved','Closed','Rejected')"
    );
    $winner = null;
    $lowestCount = PHP_INT_MAX;
    foreach ($candidates as $candidate) {
        if ((int)$candidate['ward_rank'] !== $rank) continue;
        $officerId = (int)$candidate['id'];
        $load->execute([$officerId]);
        $count = (int)$load->fetchColumn();
        if ($winner === null || $count < $lowestCount
            || ($count === $lowestCount && $officerId < (int)$winner['id'])) {
            $winner = $candidate;
            $winner['open_complaints'] = $count;
            $lowestCount = $count;
        }
    }
    return $winner;
}

/**
 * Intake fallback: choose active staff in the correct department and ward
 * even when an imported PDF role or category map cannot be satisfied.
 * This is NOT a PDF Stage 1 assignment and requires specialist re-routing.
 */
function department_intake_candidates(
    PDO $pdo, int $departmentId, int $categoryId, int $wardId
): array {
    $stmt=$pdo->prepare(
        "SELECT DISTINCT o.id,o.officer_name,o.designation,
          CASE WHEN EXISTS(SELECT 1 FROM officer_ward_map w
                           WHERE w.officer_id=o.id AND w.ward_id=?) THEN 0
               ELSE 1 END AS ward_rank,
          CASE WHEN EXISTS(SELECT 1 FROM officer_category_map cm
                           WHERE cm.officer_id=o.id AND cm.category_id=?) THEN 0
               ELSE 1 END AS category_rank
         FROM officers o
         JOIN officer_department_map dm ON dm.officer_id=o.id AND dm.department_id=?
         WHERE o.is_active=1
           AND (NOT EXISTS(SELECT 1 FROM officer_ward_map w WHERE w.officer_id=o.id)
                OR EXISTS(SELECT 1 FROM officer_ward_map w
                          WHERE w.officer_id=o.id AND w.ward_id=?))
         ORDER BY ward_rank,category_rank,o.id"
    );
    $stmt->execute([$wardId,$categoryId,$departmentId,$wardId]);
    $valid=[];
    foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $officer){
        if(!officer_stage_forbidden_title((string)$officer['designation'],1,(int)$officer['id'])){
            $valid[]=$officer;
        }
    }
    if(!$valid)return [];
    $wardRank=min(array_map(static fn(array $o):int=>(int)$o['ward_rank'],$valid));
    $valid=array_values(array_filter($valid,
        static fn(array $o):bool=>(int)$o['ward_rank']===$wardRank));
    $categoryRank=min(array_map(static fn(array $o):int=>(int)$o['category_rank'],$valid));
    return array_values(array_filter($valid,
        static fn(array $o):bool=>(int)$o['category_rank']===$categoryRank));
}

/** Only a human-configured, active central intake officer may act citywide. */
function configured_central_intake_officer(PDO $pdo): ?array
{
    $stmt=$pdo->prepare("SELECT setting_value FROM system_settings
                        WHERE setting_key='central_intake_officer_id' LIMIT 1");
    $stmt->execute();
    $raw=trim((string)($stmt->fetchColumn() ?: ''));
    if(!ctype_digit($raw) || (int)$raw<1)return null;
    $officerId=(int)$raw;
    $stmt=$pdo->prepare(
        'SELECT id,officer_name,designation FROM officers WHERE id=? AND is_active=1 LIMIT 1'
    );
    $stmt->execute([$officerId]);
    $officer=$stmt->fetch(PDO::FETCH_ASSOC);
    if(!$officer || officer_stage_forbidden_title(
        (string)$officer['designation'],1,$officerId
    ))return null;
    $officer['ward_rank']=2; // lower priority than any eligible department staff
    return $officer;
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

/**
 * Deterministically select the candidate with the lowest active complaint workload.
 * Distributes citizen complaints evenly across equally-suitable ward/department officers.
 */
function select_least_loaded_officer(PDO $pdo, array $candidates): array
{
    if (empty($candidates)) {
        throw new InvalidArgumentException('Candidate list cannot be empty.');
    }
    if (count($candidates) === 1) {
        return $candidates[0];
    }
    $ids = array_map(static fn(array $c): int => (int)$c['id'], $candidates);
    $inClause = implode(',', $ids);
    $counts = [];
    try {
        $stmt = $pdo->query(
            "SELECT assigned_officer_id, COUNT(*) AS active_count
             FROM complaints
             WHERE assigned_officer_id IN ({$inClause})
               AND status NOT IN ('Resolved', 'Closed', 'Rejected')
               AND is_deleted = 0
             GROUP BY assigned_officer_id"
        );
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $counts[(int)$row['assigned_officer_id']] = (int)$row['active_count'];
        }
    } catch (Throwable $e) {}

    usort($candidates, static function(array $a, array $b) use ($counts): int {
        $c1 = $counts[(int)$a['id']] ?? 0;
        $c2 = $counts[(int)$b['id']] ?? 0;
        if ($c1 !== $c2) {
            return $c1 <=> $c2;
        }
        return (int)$a['id'] <=> (int)$b['id'];
    });

    return $candidates[0];
}

/** Return active department officers matching the required Stage 1 role, ordered by ward scope. */
function pdf_department_role_candidates(PDO $pdo, int $departmentId, string $requiredRole, int $wardId): array
{
    $sql = "SELECT o.id, o.officer_name, o.designation,
                   CASE WHEN EXISTS(SELECT 1 FROM officer_ward_map w WHERE w.officer_id=o.id AND w.ward_id=?) THEN 0
                        WHEN NOT EXISTS(SELECT 1 FROM officer_ward_map w WHERE w.officer_id=o.id) THEN 1
                        ELSE 2 END AS ward_rank
            FROM officers o
            JOIN officer_department_map dm ON dm.officer_id=o.id AND dm.department_id=?
            WHERE o.is_active=1
            ORDER BY ward_rank, o.id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$wardId, $departmentId]);
    $eligible = [];
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        if (pdf_policy_role_matches($requiredRole, (string)$row['designation'])) {
            if (!officer_stage_forbidden_title((string)$row['designation'], 1, (int)$row['id'])) {
                $eligible[] = $row;
            }
        }
    }
    return $eligible;
}

/** Return active operational officers mapped to this ward in this department as fallback. */
function pdf_department_ward_fallback_candidates(PDO $pdo, int $departmentId, int $wardId): array
{
    $sql = "SELECT o.id, o.officer_name, o.designation, 0 AS ward_rank
            FROM officers o
            JOIN officer_department_map dm ON dm.officer_id=o.id AND dm.department_id=?
            JOIN officer_ward_map w ON w.officer_id=o.id AND w.ward_id=?
            WHERE o.is_active=1
            ORDER BY o.id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$departmentId, $wardId]);
    $eligible = [];
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        if (!officer_stage_forbidden_title((string)$row['designation'], 1, (int)$row['id'])) {
            $eligible[] = $row;
        }
    }
    return $eligible;
}

/** Return active operational officers in this department as fallback. */
function pdf_department_general_fallback_candidates(PDO $pdo, int $departmentId): array
{
    $sql = "SELECT o.id, o.officer_name, o.designation, 1 AS ward_rank
            FROM officers o
            JOIN officer_department_map dm ON dm.officer_id=o.id AND dm.department_id=?
            WHERE o.is_active=1
            ORDER BY o.id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$departmentId]);
    $eligible = [];
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        if (!officer_stage_forbidden_title((string)$row['designation'], 1, (int)$row['id'])) {
            $eligible[] = $row;
        }
    }
    return $eligible;
}

