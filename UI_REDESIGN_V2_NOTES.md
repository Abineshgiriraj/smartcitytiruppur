# Smart City Tiruppur — structural UI redesign (V2)

Isolated branch: \`feature/smartcity-ui-refresh\`. This work contains NO escalation, SQL, authorization or production database changes.

## Public homepage
- Replaces old left-column statistics / right-column service list with a full-width civic hero + prominent New Complaint / Track Complaint actions, separate three-card KPI strip, four-column Quick Services gallery, and customer-support contact strip.
- Preserves: PHP statistics query, totals, labels, four service URLs, original header/footer, logos, contact email setting and support telephone.
- Uses the existing project building image \`assets/images/smart-bg.jpg\`; no external assets or new dependencies.
- Public forms, menus, tracking pages and dashboard continue using the existing content and JS.

## Admin
- Pure CSS redesign of the shared administrative sidebar (light workspace), navy topbar, civic-gradient page heading, metrics, tables, cards, and charts.
- Does NOT alter employee role menus, queries, or actions.

## Files changed
- \`index.php\` — presentation markup only; query preserved byte-for-byte
- \`assets/css/citizen-refresh.css\` — new V2 landing styles appended
- \`assets/css/admin-refresh.css\` — new V2 admin styles appended
- \`includes/header.php\`, \`includes/employee-header.php\` — cache-busting version updated only

## QA & release
Visual inspection in \`smartcity-ui-preview\` separate git worktree:
- Desktop / mobile / zoom, header dropdowns, Tamil text and focus ring
- Cards and CTAs all route to their original links; counters unchanged
- Admin sidebar, filters, tables, role-based nav
- Tracking form, citizen complaint form and CSRF remain unchanged
- Check browser console and PHP syntax
- DO NOT submit test complaints against \`smartcitytiruppur\`; do NOT merge while Antigravity is auditing the escalation branch
- Report for commit \`84af8ae\` identifies unsafe \`trg_complaints_auto_assign\` trigger and inconsistent candidate-selection test evidence; these are separate backend tasks, not part of V2 UI.

\`git fetch origin; cd D:\XAMPP\htdocs\test\smartcity-ui-preview; git pull\`
