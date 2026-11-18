# Smart City Tiruppur + PGP database consolidation

**Target database:** `smartcitytiruppur`. **Current status:** two original databases are retained unchanged. The PGP dashboard can read both but deliberately never sums them because complaints may overlap.

## Why not just import pgp into smartcitytiruppur?

The standalone portal stores complaints in `smartcitytiruppur.complaints` with text statuses, numeric primary/foreign keys, and municipality-specific tables. The previous Laravel app stores cases in `pgp.new_complaints` with numeric statuses and string `unique_id` keys. User and permission tables also differ. Direct replacement can orphan assignments or silently change permissions.

## Read-only inventory (run in phpMyAdmin SQL tab, same local MySQL server)

```sql
SELECT table_schema AS database_name, COUNT(*) AS table_count
FROM information_schema.tables
WHERE table_schema IN ('pgp', 'smartcitytiruppur')
  AND table_type = 'BASE TABLE'
GROUP BY table_schema;
```

List table-name collisions and assess their schemas:

```sql
SELECT p.table_name, p.table_rows AS pgp_estimated_rows,
       s.table_rows AS smartcity_estimated_rows
FROM information_schema.tables p
JOIN information_schema.tables s ON s.table_name = p.table_name
WHERE p.table_schema = 'pgp' AND s.table_schema = 'smartcitytiruppur'
  AND p.table_type = 'BASE TABLE' AND s.table_type = 'BASE TABLE'
ORDER BY p.table_name;
```

**Note:** information_schema table_rows is approximate for InnoDB. Use direct COUNT queries for exact figures.

```sql
SELECT 'PGP all' AS source, COUNT(*) AS records FROM pgp.new_complaints
UNION ALL
SELECT 'PGP active', COUNT(*) FROM pgp.new_complaints WHERE is_delete = 0
UNION ALL
SELECT 'Smart City all', COUNT(*) FROM smartcitytiruppur.complaints
UNION ALL
SELECT 'Smart City active', COUNT(*) FROM smartcitytiruppur.complaints WHERE is_deleted = 0;
```

Compare relevant source statuses without guessing their mappings:

```sql
SELECT status, COUNT(*) AS records FROM pgp.new_complaints WHERE is_delete = 0 GROUP BY status ORDER BY status;
SELECT status, COUNT(*) AS records FROM smartcitytiruppur.complaints WHERE is_deleted = 0 GROUP BY status ORDER BY status;
```

## Safe migration plan

1. Take verified, restorable backups of BOTH databases. Keep them outside the Git repository.
2. Inventory every PGP table, keys, views, triggers, stored routines, FKs and table-name collision. Determine whether each module is actually required.
3. Compare PGP and Smart City complaints using validated grievance IDs and other fields to identify duplicates; never infer uniqueness solely from complaint counts or phone numbers.
4. Build **versioned, namespaced staging tables** in a separate disposable test database; validate record counts and key relationships, and map statuses, departments, wards and user IDs.
5. Test a clean deployment and all required routes against the unified *test* database; verify permissions and attachments. Prepare an explicit rollback.
6. Only after approval, perform the reviewed migration to the destination `smartcitytiruppur` with a controlled maintenance window, and update Laravel/PHP database connection settings. Reconcile dashboard totals.
7. Keep the original `pgp` database as a read-only backup until reconciliation, tests and rollback retention are complete. Do not drop it automatically.

## Dashboard source configuration

PGP retains `DB_DATABASE=pgp`. The new Laravel connection named `smartcity` has independent optional environment settings:

```ini
SMARTCITY_DB_HOST=127.0.0.1
SMARTCITY_DB_PORT=3306
SMARTCITY_DB_DATABASE=smartcitytiruppur
SMARTCITY_DB_USERNAME=root
SMARTCITY_DB_PASSWORD=
```

Use your actual **local** database user/password. Never copy an existing production password into Git. For access isolation, use a dedicated SELECT-only MySQL account for `SMARTCITY_DB_USERNAME`. This configuration does not merge tables and does not change the standalone PHP website.
