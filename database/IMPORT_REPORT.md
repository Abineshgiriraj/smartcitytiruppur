# Legacy data import report

Source: `smart_tiruppur (3).sql`

## Original rows preserved

| Legacy table | Rows |
|---|---:|
| complaint_entries | 1 |
| dept_master | 1437 |
| feedback | 0 |
| officer_master | 221 |
| priority_master | 322 |
| street_master | 4190 |
| **Total** | **6171** |

Every original row is preserved in the corresponding `legacy_*` archive table.

## Clean website data created

| Clean table/data | Rows |
|---|---:|
| departments | 14 |
| complaint_categories | 306 |
| zones | 4 |
| wards | 60 |
| streets | 4176 |
| officers | 89 |
| officer_department_map | 224 |
| officer_category_map | 1383 |
| officer_ward_map | 2345 |
| complaints | 1 |
| complaint_status_history | 1 |

## Normalization decisions

- 14 duplicate street rows were retained in
  `legacy_street_master` but mapped to the first matching clean street.
- 2 department/category pairs had conflicting SLA values.
  The clean category uses the lowest legacy SLA value, while every source row
  remains in `legacy_priority_master`.
- The category `Public Health / UGD-Others` existed in `dept_master` without
  an SLA row in `priority_master`; its clean SLA is `0` until an administrator
  configures it.
- The one legacy complaint was mapped to the normalized department, category,
  ward, zone and closest matching street. Its original row remains unchanged
  in `legacy_complaint_entries`.
- No employee login account was present in the legacy database. Create the
  first Super Admin through `employee/setup-admin.php`.
