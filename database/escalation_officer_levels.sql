-- Smart City Tiruppur - Complaint Auto-Escalation Officer Levels Master Data
-- Compatible with MySQL 8.0+ / MariaDB 10.4+

USE `smartcitytiruppur`;

-- 1. Create table escalation_officer_levels
CREATE TABLE IF NOT EXISTS `escalation_officer_levels` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `category_id` INT UNSIGNED NOT NULL,
  `level_no` TINYINT UNSIGNED NOT NULL COMMENT '1=Level 1 (Field Officer/AE), 2=Level 2 (AEE/SO), 3=Level 3 (EE/CHO), 4=Level 4 (Commissioner)',
  `officer_id` INT UNSIGNED NOT NULL,
  `designation` VARCHAR(150) NOT NULL,
  `escalation_period_days` SMALLINT UNSIGNED NOT NULL DEFAULT 3 COMMENT 'SLA period in days before auto-escalating to next level',
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_category_level` (`category_id`, `level_no`),
  KEY `idx_eol_category_active` (`category_id`, `is_active`),
  KEY `idx_eol_officer` (`officer_id`),
  CONSTRAINT `fk_eol_category` FOREIGN KEY (`category_id`) REFERENCES `complaint_categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_eol_officer` FOREIGN KEY (`officer_id`) REFERENCES `officers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Create table complaint_escalations
CREATE TABLE IF NOT EXISTS `complaint_escalations` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `complaint_id` BIGINT UNSIGNED NOT NULL,
  `escalation_level_id` INT UNSIGNED NOT NULL,
  `level_no` TINYINT UNSIGNED NOT NULL,
  `officer_id` INT UNSIGNED NOT NULL,
  `escalated_at` DATETIME NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_ce_complaint_level` (`complaint_id`, `level_no`),
  KEY `idx_ce_officer` (`officer_id`),
  CONSTRAINT `fk_ce_complaint` FOREIGN KEY (`complaint_id`) REFERENCES `complaints` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_ce_escalation_level` FOREIGN KEY (`escalation_level_id`) REFERENCES `escalation_officer_levels` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_ce_officer` FOREIGN KEY (`officer_id`) REFERENCES `officers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Historical bulk seed REMOVED to protect existing officer assignments.
-- Earlier versions assigned the first four mapped officer IDs to stages 1-4,
-- labeled those officers AE/AEE/EE/Commissioner without verifying designations,
-- fell back to officer_id=1 and used ON DUPLICATE KEY UPDATE officer_id=...
-- which could OVERWRITE any reviewed stage allocation when rerun.
-- Running this file now creates missing tables ONLY and preserves all records.
-- Stage officer assignments must be individually checked against official
-- department, designation and escalation approvals before insertion or update.
-- Read-only auditing: employee/escalation-audit.php

