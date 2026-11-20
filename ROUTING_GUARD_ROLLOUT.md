# Escalation routing guard — first safe remediation

## Intent
Prevent direct-to-Commissioner Stage 1 assignment and unverified stage movement without guessing the approved ICTS hierarchy. No existing complaint, mapping, designation, SLA or employee account is changed by this commit.

## Enforcement
- New assignment checks department/category, ward/zone, active officer, explicit **officer_stage_approvals** entry, department + category mappings, ward scope, and forbidden executive title / Officer #1.
- Manual reassignment checks the same approval for the recorded current stage inside a row-locked transaction; status-only edits keep the existing officer; unassign closes history.
- CLI stage changes require both current and next officers to be approved. Stops if historical assignment/stage records disagree, or if stage >=4. Does not implicitly enable scheduling.
- **Hard production execution block:** CLI --execute refuses any database name not ending in \`_test\`. No main database can be mutated by this worker in the current rollout.
- The helper fails closed when the approvals table does not exist; it does not perform schema changes or automatic approvals.

## Operational impact
Until official approvals have been entered, new complaints will be **Registered / unassigned**. This is intentional and requires a staffed triage queue and approved mapping before any live deployment. Older complaint assignments are not modified. The separate staff-facing approval workflow and official SLA restoration are NOT yet implemented.

## Test steps — sandbox only
1. \`git pull origin feature/smartcity-pgp-integration\` and lint the edited PHP files.
2. Run \`D:\XAMPP\php\php.exe tests\test_officer_routing_guard.php\` to check prohibited title cases.
3. Back up and verify the sandbox database. In phpMyAdmin **select** \`smartcity_escalation_test\`, verify \`SELECT DATABASE()\`, then import \`database/officer_stage_approvals.sql\`. It only creates the empty table.
4. After authorized municipal staff sign off the EXACT category/stage/officer and corresponding category/department/ward maps, enter approval rows with real \`approved_by\` and \`approval_reference\`; never auto-generate approvals from existing seeded mappings.
5. Use an isolated complaint in test DB to verify no approval -> unassigned, approved field officer -> assigned, Commissioner/Officer #1 -> blocked; check all stages, manual transfer and history in DB.
6. Check \`escalate-complaints.php --dry-run\` and \`--execute --expect-db=smartcity_escalation_test\` **only on sandbox**. \`--execute --expect-db=smartcitytiruppur\` is explicitly prohibited by code.

## Not yet corrected
Legacy ICTS may have 5–6 stages, but the current worker has 4. 286 stage1 Commissioner mappings and 65 Officer #1 mappings remain flagged, not edited. \`complaint_categories.sla_days\` and the old public OTP bypass remain separate remediation tasks. Do NOT enable production scheduling or claim ICTS parity.

## QA caveat
The pure test covers title exclusions only; actual database-backed staging tests must be run locally after the sandbox migration. Existing 38 test suite results cannot be presumed to pass after this security change.
