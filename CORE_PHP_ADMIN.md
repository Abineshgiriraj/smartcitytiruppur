# Smart City Tiruppur — Core PHP admin

The project now runs its citizen website and employee administration in **Core PHP + PDO + MySQL**. The PGP Laravel subtree was removed from this development branch; a Git backup retains the original code.

## Preserved application

- Public registration, tracking, complaint view, uploads, contact pages
- Employee login and role restrictions
- Complaint inbox, detail, reports, feedback, users and master-data operations
- Smart City officer master and mapping tables
- CLI complaint escalation processor and its safe dry-run (not automatically scheduled)

## Advanced admin improvements

- Responsive left sidebar and topbar shared by existing PHP employee pages
- KPI-driven corporation overview with role-scoped data, monthly chart, department breakdown and recent complaints
- Read-only officer directory to inspect Smart City officer mapping coverage
- Read-only escalation monitor with stage rules, overdue complaints and recent escalation events

No Laravel packages, Artisan, Blade, PGP controllers, or PGP database connection are required for this admin.

## PGP database — DO NOT DROP WITHOUT VERIFIED BACKUP

PGP previously contained approximately **34,746** complaint records, while the Smart City database contained 3. The PGP complaint history has **not** been migrated. Deleting the PGP database before retaining and verifying an archival backup can result in permanent loss of the only copy of that history.

To retire it safely:
1. Confirm this is the local PGP DB, not a production DB.
2. Export the complete PGP DB including tables, views, routines and triggers. Store the dump **outside the web root and Git repo**.
3. Verify the backup can be restored to a disposable database; compare complaint row counts and attachments stored outside MySQL.
4. Search all local configs, scheduled tasks, other applications, and reports for connections to DB \`pgp\`.
5. Only after sign-off, run \`DROP DATABASE pgp;\` manually in phpMyAdmin, never as part of the PHP admin or startup.

## Install the changes locally

From the project folder on the development branch:

\`\`\`powershell
git status
git pull origin feature/smartcity-pgp-integration
D:\XAMPP\php\php.exe -l .\includes\employee-header.php
D:\XAMPP\php\php.exe -l .\employee\dashboard.php
D:\XAMPP\php\php.exe -l .\employee\officers.php
D:\XAMPP\php\php.exe -l .\employee\escalation.php
\`\`\`

Do not pull into a directory where \`pgp/\` contains local uncommitted changes until safely backed up. Also verify authentication, role-scoped dashboards, filters, writes and complaint history with a disposable test database before deploying to production.
