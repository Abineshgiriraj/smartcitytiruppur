# ICTS Complaint Management — Initial PHP implementation

The old ICTS screenshots show: New Complaint, Complaints, Repeated Complaints, Closed Complaints, Category Dashboard, Zone Dashboard, Map View and Feedback.

This change adds \`employee/complaint-centre.php\` with five **read-only** views:

- **Potential Repeats:** candidate groups with the same mobile, category and ward in a selected date range. This is an analyst heuristic, **not** the original ICTS repeat criterion. It does not mark, merge, or close complaints.
- **Closed Complaints:** defaults to Closed; Resolved and Both are available as separate filters.
- **Category Dashboard:** department/category received, resolved, pending, overdue and reopened totals.
- **Zone Dashboard:** per-zone received, resolved, pending, overdue and reopened.
- **Map / Area Coverage:** ward breakdown. The current schema lacks verified point coordinates, so the interface does not invent map pins.
- **New Complaint** menu routes to the existing public PHP complaint form. This is not yet a privileged employee registration workflow.
- **Feedback** continues to use existing employee/feedback.php.

The views use existing role visibility and validated date filters, render HTML-escaped data, support masked repeat-mobile values in CSV, and do not update any table.

## Important QA steps

1. On \`feature/smartcity-pgp-integration\`, pull and PHP lint \`employee/complaint-centre.php\` and \`includes/employee-header.php\`.
2. Browse each of the five tabs as Super Admin, Department Head, Zone Officer, Ward Officer, Field Officer and Report User. Confirm no cross-department/ward disclosure.
3. Validate date-filter results, pagination/limit caveat, CSV download and search-reset links.
4. Verify matched potential repeats manually; do not treat an identical mobile/category/ward as proof of duplication.
5. Verify reopened counts vs complaint_reopen_history, and closed/resolved statuses.
6. The public complaint form currently contains a temporary OTP validation bypass in \`new-complaint.php\`. **Do not deploy that bypass to production.** Re-enable OTP validation and review officer assignment permissions before enabling employee registration.
7. Avoid exporting citizen personal information outside authorized workflow.

No DB schema migration, automatic background job, production database write, or SQL Server import is performed by this change.

Next: implement approved admin-specific registration, user-role page mapping, and true geolocated maps only after master/data requirements are confirmed.
