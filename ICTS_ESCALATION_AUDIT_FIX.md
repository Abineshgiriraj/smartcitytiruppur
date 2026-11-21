# Escalation audit improvement / source-data safety

Verified from the current UI: 1,232 active category-stage rules; 1,232 title-label differences; 65 missing department/category mappings; no inactive stage officers and no blank recorded titles.

Root cause in the previous \`database/escalation_officer_levels.sql\`: it automatically assigned generic AE/AEE/EE/Commissioner labels to the first four officer IDs in each category map (or fallback officer #1), then used ON DUPLICATE KEY UPDATE to overwrite existing assignments if rerun. The bulk seed INSERT has been removed: running the SQL file now only creates missing tables and does not seed or overwrite stage mappings.

The revised \`employee/escalation-audit.php\`:
- Defaults to *Technical / mapping issues* instead of flagging every role-label discrepancy as a technical error.
- Counts and filters missing maps, inactive officers, invalid SLA, blank names and duplicate officer assignments across stages in the same category.
- Keeps a separate *Title differences* review queue and highlights common template-stage labels.
- Separates technical flags from manual personnel review in the table and CSV export.
- Preserves read-only queries, role permissions, bounded report size and CSV escaping.

Existing records are **not corrected** or changed by this source update. Titles cannot safely be auto-corrected, and no replacement officer can be inferred from IDs or generic escalation labels. Department/HR-approved stage-authority mapping is required before updating assignments. Exact ward-stage \`department_allocations\` rules are independent and must be reviewed separately. Never schedule the live escalation engine until approval and testing.

## Local checks
- Pull on development branch, lint modified PHP files, open Escalation Audit as Super Admin.
- Check default technical view, Missing Mapping, Repeated Stage Officer, Title Differences and All Active Rules.
- Verify CSV and read-only behavior. No form should update officer data.
- Check old \`database/escalation_officer_levels.sql\` no longer contains a bulk INSERT.
