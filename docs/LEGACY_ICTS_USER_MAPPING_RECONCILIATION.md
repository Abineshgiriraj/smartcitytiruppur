# Legacy ICTS User Mapping Details — evidence and reconciliation

Evidence: administrator-supplied screenshot on 23 November 2026 of
`https://smarttiruppur.com/Masters/frmusermappingdetails.aspx`. The authenticated
page could not be retrieved independently via the public web client. The
screenshot is not a database export or an official appointment record.

## Explicitly visible in the legacy screenshot

Filters displayed:
- Department: Engineering - Street Light
- Complaint Category: **Street Light**
- Zone: **ZONE - 04**
- Ward: **WARD-(53)**

Grid rows show **Wards = 57** for all seven displayed employees,
**not Ward 53**. This must be resolved in the legacy application or through
a verified database export before using the result for Ward 53.
Do not silently overwrite ward 53 or treat 57 as a typo.

Designation/priority pairs from visible grid (priority is a legacy number,
NOT the PHP PDF stage_no): 
- ARUMUGAM K — AE(ELECTRICAL), priority 6
- SENTHIL KUMAR — AEE, priority 5
- ALAUDIN, MUNIYANDI, Gopi — EE, priority 4
- Senthilmurugan — DEPUTY COMMR, priority 2
- AMITH — COMMR, priority 1

The screenshot does not show a CE employee in the filtered list. Do not
assume the visible list constitutes all six required PDF policy stages.

## Target complaint

- `TCMC-2026-950324`: Engineering - Street Light, **Others** (category ID 24),
  zone ID 4, ward ID 53 in `smartcity_escalation_test`.
- PDF policy 24 has six reviewed stages: AE(ELECTRICAL) 2d, AEE 1d,
  EE 1d, CE 1d, DEPUTY COMMR 1d, COMMR 1d.
- Latest user-run read-only diagnostics: policy approved/valid; zero Stage 1
  officer approvals; zero exact allocation rows; six active department/category
  mapped officers; none with a populated AE(ELECTRICAL) designation.
- Legacy list filter **Street Light** is distinct from target category **Others**.
- Legacy personnel record ARUMUGAM K resembles local officer #4, but matching
  names do NOT prove that they are the same employee, nor that either serves
  Ward 53 for category Others.

## Source-to-target mapping specification

Use a reviewed CSV/export from an authorized legacy administrator containing:
- source employee stable ID (not name alone)
- source department and category IDs and labels
- source zone and ward ID/name after filter/result mismatch reconciliation
- source designation and approved priority mapping
- effective dates / active status
- verified corresponding PHP officer ID and user_id
- municipal appointment and delegation/approval reference for target stage

Join on stable identifiers and confirm identity and ward scope. Compare
role/designation against `pdf_escalation_policy_stages` for the **exact category**.
Legacy priority values must be explicitly mapped to PDF stage_no; do not
reverse-sort or offset blindly. Never import an arbitrary priority as a stage.

No screenshots or exports grant actual authorization in the PHP DB. The
`officer_stage_approvals` table is populated only after authorized signoff.
The reviewer must verify category, ward scope and personnel authority.

## Changes made alongside this analysis

- `employee/masters.php` now refuses exact allocation-rule saves unless the
  officer is approved for that stage, the entered days match the approved PDF,
  and the ward mapping is recorded for Stage 1. Multi-ward rule saves are
  enclosed in a transaction; any failed validation prevents writes.
- `employee/allocation-inspector.php` labels its old lookup as an unverified
  legacy candidate (not a selected officer).
- The production database and authenticated ICTS records were not modified.

## What the legacy administrator should verify next

On the original User Mapping Details page, choose:
**Engineering - Street Light > Others > ZONE - 04 > WARD-(53)** and
click SEARCH. Verify the resulting **Wards** values are 53 (not 57).
Then corroborate through Wards Mapping Details and an authoritative
employee identifier. If the ward mismatch persists, treat allocation as blocked
and resolve the source data issue before approving or importing mappings.

After authorized officer IDs and policies are fully reconciled in
`smartcity_escalation_test`, test one synthetic Stage 1 allocation and
five subsequent controlled escalations. Keep the live job disabled.
