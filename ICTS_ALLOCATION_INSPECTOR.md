# Officer Allocation Inspector — Phase 1 (read-only)

Verified from the current project:
- \`includes/functions.php::assign_officer_for_complaint()\` checks the exact Stage 1 department allocation rule first, then ward+department, category+ward, category escalation Level 1, category-only, department-only.
- Existing \`masters.php\` supports manual mapping edits and allocations. The current environment screenshot displayed 12,494 officers and no linked employee accounts; mappings should be audited before bulk changes.
- Existing \`escalation_officer_levels\` data may contain configured role names that do NOT match the officer's actual title.

## Added: \`employee/allocation-inspector.php\`
- Admin/Super Admin only. Requires \`masters.view\` in the central role mapper.
- Six summary counts, including blank officer names, linked staff accounts, mapping totals and inactive stage officers.
- Select a category and ward for a **read-only initial assignment preview** that runs the same prioritized SQL queries as the PHP initial-assignment function, but NEVER writes.
- Shows configured category-level stages, exact ward allocation rules, stage SLA days, and warnings for inactive officers or missing department/category/ward mappings.
- Officer search is bounded to 60 entries; loads no full 12k-row list and avoids the existing \`masters.php\` expensive cross-join.
- No migrations, SQL Server imports, officer reassignments or escalation jobs.

## Limitations and safeguards
- Preview is a point-in-time estimate; it cannot guarantee a future outcome if rules change, and does not run database BEFORE INSERT triggers.
- Stage rules without a department/category map may still have been deliberately configured; flagged as **review**, not automatically disabled.
- The inspector does not validate real staff job titles, authorize assignments or grant new role permissions.
- Existing admin edit tools are unchanged. Do not use them to bulk-edit rules without first verifying source data and testing in a separate database.
- The existing public complaint form still has a temporary OTP check bypass; resolve it before production deployment.

## Local test
1. Pull the feature branch and run \`php -l employee/allocation-inspector.php\` and \`php -l includes/employee-header.php\`.
2. As Super Admin, open \`employee/allocation-inspector.php\`. Verify summary counts against phpMyAdmin read-only COUNT queries.
3. Select complaint category 272 and its known ward 13 **if these IDs are still valid in the current database**; inspect exact rules and preview tier selection; do not assume historical IDs still match.
4. Search officer ID 18 or officer name. Confirm search is bounded and only displays actual stored mappings.
5. Verify non-admin role cannot directly access inspector (HTTP 403).
