# Role/Page Mapper — development branch

Added \`employee/page-permissions.php\` and a centralized authorization layer in \`includes/auth.php\`.
This is a **restrictive** authorization layer: it cannot grant permissions that the previous PHP role model denied. The existing department, zone, ward and officer filters still apply.

## Data storage
No schema migration. A serialized JSON matrix is stored at \`system_settings.setting_key = 'role_page_permissions_v1'\` **only when Super Admin explicitly saves a role**.
If the row is absent, existing PHP role permissions remain in effect. If present but malformed, access is denied to all roles except Super Admin.
Each role can be reset to the inherited baseline. Super Admin cannot be restricted via the mapper, to preserve recovery access.

## Enforcement
- \`dashboard.php\`: dashboard.view
- \`complaints.php\`, \`complaint-centre.php\`, \`complaint-view.php\`, \`download-attachment.php\`: complaints.view
- \`complaint-view.php\` POST: complaints.update (existing role and complaint scope still enforced)
- \`reports.php\`, \`icts-reports.php\`: reports.view
- \`feedback.php\`: feedback.view; POST: feedback.manage
- \`officers.php\`: officers.view
- \`escalation.php\`: escalation.view
- \`masters.php\`: masters.view; POST: masters.manage
- \`master-control.php\`: masters.view; POST: designations.manage
- \`users.php\`: users.view; POST: users.manage
- \`page-permissions.php\`: roles.view; POST: roles.manage (Super Admin only)
- Sidebar hides restricted sections, but actual server-side checks independently prevent direct URL access.

## Safety and limitations
- No officers, complaints, credentials or escalation mappings were changed by this source commit.
- This is an *allow-list on top of* existing hard-coded PHP role checks, not a blanket replacement or new grant system.
- Existing public complaint intake and OTP behavior are **not** controlled by this mapper. The prior \`new-complaint.php\` OTP bypass must be fixed before production use.
- Admin user/role table still needs a full roles-based row/action mapping review. Not all \`ajax/\` endpoints are included in this phase.
- The Advanced Masters screenshot showed 12,494 officers and zero linked employee accounts; these figures differ from the previously tested 89 officers. Investigate import/master-data provenance before using writes.
- Test on a cloned database before saving any restrictions; never lock out a role without first verifying a Super Admin login.

## Test steps
1. Pull branch; PHP lint all modified files, especially \`includes/auth.php\`, \`employee/page-permissions.php\`, \`includes/employee-header.php\`.
2. Open the new page as Super Admin; confirm defaults, switch between roles.
3. On a **separate test database**, uncheck \`reports.view\` for Report User and Save; confirm navigation hides reports and direct URLs for \`reports.php\` and \`icts-reports.php\` return HTTP 403, with other scopes unchanged.
4. Restore role defaults. Test Admin may view but not edit the role mapper.
5. Verify Department Head and Ward Officer scopes remain enforced in all complaint views and detail pages.
