# Complaint Category Master — source-vs-database repair

## Confirmed reason the Town Plan dropdown shows only Building Plan
The current main website loads all category choices from the same MariaDB connection used for departments (no AJAX needed). The screenshot shows only **Building Plan (1 day)** under **Engineering -Town plan**. Yet historical `database/data_import.sql` contains **29** active, non-placeholder categories under that department. For example, Building Plan has **8 days** in this historical source. That is evidence of an incomplete/different or inactive category master in the connected main database, not a rendering defect. The main database state has NOT been inspected directly by the agent.

The source import has 306 categories across 14 departments; 302 are active non-placeholder entries. It is a historical reference, not proof that its SLA days are currently approved.

## What was added
- `database/category_catalog_reference.json`: exact source catalog (non-placeholder entries), with department names rather than hard-coded current database IDs.
- `tests/audit_category_catalog.php`: read-only comparison by department name AND category name; reports missing, inactive, and historical-SLA differences. Requires exact --expect-db and aborts if actual DB is different.
- `--apply-test`: inserts ONLY missing entries into **smartcity_escalation_test** after manual backup; never changes existing category names, IDs, SLA, active flags, complaint records, officer routing, or history. Requires approval of using historical SLA as test fixture.
- **No automatic migration of the main database.** Do not import the entire `database/data_import.sql` on an existing installation: it contains `USE` statements, large import data, DROP TABLE and other changes.

## Commands — READ-ONLY MAIN
`D:\XAMPP\php\php.exe tests\audit_category_catalog.php --expect-db=smartcitytiruppur --department="Engineering -Town plan"`

To check every department:
`D:\XAMPP\php\php.exe tests\audit_category_catalog.php --expect-db=smartcitytiruppur`

## Optional sandbox-only restoration
**After confirming backup and that CLI config points to smartcity_escalation_test**, run:
`php tests\audit_category_catalog.php --expect-db=smartcity_escalation_test`
`php tests\audit_category_catalog.php --expect-db=smartcity_escalation_test --apply-test`

Re-run audit and verify Town Plan count. Synthetic categories added to sandbox by this action may not have valid officer approvals; complaint submissions can remain unassigned by design. Do not use sandbox category IDs as production IDs.

## For a controlled main DB repair
1. Back up `departments` and `complaint_categories`; retain foreign-key mappings and existing complaints.
2. Run read-only main audit and inspect missing/inactive/misdepartment records and existing incorrect SLAs. Obtain authorized category catalog and SLA policy.
3. Prepare an additive **missing-only** SQL transaction using real current department IDs; do not overwrite/renumber category IDs or alter referenced categories.
4. Test the same migration against a fresh copy of production, inspect open complaint routing, and get sign-off before applying to main.
5. Re-test department -> category dropdown and read-only audit for all departments.
6. Automatic escalation remains disabled; officer approval registry and correct ICTS SLA mapping are separate issues.
