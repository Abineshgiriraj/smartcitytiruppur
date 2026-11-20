# Escalation Configuration Audit — ICTS Admin (read-only)

This module was added in response to the authenticated Smart City inspector screenshot showing active category-stage mappings where **configured role labels** and **recorded officer designations** differ. In the example displayed, Stage 2 was labeled AEE but the mapped officer had designation ACCOUNTANT. The source data also showed 12,494 officers, 124 unnamed, zero linked employee accounts and zero active exact allocation rules.

## New screens and checks

- \`employee/escalation-audit.php\`: Super Admin/Admin only, uses \`masters.view\`, read-only.
- Summary of stage rules with missing/mismatched designations, inactive officers, missing department/category mappings.
- Filter by department, stage, issue type and category/officer. CSV export with spreadsheet formula escaping.
- Explicit distinction between a **string match** and authorized personnel assignment.
- The \`employee/allocation-inspector.php\` stage table now shows a separate title review status, rather than implying a green mapping badge is a verified designation.
- \`employee/master-control.php?tab=escalations\` shows a title review column.
- Utility: \`includes/escalation-quality.php\` exposes two purely read-only helpers for conservative title comparison and issue reporting.

No complaint assignments, officer records, escalation rules, permissions, schemas or live scheduling are changed.

## Review cautions

A label text difference is a flag, NOT proof the assignment is wrong. Acronyms, synonyms, delegated responsibilities and staffing changes require authorized verification. A text match is NOT proof of appointment.

Category-level \`escalation_officer_levels\` mappings differ from exact \`department_allocations\` ward-stage rules. Stage mappings are not automatically ward-authorized; the audit explicitly does not certify ward scope.

This implementation inspects up to 5,000 active stage rules per filtered query. When truncated, it displays an incomplete-results notice. The report uses current data and may change while records are updated; it is not an official historical snapshot.

## Test before any live automation

1. Pull \`feature/smartcity-pgp-integration\`, lint \`includes/escalation-quality.php\`, \`employee/escalation-audit.php\`, \`employee/allocation-inspector.php\`, \`employee/master-control.php\`, \`includes/employee-header.php\`.
2. Open \`employee/escalation-audit.php\`, filter category ID 1 / AEE, check its recorded designation and mapping status.
3. Verify CSV only includes filtered rows and that no database records change.
4. Verify only admin roles can access the audit, and check the existing role page mapping denies restricted accesses.
5. Have authorized municipal staff validate actual assignments, designations and escalation chain before enabling the scheduled CLI engine. Verify exact allocation rules independently.

Do not automatically rewrite recorded titles to match role labels. This code does not restore the existing public complaint OTP enforcement; public deployment must address that separately.
