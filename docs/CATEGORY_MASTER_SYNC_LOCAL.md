# Restore missing complaint categories in LOCAL XAMPP database
The public category dropdown is properly filtered by department, but the main
database shown in screenshots contains only one Town Plan category ("Building Plan",
SLA 1 day). The committed historical import includes 29 active Town Plan categories
("Building Plan" historically 8 days). The UI cannot display missing database rows.

This script synchronizes MISSING category names from the already versioned
`database/category_catalog_reference.json` (302 original non-placeholder
categories) into `smartcitytiruppur.complaint_categories`. It looks up department
IDs by name, **does not copy source numeric IDs**, preserves all existing
categories, active flags, SLA values, complaints, mappings and history.

**Run a read-only preview first:**
```powershell
D:\XAMPP\php\php.exe .\tools\sync_missing_categories.php --expect-db=smartcitytiruppur
```

**Apply to LOCAL XAMPP ONLY, with explicit confirmation:**
```powershell
D:\XAMPP\php\php.exe .\tools\sync_missing_categories.php --expect-db=smartcitytiruppur --apply-local --confirm=ADD_MISSING_CATEGORY_ROWS
```

It requires `APP_ENV=local`, a local DB host, and an exactly matching selected
database. Before any insertion it writes a complete JSON snapshot of current
departments and categories under the OS temporary directory (outside webroot).
The script uses a transaction and named lock and inserts only rows that don't
already exist (checked by SQL collation). Missing or inactive departments block
the apply. Existing inactive categories are not reactivated; existing SLA
differences are reported and left untouched.

The newly inserted category SLA days come from a **historical** normalized
import, not independent official ICTS verification; the official SLA audit and
municipal category authorization must still occur before deployment.

Do NOT run `database/data_import.sql` on a populated database; it contains
`USE`, `DROP TABLE` and other destructive/full-import statements.

Do NOT run this command on production MariaDB or a remote server. Keep
automatic escalation disabled. This script does not approve officers, fix OTP,
or restore all workflows automatically.
