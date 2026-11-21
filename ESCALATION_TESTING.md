# Smart City Tiruppur escalation: verified source comparison and safe test

## Findings

- PGP's `escalationController.php` manages department/stage settings through `sp_manage_escalation`. PGP's `departmentAllocationController.php` manages staff and sub-allocation settings, and its escalation report aggregates results from a database view. PGP's Laravel `app/Console/Kernel.php` has no active scheduled escalation command. A functioning PGP *automatic* escalation runtime was **not verified**.
- Smart City has its own implementation in `escalate-complaints.php`, using the **updated** `smartcitytiruppur.officers`, `officer_..._map`, `department_allocations`, `escalation_officer_levels`, `complaint_assignments`, `complaint_escalations` and `complaint_status_history`. No PGP officer data is migrated or used.
- Reported local data: 0 `department_allocations`, 224 officer department mappings, 1,383 category mappings, 2,345 ward mappings, and 1,224 active four-level escalation mappings.
- Prior runner could act on unassigned complaints, ignore later `due_at` values, select inactive next-stage officers, and had no dry-run or job concurrency guard.
- Existing SQL `database/department_allocations.sql` has a nullable `escalation_level_id` adjustment, but do **not** run that SQL blindly. The updated runner checks the actual column nullability and reports a SKIP if an exact department rule needs NULL but the table disallows it.

## Safety and execution

Default invocation is **read-only complaint logic**. `--execute` is required for actual writes; `--expect-db` ensures the intended DB is connected. The script is **CLI-only** to prevent browser-triggered execution.

Do NOT run `--execute` on your existing records until officer designations, current assignment and stage mappings are verified. Some escalation records were auto-seeded using the first category-mapped officers and may not correspond to the actual AE/AEE/EE/Commissioner hierarchy. Active officer does not imply correct designation/authority.

Use PowerShell:

```powershell
cd D:\XAMPP\htdocs\test\smartcitytiruppur
D:\XAMPP\php\php.exe -l .\escalate-complaints.php
D:\XAMPP\php\php.exe .\escalate-complaints.php --dry-run
D:\XAMPP\php\php.exe .\escalate-complaints.php --dry-run --complaint-id=8
```

`[SKIP]` explains each non-eligible complaint; `[WOULD ESCALATE]` displays the proposed next stage and officer. A dry run does not modify complaint assignments.

### Execution test in an isolated, disposable test database

1. Back up both databases. Restore Smart City into a **separate local test database** and point a *test copy* of `config/app.php` `DB_NAME` to the clone. Do not reconfigure your working website.
2. Verify the clone connection with `SELECT DATABASE()`. Review that mapped next-stage officers are genuinely authorized for the category and locality; do not use synthetic Officer ID 1 as a higher-officer fallback.
3. Create an isolated test complaint with a Stage 1 assignment, current assignment history, appropriate category and zone/ward. Set its test-only `due_at` to a past value. Do not alter real complaints 1, 2 or 8.
4. Run the dry run with its test complaint ID, inspect the intended officer and level.
5. **Only in the disposable test clone**, execute:

```powershell
D:\XAMPP\php\php.exe .\escalate-complaints.php --execute --expect-db=smartcity_escalation_test --complaint-id=TEST_ID
```

Replace `TEST_ID` with its actual numeric test ID and use the **actual clone database name** after `--expect-db=`.

6. Confirm the complaint's new officer/due date, one current assignment record, one new escalation row and a timeline/history entry. Run again; it must not escalate the same complaint twice immediately.
7. Test a resolved complaint, unassigned complaint, missing next-level rule, inactive next officer, identical current/next officer, and a complaint not yet due. Each should SKIP.
8. Keep the live scheduler **disabled** until all test and officer authorization checks pass.

### Read-only checks for the real local database

```sql
SELECT c.id,c.reference_no,c.status,c.assigned_officer_id,c.due_at
FROM smartcitytiruppur.complaints c WHERE c.is_deleted=0 ORDER BY c.id;

SELECT el.category_id, el.level_no, el.officer_id, o.officer_name, o.designation,
       el.escalation_period_days
FROM smartcitytiruppur.escalation_officer_levels el
JOIN smartcitytiruppur.officers o ON o.id=el.officer_id
WHERE el.is_active=1
ORDER BY el.category_id,el.level_no LIMIT 40;

SELECT column_name, is_nullable FROM information_schema.columns
WHERE table_schema='smartcitytiruppur' AND table_name='complaint_escalations'
AND column_name='escalation_level_id';
```

### Scheduling after approval

An unattended hourly job must use explicit `--execute --expect-db=smartcitytiruppur`, and run from Windows Task Scheduler or cron. The runner applies a MySQL named lock, row locking and a single transaction per complaint to avoid duplicate concurrent escalation records. Schedule only after a verified dry run and separate test. No page-load-triggered escalations.

### Semantics

- The configured `due_at` deadline takes precedence over stage-duration calculations, so a category-specific 34-day deadline cannot be overridden by a generic 3-day rule.
- The current complaint status stays unchanged on escalation (e.g. `Assigned`) but the history receives an `Auto-escalated to Stage …` entry.
- The script intentionally does **not** auto-escalate an unassigned complaint. Initial assignment/remediation is a separate workflow.
- The next officer must be active, distinct from the current officer, and mapped at the next stage. This still does NOT prove job title correctness: an administrator must verify officer roles.
