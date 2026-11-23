# PDF-guided automatic allocation/escalation: test rollout

Source: `21.11.26(1).pdf` (SHA-256
`03a496809403a2de6a597a844a36ffc979de47657541342924628c8d33d70c4b`).
It matches the approved source fingerprint hard-coded in
`includes/pdf-escalation-policy.php` and the repository's existing
`database/pdf_escalation_policy.json`. The document defines 301
category-specific workflows and 1,409 stage rows; it does **not** specify
employee IDs, login accounts, ward mappings, appointments, or authorization
numbers.

Example: Engineering - Street Light / Others, source row 24,
AE(ELECTRICAL) 2d -> AEE 1d -> EE 1d -> CE 1d ->
DEPUTY COMMR 1d -> COMMR 1d (7 days).

## Software changes

- The initial assignment code in `includes/functions.php` requires an approved
  category policy and real approved officer for Stage 1; its deadline is the
  Stage 1 period, not `complaint_categories.sla_days`.
- The PDF escalation worker `pdf-escalate-complaints.php` now selects overdue
  assigned complaints first in automatic/batch runs. The former selection
  scanned the first 500 assigned cases irrespective of their due date and
  could starve overdue records.
- An exact active next-stage allocation rule is authoritative even when one
  approved alternative exists. Wrong officer or wrong SLA means **skip for
  review**, not silently route around the exact rule.
- `retry-pending-allocations.php` can reattempt Registered/unassigned
  complaints after legitimate approvals are recorded. Both jobs use
  transactions/locks and are limited to `smartcity_escalation_test`
  for actual writes.
- `tools/check_pdf_automation_readiness.php` displays each official stage
  and whether the matching approved officer / ward rule exists.
- `tools/run_pdf_workflow_test.ps1` combines the two jobs with error handling
  and a log. It defaults to --dry-run, writes to local log file only, and
  never enables a Windows scheduled task on its own.
- The legacy fixed-four-stage `escalate-complaints.php` remains disabled
  for execution. Do not schedule it.

## Local validation (no database changes)

On the verified **test** worktree:

```powershell
cd D:\XAMPP\htdocs\test\smartcity-pdf-test
git pull --ff-only origin feature/pdf-escalation-6-level

D:\XAMPP\php\php.exe .\tests\test_pdf_escalation_policy.php
D:\XAMPP\php\php.exe .\tests\test_pdf_category_coverage.php
D:\XAMPP\php\php.exe -l .\pdf-escalate-complaints.php
D:\XAMPP\php\php.exe -l .\tools\check_pdf_automation_readiness.php

D:\XAMPP\php\php.exe .\tools\check_pdf_automation_readiness.php --expect-db=smartcity_escalation_test --reference=TCMC-2026-950324
powershell -NoProfile -File .\tools\run_pdf_workflow_test.ps1
```

The readiness command reports NOT READY (exit code 3) if any stage lacks
sufficient authorized routing. The test runner's default is dry-run, and
will **not** allocate complaints or escalate them.

## Isolated test execution after approvals and backup

Once the municipal admin has reviewed the correct employee identities,
appointments, department/category/ward scope and approval references, and
test-only records are prepared and backed up, a person with permission may
run:

```powershell
powershell -NoProfile -File .\tools\run_pdf_workflow_test.ps1 -ExecuteTest
```

This command writes ONLY to the explicitly configured
`smartcity_escalation_test` DB (enforced inside both workers).
It attempts eligible registered assignments, then any overdue authorized
next-stage transitions. Ineligible entries are skipped, not guessed.
Run it manually first, verify current assignment and status history
and escalation rows. Do not simulate municipal approvals as if official.

## Production is intentionally not scheduled

Real live deployment still requires:
- Approved identities and per-category/per-stage authority.
- Reconciliation of the legacy User Mapping Details filter inconsistency:
  user screenshot selected Street Light / Ward 53 but result grid said Ward 57;
  the target complaint is category **Others** / Ward 53.
- Explicit first-stage officer plus later-stage officer mapping for all
  intended categories and wards, verified official designations.
- Backup/rollback plan, scheduler permissions and logging/alerts, end-to-end
  DB tests, and municipal change authorization.
- Safe production rollout implementation reviewed independently. Neither
  job's `--execute` currently accepts the production DB.

No modification to production databases or live appointments was performed
as part of these GitHub commits.
