# Verified complaint allocation — controlled fix

## Confirmed cause
Public complaint registration inserts into \`complaints\` then calls \`assign_officer_for_complaint()\`. The main database has no \`officer_stage_approvals\` table; the new safety guard refuses to pick unverified staff. Citizen detail pages correctly show unassigned. **Do not bypass this guard or auto-fill official approval entries**.

## Changes
- Fallback officer search now examines up to 250 candidates per tier, checking explicit Stage 1 authority before selecting (not the first officer by ID).
- Exact Stage 1 department allocation is authoritative: if its officer is unapproved, assignment stops for review instead of silently applying another tier.
- New complaint creation verifies that a legacy BEFORE INSERT trigger did not assign an officer before the PHP guard ran; if it did, registration transaction fails and rolls back rather than committing unsafe assignment. The legacy trigger source is removed from the installation SQL, but any deployed trigger requires a separately approved migration.
- Citizen success and details show **pending authorized allocation** instead of implying an officer was selected. The complaint remains registered and trackable until legitimately assigned.
- Adds \`employee/officer-approvals.php\`: authenticated administrator screen with explicit category, stage, existing officer ID, written approval reference, audit trail, revocation, and a read-only review queue. It never auto-populates approvals or auto-assigns past complaints.
- Existing employee complaint update form continues to validate candidate approvals. The admin must review the specific complaint and manually assign an approved officer.
- No database changes, active trigger removal, or approval inserts are performed by a Git commit.

## Critical remaining prerequisites
1. Audit live triggers with read-only \`SHOW TRIGGERS WHERE Table = 'complaints'\`. Remove the old trigger only after verifying backup/change authorization. Do not run \`database/department_allocations.sql\` as an upgrade script.
2. Confirm real personnel category/dept/ward authority. Obtain signed municipal approval. Only then install the empty approval schema with backup and authorized change control. Do not copy approvals from sandbox.
3. Use the protected page to document approved officers and follow up on unassigned cases. Confirm that stage-level configuration and identity are correct; do not bulk-invert ID orders.
4. Restore official ICTS SLA timings separately. Production automatic escalation stays NO-GO.
5. Re-enable OTP verification prior to public deployment: current \`new-complaint.php\` contains a legacy temporary bypass, separate security defect.
6. Confirm test results with end-to-end PHP+MariaDB requests and verify record-level invariants in \`smartcity_escalation_test\` before merging.

## Read-only verification SQL (select database explicitly)
\`\`\`sql
SELECT DATABASE();
SELECT COUNT(*) FROM information_schema.TABLES
 WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='officer_stage_approvals';
SHOW TRIGGERS WHERE \`Table\` = 'complaints';
SELECT c.id,c.reference_no,c.status,c.assigned_officer_id,
       d.department_name,cc.category_name,c.due_at
FROM complaints c
JOIN departments d ON d.id=c.department_id
JOIN complaint_categories cc ON cc.id=c.category_id
WHERE c.reference_no='TCMC-2026-576865' LIMIT 1;
\`\`\`

## Sandbox regression cases
- Registration without approval → Registered, no assigned officer, initial status history recorded.
- Registration with valid synthetic Stage1 approval → Assigned to reviewed synthetic field officer, one current assignment.
- Commissioner/Officer#1 regardless of historical mapped order → blocked.
- Unapproved first candidate then approved second candidate in the same tier → approved second selected.
- Trigger installed and exact allocation → registration rolled back, no unsafe committed assignment.
- Trigger absent and approved allocation → assigned through PHP, correct \`complaint_assignments\` and status history.
- Admin with proper permissions can add and revoke explicit approvals; unauthorized users cannot.
- Manual assignment of an unassigned case requires approved category/stage officer.
- Resolved, rejected, deleted and malformed category/zone cases remain safely handled.
