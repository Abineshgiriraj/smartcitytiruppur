-- Execute ONLY after selecting a verified test/clone database in phpMyAdmin.
-- No rows are populated by this migration and existing data is untouched.
CREATE TABLE IF NOT EXISTS officer_stage_approvals (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    category_id INT UNSIGNED NOT NULL,
    stage_no TINYINT UNSIGNED NOT NULL,
    officer_id INT UNSIGNED NOT NULL,
    approval_reference VARCHAR(255) NOT NULL,
    approved_by INT UNSIGNED NOT NULL,
    approved_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    revoked_at DATETIME NULL,
    UNIQUE KEY uk_authority_stage_officer (category_id,stage_no,officer_id),
    KEY idx_authority_live (category_id,stage_no,revoked_at),
    CONSTRAINT fk_auth_stage_category FOREIGN KEY (category_id) REFERENCES complaint_categories(id),
    CONSTRAINT fk_auth_stage_officer FOREIGN KEY (officer_id) REFERENCES officers(id),
    CONSTRAINT fk_auth_stage_user FOREIGN KEY (approved_by) REFERENCES users(id),
    CONSTRAINT chk_stage_authorized_range CHECK (stage_no BETWEEN 1 AND 6),
    CONSTRAINT chk_stage_auth_reference CHECK (CHAR_LENGTH(TRIM(approval_reference)) > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
