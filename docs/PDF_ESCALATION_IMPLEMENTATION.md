# Smart City Tiruppur — PDF-specific escalation repair

**Source:** uploaded `21.11.26.pdf` (15 pages; 301 category workflows; 1,409 stage rows). This is an *implementation package for an isolated test worktree*, not an authorized production deployment.

## What differs from the old implementation

- The old engine used a fixed 4-stage chain, despite categories needing 1, 2, 3, 4, 5 or 6 stages.
- The old seed sorted officers by numeric IDs, sometimes placing Commissioner as Stage 1; that is not a real municipal hierarchy.
- `complaint_categories.sla_days` generally stores a **total** period; initial assignment must use **PDF Stage 1 days** instead.
- Later stages must use **their own PDF period**, rather than generic or legacy fallback periods.
- Stage 1 and later stage officers must be explicitly verified by the municipality; the PDF contains designations, **no actual officer IDs or authorization documents**.
- `officer_stage_approvals` was missing from the user's main DB at the previous read-only audit; a PDF by itself does not cure that.

## Verified extraction summary

301 rows, 1,409 stage entries. All 301 rows match categories in the previously uploaded `smartcitytiruppur.sql` (306 category rows); the five extras are four `-` placeholders and `UGD-Others` in Public Health, none of which is assigned a PDF workflow. Distribution: 16 one-level, 1 two-level, 3 three-level, 104 four-level, 96 five-level, 81 six-level workflows. All source totals equal the sum of their stage periods.

Reference cases:

- **Poor Lighting / Engineering - Street Light:** AE(ELECTRICAL) 2, AEE 1, EE 1, CE 1, Deputy COMMR 1, COMMR 1, total **7**.
- **Building Plan / Engineering -Town plan:** AE(TP) 4, AEE 1, EE(PLAN) 1, Deputy COMMR 1, COMMR 1, total **8**.
- **New streetlight / New Infrastructure - streetlight:** AE/JE 30, AEE 2, EE 2, CE 2, **AE 0**, COMMR 1, total **37**. This unusual 0-day Level 5 is preserved exactly and **blocks policy approval** until officially corrected.
- **Water Supply HSC Name Correction:** last Commissioner stage is **0 days**; similarly blocked.
- **Biometric Related / PDS:** NODAL OFFICER 5 days and no escalation (terminal Stage 1).
- **Conductor Misbehaving / Transport Department:** Deputy Manager of Commercial 6 days and no escalation (terminal Stage 1).

Two wrapped cells on PDF page 1 were visually verified and normalized for rows #1 and #2 (Accounts).

## What each file does

- `database/pdf_escalation_policy.json`: complete stage policy (from PDF, not generated employee IDs).
- `database/pdf_escalation_policy.csv`: easy human review (1,409 rows).
- `database/pdf_escalation_policy_schema.sql`: category-to-policy and policy-to-stages tables, initially **unapproved**.
- `database/upgrade_test_stage6.sql`: separately reviewed MariaDB schema update for stage 5/6 approvals and nullable escalation level FK.
- `includes/pdf-escalation-policy.php`: policy validation, policy loading, role matching.
- `tools/import_pdf_escalation_policy.php`: read-only audit and **test-only** import, never populates officer approvals.
- `tools/apply_pdf_workflow_code.php`: **test-only** source patcher for old Core PHP files; backs up prior code; doesn't touch DB.
- `employee/pdf-policy-review.php`: Super Admin signoff page (only after official policy verification; no zero-day stage allowed).
- `pdf-escalate-complaints.php`: corrected sandbox worker, strict policy and approved-officer checks, per-stage deadline, terminal stage stop, transactional audit.
- `tests/test_pdf_escalation_policy.php`: 18 read-only PDF extraction and officer-role regression assertions.

## Install into ISOLATED test worktree — Windows PowerShell

1. Back up `smartcitytiruppur` and `smartcity_escalation_test`. Keep production scheduler disabled.
2. From your original Git repository create a new working tree at:

   ```powershell
   cd D:\XAMPP\htdocs\test\smartcitytiruppur
   git fetch origin
   git worktree add -b local-pdf-policy-test D:\XAMPP\htdocs\test\smartcity-pdf-test origin/main
   ```

3. Expand the attached package **into `D:\XAMPP\htdocs\test\smartcity-pdf-test`**. It contains new files only. No original source will be overwritten during extraction.
4. Change **only the test worktree** `config/app.php`:

   ```php
   const BASE_URL = '/test/smartcity-pdf-test';
   const DB_NAME = 'smartcity_escalation_test';
   ```

5. Validate code patches before applying:

   ```powershell
   cd D:\XAMPP\htdocs\test\smartcity-pdf-test
   D:\XAMPP\php\php.exe .\tools\apply_pdf_workflow_code.php
   D:\XAMPP\php\php.exe .\tools\apply_pdf_workflow_code.php --apply-test
   D:\XAMPP\php\php.exe .\tests\test_pdf_escalation_policy.php
   D:\XAMPP\php\php.exe .\tests\test_pdf_category_coverage.php
   ```

6. In phpMyAdmin select **smartcity_escalation_test**. After verified backup/import review, apply `database/pdf_escalation_policy_schema.sql`. If the empty `officer_stage_approvals` already exists, inspect `SHOW CREATE TABLE officer_stage_approvals` then review `database/upgrade_test_stage6.sql`. The source file contains SQL ALTERs, and must never be run against the main DB by mistake.
7. Preview then import the PDF definitions (safe test DB, creates draft/unapproved policy only):

   ```powershell
   D:\XAMPP\php\php.exe .\tools\import_pdf_escalation_policy.php --expect-db=smartcity_escalation_test
   D:\XAMPP\php\php.exe .\tools\import_pdf_escalation_policy.php --expect-db=smartcity_escalation_test --apply-test
   ```

   If category/department names do not match, the import **aborts** rather than inventing links. Report unmatched rows for explicit reconciliation.

8. **Authorized Super Admin** reviews each policy and records real signed policy reference in `employee/pdf-policy-review.php`. Zero-day policies cannot be approved until corrected from an official source. This step **does not** assign any officer.
9. In `employee/officer-approvals.php`, explicitly approve only the real municipal officer for each stage/category after verifying designation, category and ward scope and signed authority. Commissioner is only a valid *PDF-designated* stage and never Stage 1.
10. Test an isolated synthetic complaint. Initial assignment uses PDF **Stage 1**, assigns only an approved officer and sets `due_at` for Stage 1. Stage progression uses the new worker:

    ```powershell
    D:\XAMPP\php\php.exe .\pdf-escalate-complaints.php --dry-run --expect-db=smartcity_escalation_test --complaint-id=900060
    ```

    If and **only if** fully verified in a test clone, `--execute --expect-db=smartcity_escalation_test --complaint-id=...` is available. The worker refuses execution against `smartcitytiruppur` regardless of CLI arguments.

11. Verify actual assignment history (one active record), stage log, deadline and final stage. Re-run dry-run at the final stage and verify **TERMINAL_STAGE**. The old 4-stage worker is disabled in the test worktree.

## Strict NO-GO for production

This package is **not deployed**. The exact real officer identities, approval references, incomplete historical `complaint_assignments`, stage mapping completeness, zero-day discrepancies, and 301/306 PDF-category coverage cannot be solved from the PDF alone. There is no safe way to say that every new complaint is automatically assignable or that production escalation is proven end-to-end based on this document.

Do **not** run `--execute` on the main database, migrate/overwrite the old officer mapping rows, bulk-approve officer #1, or silently reinterpret PDF zero-day rules. Existing complaint records and previously recorded stage deadlines are not changed.
