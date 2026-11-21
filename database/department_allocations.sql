-- Department Allocation Matrix & Auto-Assignment Trigger for Smart City Tiruppur
-- Mirrors PGP's (Department + Category + Zone + Ward + Stage -> Officer + SLA Days) auto-allocation design.
-- Additive migration — does not drop or alter existing DB connections or master data.

CREATE TABLE IF NOT EXISTS `department_allocations` (
    `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `department_id` INT(10) UNSIGNED NOT NULL,
    `category_id` INT(10) UNSIGNED NOT NULL,
    `zone_id` INT(10) UNSIGNED NOT NULL,
    `ward_id` INT(10) UNSIGNED NOT NULL,
    `stage_no` TINYINT(3) UNSIGNED NOT NULL DEFAULT 1 COMMENT '1=Stage 1 (Initial Assigned Officer), 2..7=Escalation Stages',
    `officer_id` INT(10) UNSIGNED NOT NULL,
    `escalation_days` SMALLINT(5) UNSIGNED NOT NULL DEFAULT 3 COMMENT 'SLA period in days at this stage',
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_dept_alloc_rule` (`department_id`, `category_id`, `zone_id`, `ward_id`, `stage_no`),
    KEY `idx_dept_alloc_lookup` (`department_id`, `category_id`, `zone_id`, `ward_id`, `stage_no`, `is_active`),
    KEY `idx_dept_alloc_officer` (`officer_id`),
    CONSTRAINT `fk_da_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_da_category` FOREIGN KEY (`category_id`) REFERENCES `complaint_categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_da_zone` FOREIGN KEY (`zone_id`) REFERENCES `zones` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_da_ward` FOREIGN KEY (`ward_id`) REFERENCES `wards` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_da_officer` FOREIGN KEY (`officer_id`) REFERENCES `officers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Allow complaint_escalations to also log escalations triggered via department_allocations
ALTER TABLE `complaint_escalations`
    MODIFY COLUMN `escalation_level_id` INT(10) UNSIGNED NULL;

-- IMPORTANT: Auto-assign trigger intentionally NOT installed.
-- A BEFORE INSERT trigger bypasses officer_stage_authorized() and can
-- send citizen complaints to unverified senior officers.
-- Do not recreate trg_complaints_auto_assign. New assignments must
-- pass through the checked PHP service after registration.
-- Existing installations require a separate, reviewed migration to
-- remove the historical trigger. This file makes no implicit DROP.
