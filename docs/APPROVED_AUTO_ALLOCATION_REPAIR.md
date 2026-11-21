# Approved initial officer auto-allocation

Problem: citizen complaint is Registered but no assigned officer. The old mapping and stage-1 seed are unsafe and do not establish official authority. Main approval registry was previously missing. Officer IDs 1/Admin and Commissioner stage 1 cannot be used as fallbacks.

Fix:
- First check an exact Stage 1 allocation. An unapproved exact rule remains a hard stop, not an excuse to bypass reviewed allocation.
- For no exact rule, search explicitly approved Stage 1 officers FIRST; rank ward-matched officers before city-wide officers and verify department/category/ward authority. This avoids truncating unapproved legacy candidate lists at LIMIT 250.
- Employee complaint details offers a permission-guarded retry of the SAME allocator on Registered + unassigned complaints. It is transactional, locked, audited, and does not invent approvals or reassign existing complaints.
- Read-only diagnostic tools/diagnose_complaint_allocation.php reports context, table presence, stage1 records, department/category/ward checks and exact rule blocks.

Local test in a separate worktree BEFORE main merge:
1. Configure BASE_URL and DB_NAME=smartcity_escalation_test, preserve production main settings.
2. php -l includes/functions.php, employee/complaint-view.php, tools/diagnose_complaint_allocation.php
3. Run diagnostic on a sandbox complaint reference, confirm missing approvals are reported accurately.
4. Use only synthetic, documented approvals in sandbox to verify allocation to junior officer, one current assignment, correct due_at/history, and repeat retry rejects.
5. If exact Stage 1 rule points to unapproved staff, it must remain unassigned pending review, even when another officer is approved.

Main database readiness diagnosis (NO DATA CHANGE):
D:\XAMPP\php\php.exe .\tools\diagnose_complaint_allocation.php --expect-db=smartcitytiruppur --reference=TCMC-2026-576866

Do NOT activate background automatic escalation. Do not create fake approval references or assume that designation or mapped IDs are official authorization. The uploaded category SQL is independent from officer authorization.
