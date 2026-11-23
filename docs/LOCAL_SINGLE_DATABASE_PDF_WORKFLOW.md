# Single local database workflow: smartcitytiruppur

The app's `config/app.php` is already configured with
`DB_HOST=127.0.0.1`, `DB_PORT=3306`,
`DB_NAME=smartcitytiruppur` and `BASE_URL=/test/smartcitytiruppur`.
**Do not copy** `smartcity-pdf-test/config/app.php` into this main project.

Changes in this branch:
- `includes/local-pdf-workflow-guard.php` checks that the CLI workers
  connect to **smartcitytiruppur**, that `--expect-db` agrees with actual
  connection, and (for writes) that the PHP CLI runs locally from the
  `smartcitytiruppur` folder in a local environment.
- `retry-pending-allocations.php` and `pdf-escalate-complaints.php`
  now support the main local DB. Both still require PDF-reviewed policy,
  legitimate officer/stage approvals and correct department/category/ward
  matches. They refuse to guess or fabricate approvals.
- `tools/run_pdf_workflow_readonly.cmd` and
  `tools/run_pdf_workflow_test.ps1` now target this local DB.
- The legacy `smartcity_escalation_test` DB, older test worktree and
  scripts are **not deleted** or modified. Keep the clone/backups for
  rollback until the main local version is verified.

## Verify without changing any complaints

```powershell
cd D:\XAMPP\htdocs\test\smartcitytiruppur
git pull --ff-only origin feature/pdf-escalation-6-level
D:\XAMPP\php\php.exe -l .\includes\local-pdf-workflow-guard.php
D:\XAMPP\php\php.exe -l .\retry-pending-allocations.php
D:\XAMPP\php\php.exe -l .\pdf-escalate-complaints.php
.\tools\run_pdf_workflow_readonly.cmd
```

The CMD script defaults to read-only queue previews and does **not**
create a Windows scheduled task or change an officer record.

For a complaint known to exist in this DB:

```powershell
D:\XAMPP\php\php.exe .\tools\diagnose_complaint_allocation.php --expect-db=smartcitytiruppur --reference=YOUR_COMPLAINT_REFERENCE
```

You can also view
`http://localhost/test/smartcitytiruppur/employee/user-mapping-details.php`
and
`http://localhost/test/smartcitytiruppur/employee/pdf-policy-review.php`.

## Preconditions to actually allocate complaints

1. Source PDF stages for the exact category must be reviewed and
   appropriately approved. The 301 rows imported into `smartcitytiruppur`
   were initially **draft**; that is not the same as the separately
   approved policy in the former test DB.
2. For Stage 1, locate the real employee with the approved AE(ELECTRICAL)
   role for the exact complaint category, department and ward. Source
   main DB officer IDs must be used without translation: do not use
   old `smartcity_escalation_test` officer ID #4.
3. The official approving administrator records authorized stage
   approval and verifies the exact ward scope. Do not manufacture a
   reference or bulk approve all staff.
4. If approved and unique, the registration endpoint already attempts
   auto-allocation in its transaction. Existing Registered/unassigned
   complaints can be retried from the authorized complaint-view form.
5. If all prerequisites are verified, explicit CLI
   `--execute --expect-db=smartcitytiruppur` now works **only** on the
   local main project. Confirm backups and select a single complaint
   ID first, check assignment history, and only then expand rollout.
   **No scheduler has been installed or enabled.**
6. Escalation requires further-stage genuine approvals, a compatible
   `complaint_escalations.escalation_level_id` schema, complete assignment
   history, and consistent per-stage due times. The worker will skip
   ineligible/ambiguous cases. Do not enable it on live production.

The optional PowerShell runner may still be blocked by Windows execution
policy; the `.cmd` read-only runner needs no policy change.

Note: this app is only confirmed to be pointing to a locally configured
database; GitHub cannot directly inspect the user's localhost DB.
Confirm actual connection with CLI before running any write.
