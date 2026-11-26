
-- Manual, additive migration. Never run automatically during login.
-- Identity/action approvals are explicitly authorized, not inferred from contacts.
CREATE TABLE IF NOT EXISTS legacy_complaint_history_approvals (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  legacy_user_id BIGINT UNSIGNED NOT NULL,
  action_code BIGINT NOT NULL,
  officer_id INT UNSIGNED NOT NULL,
  login_user_id INT UNSIGNED NOT NULL,
  approved_by INT UNSIGNED NOT NULL,
  evidence_reference VARCHAR(255) NOT NULL,
  approved_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  revoked_at DATETIME DEFAULT NULL,
  UNIQUE KEY uq_legacy_action (legacy_user_id, action_code),
  KEY idx_lcha_officer (officer_id, login_user_id, revoked_at),
  CONSTRAINT fk_lcha_officer FOREIGN KEY (officer_id) REFERENCES officers(id),
  CONSTRAINT fk_lcha_login FOREIGN KEY (login_user_id) REFERENCES users(id),
  CONSTRAINT fk_lcha_admin FOREIGN KEY (approved_by) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS legacy_complaint_history (
  source_lifecycle_id BIGINT UNSIGNED NOT NULL PRIMARY KEY,
  source_complaint_id BIGINT UNSIGNED NOT NULL,
  complaint_id BIGINT UNSIGNED NOT NULL,
  approval_id BIGINT UNSIGNED NOT NULL,
  source_action_at DATETIME DEFAULT NULL,
  KEY idx_lch_approval_complaint (approval_id, complaint_id),
  KEY idx_lch_complaint (complaint_id),
  CONSTRAINT fk_lch_approval FOREIGN KEY (approval_id) REFERENCES legacy_complaint_history_approvals(id),
  CONSTRAINT fk_lch_complaint FOREIGN KEY (complaint_id) REFERENCES complaints(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
