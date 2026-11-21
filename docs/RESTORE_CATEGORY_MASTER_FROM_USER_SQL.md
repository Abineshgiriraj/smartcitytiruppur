# Repair Complaint Categories from the supplied SQL dump

Source: user-uploaded smartcitytiruppur.sql (phpMyAdmin export, 19 Nov 2026).
Only departments (14) and complaint_categories (306) were extracted, not citizen or credential records.

Main database screenshot: 308 complaint category rows, changed department IDs, many inactive categories, incorrect 1-day SLA values.
Do not DROP, TRUNCATE, or raw-import the table: complaints, officer_category_map, department_allocations and escalation_officer_levels refer to category IDs.

Files:
- database/category_master_from_uploaded_sql.json — original uploaded department/category rows.
- tools/restore_uploaded_category_master.php — safe, read-only-by-default CLI synchronizer.

Working directory: D:\XAMPP\htdocs\test\smartcitytiruppur

1. git pull --ff-only origin main
2. D:\XAMPP\php\php.exe .\tools\restore_uploaded_category_master.php
3. Inspect the preview, ensure local DB backup exists and that the app is connected to local XAMPP.
4. D:\XAMPP\php\php.exe .\tools\restore_uploaded_category_master.php --apply-local --confirm=RESTORE_UPLOADED_CATEGORIES
5. Run preview again. Expected: insert=0 update=0 deactivate_extras=0.
6. Hard-refresh http://localhost/test/smartcitytiruppur/new-complaint.php

Expected: Engineering -Town plan shows 29 categories (Building Plan 8 days); Water Supply shows 34.
eGovernance shows Online Payment and online Receipt at 3 days.

Details:
- Matches departments by NAME, not database ID.
- Matches categories by department name AND category name, retaining existing main category IDs.
- Existing matching records: sync exact source name, SLA and active flag.
- Missing records: insert new IDs assigned by MariaDB.
- Extra historical records: deactivate, never delete (to preserve references).
- Full pre-change category and department snapshot saved outside webroot in OS temp directory.
- Uses InnoDB transaction, row locks, local-only DB safety checks, source SHA256 fingerprint, postwrite verification.
- Does not alter complaints, officer assignments, escalation rules, officer approvals, auth, or job schedules.
- One category in supplied source has SLA=0. Sync copies exact source. Separately validate ICTS deadlines.
- Auto-escalation remains NO-GO; do not deploy category sync to live MariaDB without approved change control.

Never import the uploaded full SQL dump into an existing populated main database.
