-- MANUAL, TEST-ONLY, BACKUP-FIRST schema upgrade.
-- Confirm SELECT DATABASE() = 'smartcity_escalation_test' before running.
-- Verify old constraint name using SHOW CREATE TABLE officer_stage_approvals;
-- If the old named constraint exists, review these lines before execution.
ALTER TABLE officer_stage_approvals DROP CONSTRAINT chk_stage_authorized_range;
ALTER TABLE officer_stage_approvals ADD CONSTRAINT chk_stage_authorized_range
    CHECK (stage_no BETWEEN 1 AND 6);
-- Stage 5/6 PDF routing may not have a matching legacy escalation_officer_levels
-- row; permit NULL (no foreign key target) for new-stage escalation events.
ALTER TABLE complaint_escalations MODIFY escalation_level_id INT UNSIGNED NULL;
