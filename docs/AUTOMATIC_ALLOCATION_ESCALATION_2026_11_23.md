# Automatic allocation and PDF escalation — integration audit (23 November 2026)

## What can be verified

- Reference ICTS public site: https://smarttiruppur.com/ and the publicly listed escalation **total** periods at https://smarttiruppur.com/frmDCCEscalation.aspx.
- Authenticated ICTS `Transactions/frmwelcome.aspx` cannot be verified from repository access or public pages. Its officer mapping/approval semantics must be confirmed using properly authorized legacy administrator records.
- PR #2 already contains the 301-category, 1,409-stage reference policy and test-only PDF escalation engine. This code does **not** synchronize live ICTS officer identities or database.
- Public registration (`new-complaint.php`) calls `assign_officer_for_complaint` *inside* its transaction. If approval/policy/mappings are absent, it leaves the complaint Registered/unassigned.
- The registration database has previously been reported without an `officer_stage_approvals` table. Recheck the **actual target database** before concluding why any specific current complaint is pending.

## Fixes in this branch

1. `includes/functions.php`: initial assignment refuses to act on a non-Registered, deleted, already-assigned or historically escalated complaint. It checks current assignment state and locks the complaint in the caller transaction.
2. An **active exact** Stage 1 departmental allocation pointing to a missing/inactive/unapproved officer cannot be masked by falling back to some other officer.
3. When no exact rule exists, search approved candidates by ward priority. If several eligible officers share the best ward priority, do **not** pick the lowest arbitrary ID: leave the complaint pending until an exact authorized rule is established. The one-candidate case remains automatic.
4. `retry-pending-allocations.php`: a bounded, read-only default queue scan reports pending complaints. `--execute` calls the same verified allocator with row locking and a database advisory lock; execution is strictly limited to `smartcity_escalation_test`. The dry-run reports only readiness, not a guaranteed officer.
5. No policy or officer approvals have been invented; no production DB was accessed or modified; the legacy four-stage worker remains disabled.

## Current workflow

```text
Citizen registers complaint
  -> department + category + ward/zone verified
  -> reviewed, officially approved category PDF policy
  -> Stage 1 officer approval, correct designation and map
  -> exact active ward-stage rule OR uniquely eligible verified officer
  -> single active assignment; Stage 1 deadline and history
  -> scheduled worker (test only for now)
  -> each missed stage deadline: next approved PDF stage, audited transfer
  -> stop at final stage (1 through 6 depending on category)
```

**Important:** Categories with zero-day stage definitions are intentionally unapprovable until formally resolved. This is not a reason to silently turn zero days into one day.

## Safe local verification

In the standalone test worktree (do not change production config):

```powershell
cd D:\XAMPP\htdocs\test\smartcity-pdf-test
git fetch origin
git pull --ff-only origin feature/pdf-escalation-6-level

D:\XAMPP\php\php.exe -l .\includes\functions.php
D:\XAMPP\php\php.exe -l .\retry-pending-allocations.php
D:\XAMPP\php\php.exe -l .\pdf-escalate-complaints.php

# Read-only, against explicitly selected database:
D:\XAMPP\php\php.exe .\tools\diagnose_complaint_allocation.php --expect-db=smartcity_escalation_test --reference=TCMC-2026-950323
D:\XAMPP\php\php.exe .\retry-pending-allocations.php --dry-run --expect-db=smartcity_escalation_test --limit=100
D:\XAMPP\php\php.exe .\pdf-escalate-complaints.php --dry-run --expect-db=smartcity_escalation_test
```

The example reference may not exist in the test clone; use a known synthetic complaint if it reports "not found".

Before any **test-only** execution, confirm:
- `SELECT DATABASE()` equals `smartcity_escalation_test`.
- A backup exists and the correct schema migrations have been reviewed/applied to the test clone.
- Valid signed approvals and mapping exist for the *specific* test category, stage, officer, ward and designation.
- The test case starts Registered/unassigned with no active assignment or escalation history.

Then **test clone only**:

```powershell
D:\XAMPP\php\php.exe .\retry-pending-allocations.php --execute --expect-db=smartcity_escalation_test --complaint-id=TEST_ID
D:\XAMPP\php\php.exe .\pdf-escalate-complaints.php --dry-run --expect-db=smartcity_escalation_test --complaint-id=TEST_ID
```

`TEST_ID` is a placeholder; use the actual integer test complaint ID.

## Unresolved blockers for actual ICTS parity and live scheduling

- Official legacy ICTS DCC mappings, staff designations and signed stage/ward delegation; public pages only prove total SLA periods.
- Test migration readiness and 301/306 category exceptions.
- Authority conflicts where existing exact allocation points to the wrong officer, including the missing/inactive case.
- The PDF uses 1–6 stages; legacy 4-stage task must not be scheduled.
- Reliable job scheduling, heartbeat, failure alerting and idempotency under concurrent approvals and transfers require deployment QA.
- Public OTP validation is still commented out in `new-complaint.php`; must be enabled before going live.
- Existing records remain untouched. A production migration and rollback plan need independent municipal signoff.

Do **not** merge/activate or point a Windows Task Scheduler job at the production database on the basis of a code-only review.
