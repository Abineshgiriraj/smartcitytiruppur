# ICTS admin screenshot feature parity

This plan is grounded in the four user-provided screenshots of the authenticated legacy **smarttiruppur.com** admin on 20 November 2026. These screenshots show menu names, not the complete workflows or database semantics.

## Existing menu items identified

**Top navigation:** HOME, DASHBOARD, COMPLAINTS, MASTERS, REPORTS, UPDATE STATUS, Change Password, Admin, Signout.

**Complaints (8):** New Complaint; Complaints; Repeated Complaints; Closed Complaints; Category Dashboard; Zone Dashboard; Map View; Feedback.

**Masters (13 visible):** Departments; Designations; DCC Escalation Mapper; Complaint Categories; Wards Master; Locality Master; User Registration; List of Users; User Mapping Details; Wards Mapping Details; Role Mapping; Role Page Mapper; Complaint Source Master.

**Reports (11):** Department Wise Report; Department Category Wise Report; Zone And Complaint Source Wise Report; Zonal Wise Category Report; Periodical Report; Ward Wise Report; Employee Wise Grievance Report; Rating Abstract Report; Department Performance Report; Employee Wise Escalation Report; Employee Performance Report.

## Current new PHP implementation

- Basic complaint inbox and detail/update: employee/complaints.php and complaint-view.php
- General report/export: employee/reports.php
- Department/category/zone/ward/street/officer and allocation management: employee/masters.php
- Users and roles: employee/users.php; role scoping in includes/auth.php
- Officer directory and escalation monitor: employee/officers.php and employee/escalation.php
- CLI escalation engine (tested through stage 4 in cloned DB; NOT scheduled on main DB)
- **ICTS Reporting Centre**: employee/icts-reports.php is a read-only first-pass equivalent of the 11 visible report labels, using current Smart City data and existing tables. Date filters and department/zone filters; CSV and print. It DOES NOT claim exact parity with legacy ICTS report calculations.

## Important differences requiring follow-up

- Legacy "Repeated Complaints" has no verified matching repeat-detection rules in the Core PHP schema. The reports show "Reopened" based on complaint_reopen_history, which must not be confused with legacy "Repeated".
- "Employee Performance" currently groups current assigned officers. To calculate actual resolving officer, transfer sequence, individual SLA performance and workload ownership, the data model needs signed-off metric definitions and event-driven attribution.
- Rating abstract uses only ratings linked to a complaint in feedback. Unlinked general feedback is not included.
- No current complaint latitude/longitude columns for accurate legacy Map View; adding map pins requires validated geolocation data.
- Designation strings are missing in existing officer records; verify official responsibilities before using staged escalation in production.
- Legacy source master, role-action matrix, role-page mapping, and rich user/ward mapping screens need proper tables, permissions and workflows; never auto-create operational users from old ICTS data.
- The current MySQL database is separate from historical SQL Server ICTS_Live. No automatic SQL Server connection, import or migration is implied.

## Roadmap (without changing production)

1. Verify new reporting centre locally against a test DB, totals vs current general report, role restriction behavior, CSV, Tamil labels and empty-result handling.
2. Upgrade complaint lifecycle screens: repeated definition, transfers, reopened, closed, category/zone dashboards and approved map data.
3. Build masters: designations, staff/ward mapping, role-page/permission matrix, source master, escalation rule management.
4. Implement historical actor-attributed report metrics, SMS and ratings, notices, and approval-based status transitions.
5. Validate permissions, data import, rollbacks and all 11 legacy report definitions in QA. Only then deploy to live.
