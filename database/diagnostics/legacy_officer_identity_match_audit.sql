-- Smart City Tiruppur: READ-ONLY historical officer identity reconciliation
-- MariaDB 10.4+, uses ONLY SELECT. Does NOT alter complaints or login access.
-- Execute against your local smartcitytiruppur database, NOT live.
--
-- Background:
--   complaints.assigned_officer_id points to officers.id (not an officer name).
--   The same real person may have old inactive and new active officers.id values.
--   Name-only matches are CANDIDATES, NOT AUTHORIZED IDENTITY LINKS.
--   NULL assignees cannot be recovered by looking at officer names.

-- (A) Complaint ownership totals; always compare to admin dashboard.
SELECT
    COUNT(*) AS all_complaints,
    SUM(c.assigned_officer_id IS NULL) AS unassigned,
    SUM(c.assigned_officer_id IS NOT NULL) AS assigned,
    SUM(c.assigned_officer_id IS NOT NULL AND o.id IS NULL) AS missing_officer,
    SUM(c.assigned_officer_id IS NOT NULL AND o.is_active = 0) AS assigned_to_inactive_officer,
    SUM(c.assigned_officer_id IS NOT NULL AND o.is_active = 1) AS assigned_to_active_officer,
    SUM(c.assigned_officer_id IS NULL
        AND c.status NOT IN ('Resolved','Closed','Rejected')) AS open_unassigned
FROM complaints c
LEFT JOIN officers o ON o.id=c.assigned_officer_id
WHERE c.is_deleted=0;

-- (B) Historical owner candidates: officer name + corroborating signals.
-- Rows are candidate old/new identity PAIRS, not assignments.
SELECT
    old.id AS legacy_officer_id,
    old.officer_name AS legacy_name,
    old.designation AS legacy_designation,
    owned.complaint_count,
    owned.open_count,
    current.id AS current_officer_id,
    current.officer_name AS current_name,
    current.designation AS current_designation,
    CASE
      WHEN UPPER(TRIM(COALESCE(old.designation,''))) =
           UPPER(TRIM(COALESCE(current.designation,'')))
           AND TRIM(COALESCE(old.designation,'')) <> ''
      THEN 'YES' ELSE 'NO' END AS designation_matches,
    CASE WHEN EXISTS (
        SELECT 1 FROM officer_department_map dm_old
        JOIN officer_department_map dm_new
          ON dm_new.department_id=dm_old.department_id
        WHERE dm_old.officer_id=old.id AND dm_new.officer_id=current.id
    ) THEN 'YES' ELSE 'NO' END AS shared_department,
    CASE WHEN TRIM(COALESCE(old.mobile_no,'')) <> ''
            AND TRIM(old.mobile_no)=TRIM(COALESCE(current.mobile_no,''))
         THEN 'YES' ELSE 'NO' END AS mobile_matches,
    CASE WHEN TRIM(COALESCE(old.email,'')) <> ''
            AND LOWER(TRIM(old.email))=LOWER(TRIM(COALESCE(current.email,'')))
         THEN 'YES' ELSE 'NO' END AS email_matches,
    (SELECT COUNT(*)
     FROM officers other
     WHERE other.is_active=1
       AND other.user_id IS NOT NULL
       AND UPPER(TRIM(other.officer_name))=UPPER(TRIM(old.officer_name))
       AND EXISTS (
           SELECT 1 FROM users login
           WHERE login.id=other.user_id AND login.is_active=1
       )
    ) AS active_name_matches
FROM (
    SELECT assigned_officer_id,
           COUNT(*) AS complaint_count,
           SUM(status NOT IN ('Resolved','Closed','Rejected')) AS open_count
    FROM complaints
    WHERE is_deleted=0 AND assigned_officer_id IS NOT NULL
    GROUP BY assigned_officer_id
) owned
JOIN officers old ON old.id=owned.assigned_officer_id AND old.is_active=0
JOIN officers current
  ON current.is_active=1
 AND current.id<>old.id
 AND current.user_id IS NOT NULL
 AND CHAR_LENGTH(TRIM(old.officer_name))>=4
 AND UPPER(TRIM(current.officer_name))=UPPER(TRIM(old.officer_name))
JOIN users active_login ON active_login.id=current.user_id AND active_login.is_active=1
ORDER BY owned.complaint_count DESC,old.id,current.id
LIMIT 300;

-- (C) Count candidate ambiguity: a name is NOT proof of identity.
SELECT
    candidates.match_count AS active_same_name_candidates,
    COUNT(*) AS legacy_officer_records,
    SUM(candidates.owned_count) AS complaints_with_these_legacy_owners
FROM (
    SELECT old.id AS legacy_id, owner_count.owned_count,
           (SELECT COUNT(*)
            FROM officers active
            JOIN users login ON login.id=active.user_id AND login.is_active=1
            WHERE active.is_active=1
              AND active.id<>old.id
              AND CHAR_LENGTH(TRIM(old.officer_name))>=4
              AND UPPER(TRIM(active.officer_name))=UPPER(TRIM(old.officer_name))
           ) AS match_count
    FROM officers old
    JOIN (
       SELECT assigned_officer_id,COUNT(*) AS owned_count
       FROM complaints
       WHERE is_deleted=0 AND assigned_officer_id IS NOT NULL
       GROUP BY assigned_officer_id
    ) owner_count ON owner_count.assigned_officer_id=old.id
    WHERE old.is_active=0
) candidates
GROUP BY candidates.match_count
ORDER BY candidates.match_count;

-- Next step after reviewing results:
-- Confirm old/current officer IDs represent the SAME person using a verified
-- personnel identifier or administrator-reviewed evidence. DO NOT update
-- complaints.assigned_officer_id based on name matching alone.
