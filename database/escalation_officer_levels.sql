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

-- 3. Populate escalation officer levels for all active complaint categories
-- Automatically maps Levels 1, 2, 3, and 4 across all category departments.
INSERT INTO `escalation_officer_levels` (`category_id`, `level_no`, `officer_id`, `designation`, `escalation_period_days`, `is_active`)
SELECT 
    cc.id AS category_id,
    lvl.level_no,
    COALESCE(off_map.officer_id, 1) AS officer_id,
    lvl.designation,
    lvl.escalation_period_days,
    1 AS is_active
FROM `complaint_categories` cc
CROSS JOIN (
    SELECT 1 AS level_no, 'Assistant Engineer (Field Officer)' AS designation, 3 AS escalation_period_days
    UNION ALL
    SELECT 2 AS level_no, 'Assistant Executive Engineer (AEE)' AS designation, 5 AS escalation_period_days
    UNION ALL
    SELECT 3 AS level_no, 'Executive Engineer / Head of Department' AS designation, 7 AS escalation_period_days
    UNION ALL
    SELECT 4 AS level_no, 'Superintending Engineer / City Commissioner' AS designation, 10 AS escalation_period_days
) lvl
LEFT JOIN (
    SELECT category_id, officer_id, ROW_NUMBER() OVER (PARTITION BY category_id ORDER BY officer_id) AS rn
    FROM officer_category_map
) off_map ON off_map.category_id = cc.id AND off_map.rn = lvl.level_no
WHERE cc.is_active = 1
ON DUPLICATE KEY UPDATE 
    `officer_id` = VALUES(`officer_id`),
    `designation` = VALUES(`designation`),
    `escalation_period_days` = VALUES(`escalation_period_days`);
