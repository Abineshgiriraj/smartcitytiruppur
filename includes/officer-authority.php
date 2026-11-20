<?php
declare(strict_types=1);

/**
 * A deliberately conservative negative title check.
 * The approval table is the POSITIVE authority source. Neither officer IDs
 * nor names nor legacy category-level designation labels establish authority.
 */
function officer_stage_forbidden_title(?string $designation, int $stage, int $officerId): bool
{
    if ($officerId === 1 || $officerId < 1 || $stage < 1 || $stage > 4) return true;
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
function officer_stage_authorized(
    PDO $pdo, int $categoryId, int $stage, int $officerId,
    int $departmentId, int $wardId = 0
): bool {
    if (min($categoryId,$officerId,$departmentId) < 1 || $stage < 1 || $stage > 4) return false;
    static $errorLogged=false;
    try {
        $query=$pdo->prepare(
            "SELECT o.designation FROM officer_stage_approvals a
             JOIN officers o ON o.id=a.officer_id AND o.is_active=1
             JOIN complaint_categories c ON c.id=a.category_id AND c.is_active=1
             JOIN departments d ON d.id=c.department_id AND d.is_active=1
             WHERE a.category_id=? AND a.stage_no=? AND a.officer_id=?
               AND a.revoked_at IS NULL AND a.approved_by IS NOT NULL
               AND TRIM(a.approval_reference) <> ''
               AND c.department_id=?
               AND EXISTS(SELECT 1 FROM officer_department_map dm
                          WHERE dm.officer_id=o.id AND dm.department_id=c.department_id)
               AND EXISTS(SELECT 1 FROM officer_category_map cm
                          WHERE cm.officer_id=o.id AND cm.category_id=c.id)
             LIMIT 1"
        );
        $query->execute([$categoryId,$stage,$officerId,$departmentId]);
        $title=$query->fetchColumn();
        if ($title===false || officer_stage_forbidden_title((string)$title,$stage,$officerId)) return false;
        if ($stage===1 && $wardId>0) {
            $scope=$pdo->prepare(
                'SELECT
                   EXISTS(SELECT 1 FROM officer_ward_map WHERE officer_id=?) AS mapped_any,
                   EXISTS(SELECT 1 FROM officer_ward_map WHERE officer_id=? AND ward_id=?) AS mapped_here'
            );
            $scope->execute([$officerId,$officerId,$wardId]);
            $row=$scope->fetch(PDO::FETCH_ASSOC);
            if ((int)($row['mapped_any'] ?? 0)===1 && (int)($row['mapped_here'] ?? 0)!==1) return false;
        }
        return true;
    } catch (Throwable $error) {
        if (!$errorLogged && function_exists('log_message')) {
            log_message('Officer authority registry unavailable; assignment blocked: '.$error->getMessage());
            $errorLogged=true;
        }
        return false;
    }
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
