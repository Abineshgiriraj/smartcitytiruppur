# UI refresh — citizen and admin visual redesign

## Scope
This branch is **design-only**. It changes neither homepage counters nor database queries, authorization, escalation logic, form fields, input names, POST/GET behavior, links, routes, application JavaScript, nor existing master data.

Starting point: \`84af8ae7b2825340aacf8779e992419697584d96\`.

### Files
- \`assets/css/citizen-refresh.css\` (new): navigation, homepage stat cards, service panel, forms, tracking, public dashboard, footer, responsive appearance, keyboard focus.
- \`assets/css/admin-refresh.css\` (new): administrative sidebar, topbar, metrics, tables, forms, quick actions, charts.
- \`includes/header.php\`: adds one stylesheet link **after** original public CSS.
- \`includes/employee-header.php\`: adds one stylesheet link **after** original admin CSS.

No application PHP logic was changed. The original stylesheets remain available. Rollback requires removing the two new stylesheet links only.

### Isolation from escalation tests
Created on separate branch \`feature/smartcity-ui-refresh\` to avoid modifying \`feature/smartcity-pgp-integration\` while another agent runs its tests. Review/merge only when tests have completed and a UI smoke test has passed.

### Manual visual/behavior verification checklist
1. Homepage: same resolved/pending/total database values and all 4 quick-service links; verify desktop/tablet/mobile.
2. Public registration: same fields, autocomplete, CAPTCHA, file upload, CSRF and submission controls (DO NOT submit against live).
3. Track complaint and public dashboard: same queries/data, forms, tabs and filters.
4. Admin dashboard and all staff pages: role-controlled nav remains correct; forms, tables, modal dialogs and menus remain usable.
5. Keyboard: Tab focus visible; mobile navbar and sidebar toggle still work; check high zoom and long Tamil text.
6. Check print views remain readable.
7. Do not enable production escalation; this design branch does not modify the routing safety work.

### Manual rollback
Remove these stylesheet links from their respective header files, or revert the UI commit. No database migration is required.
