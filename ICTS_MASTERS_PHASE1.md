# Advanced ICTS Masters — Phase 1

Source: authenticated ICTS menu screenshots showed Departments, Designations, DCC Escalation Mapper, Complaint Categories, Wards Master, Locality Master, User Registration, List of Users, User Mapping Details, Wards Mapping Details, Role Mapping, Role Page Mapper and Complaint Source Master.

New admin-only \`employee/master-control.php\` provides:

- **Designations:** view current officer designation and linked account; an explicit, CSRF-protected, audited Admin/Super Admin "Save" action updates the designation only. Never infer official title from escalation labels.
- **Officer & Ward Mapping:** read-only counts of existing department/category/ward associations per officer and route to pre-existing \`masters.php?section=mappings\`.
- **Employee User Mapping:** user-to-officer links and scoped department/zone/ward information.
- **Roles & Page Access:** existing role distribution and link to user management; a configurable Role Page Mapper is NOT implemented. Don't treat these as a verified permission matrix.
- **DCC Escalation Mapping:** category-stage rules, officer titles, active flags and department/category map checks. No automated writes.
- **Complaint Sources:** observed source values and counts. No independent editable source master until the desired data model is approved.

No database migration required. Nothing is imported from PGP or SQL Server ICTS. The existing PHP role checks remain authoritative. No application records change unless an authorized admin manually submits a designation update.

## Local test
1. Pull development branch; lint \`employee/master-control.php\` and \`includes/employee-header.php\`.
2. Login as Super Admin; open all tabs. Verify that Department Head/Field Officer cannot access Advanced Masters.
3. Verify officer counts, unlinked employees, mapping coverage and escalation validity against phpMyAdmin.
4. On a dedicated \`smartcity_escalation_test\` application test environment only, update a known officer's official designation; verify only \`officers.designation\` changes and an \`audit_logs\` record appears. Don't guess staff titles in production.
5. Pending next phase: official designation master table; editable, enforced role-page permission matrix; verified user/ward mapping workflows; controlled stage rule changes with approval/rollback.

Important security issue outside this commit: \`new-complaint.php\` has a temporary OTP verification bypass. Restore OTP enforcement before any public deployment.
