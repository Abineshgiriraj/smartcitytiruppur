-- Manual migration for the isolated test database only. Verify SELECT DATABASE() first.
-- Separate PDF workflow definitions from the person's approved officer mapping.
-- No changes to existing complaint, escalation, assignment, or officer records.
CREATE TABLE IF NOT EXISTS pdf_escalation_policies (
  category_id INT UNSIGNED NOT NULL PRIMARY KEY,
  source_row SMALLINT UNSIGNED NOT NULL,
  total_days SMALLINT UNSIGNED NOT NULL,
  stage_count TINYINT UNSIGNED NOT NULL,
  pdf_sha256 CHAR(64) NOT NULL,
  is_approved TINYINT(1) NOT NULL DEFAULT 0,
  approval_reference VARCHAR(255) NULL,
  approved_by INT UNSIGNED NULL,
  approved_at DATETIME NULL,
  CONSTRAINT fk_pdf_policy_category FOREIGN KEY (category_id) REFERENCES complaint_categories(id),
  CONSTRAINT fk_pdf_policy_approver FOREIGN KEY (approved_by) REFERENCES users(id),
  CONSTRAINT chk_pdf_stage_count CHECK (stage_count BETWEEN 1 AND 6),
  CONSTRAINT chk_pdf_approval_details CHECK (is_approved=0 OR (approval_reference IS NOT NULL AND approved_by IS NOT NULL))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS pdf_escalation_policy_stages (
  category_id INT UNSIGNED NOT NULL,
  stage_no TINYINT UNSIGNED NOT NULL,
  role_label VARCHAR(150) NOT NULL,
  period_days SMALLINT UNSIGNED NOT NULL,
  PRIMARY KEY (category_id,stage_no),
  CONSTRAINT fk_pdf_policy_stage_category FOREIGN KEY (category_id)
    REFERENCES pdf_escalation_policies(category_id),
  CONSTRAINT chk_pdf_stage_no CHECK (stage_no BETWEEN 1 AND 6)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Existing complaint_escalations.escalation_level_id may be NOT NULL.
-- A new Stage 5/6 approved-officer routing can legitimately lack a legacy
-- escalation_officer_levels FK. On reviewed test DB and after backup:
-- ALTER TABLE complaint_escalations MODIFY escalation_level_id INT UNSIGNED NULL;
-- Do NOT run automatic escalation from this migration.
