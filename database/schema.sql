-- Smart City Tiruppur
-- Empty database schema only. No INSERT statements and no master data.
-- Compatible with MariaDB 10.4+ / MySQL 8+

SET NAMES utf8mb4;
SET time_zone = '+05:30';
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS `smartcitytiruppur`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `smartcitytiruppur`;

CREATE TABLE `departments` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `department_name` VARCHAR(150) NOT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_department_name` (`department_name`),
  KEY `idx_department_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `complaint_categories` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `department_id` INT UNSIGNED NOT NULL,
  `category_name` VARCHAR(250) NOT NULL,
  `sla_days` SMALLINT UNSIGNED NOT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_department_category` (`department_id`, `category_name`),
  KEY `idx_category_active` (`is_active`),
  CONSTRAINT `fk_category_department`
    FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `zones` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `zone_no` VARCHAR(20) NOT NULL,
  `zone_name` VARCHAR(150) DEFAULT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_zone_no` (`zone_no`),
  KEY `idx_zone_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `wards` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `zone_id` INT UNSIGNED NOT NULL,
  `ward_no` VARCHAR(20) NOT NULL,
  `ward_name` VARCHAR(150) DEFAULT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_ward_no` (`ward_no`),
  KEY `idx_ward_zone` (`zone_id`),
  KEY `idx_ward_active` (`is_active`),
  CONSTRAINT `fk_ward_zone`
    FOREIGN KEY (`zone_id`) REFERENCES `zones` (`id`)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `streets` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `ward_id` INT UNSIGNED NOT NULL,
  `street_name` VARCHAR(300) NOT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_street_ward` (`ward_id`, `street_name`),
  KEY `idx_street_name` (`street_name`),
  KEY `idx_street_active` (`is_active`),
  CONSTRAINT `fk_street_ward`
    FOREIGN KEY (`ward_id`) REFERENCES `wards` (`id`)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `full_name` VARCHAR(150) NOT NULL,
  `email` VARCHAR(250) NOT NULL,
  `mobile_no` VARCHAR(15) DEFAULT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `role` VARCHAR(50) NOT NULL,
  `department_id` INT UNSIGNED DEFAULT NULL,
  `zone_id` INT UNSIGNED DEFAULT NULL,
  `ward_id` INT UNSIGNED DEFAULT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `last_login_at` DATETIME DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_user_email` (`email`),
  KEY `idx_user_role` (`role`),
  KEY `idx_user_active` (`is_active`),
  KEY `idx_user_department` (`department_id`),
  KEY `idx_user_zone` (`zone_id`),
  KEY `idx_user_ward` (`ward_id`),
  CONSTRAINT `fk_user_department`
    FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`)
    ON UPDATE CASCADE ON DELETE SET NULL,
  CONSTRAINT `fk_user_zone`
    FOREIGN KEY (`zone_id`) REFERENCES `zones` (`id`)
    ON UPDATE CASCADE ON DELETE SET NULL,
  CONSTRAINT `fk_user_ward`
    FOREIGN KEY (`ward_id`) REFERENCES `wards` (`id`)
    ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `officers` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED DEFAULT NULL,
  `officer_name` VARCHAR(150) NOT NULL,
  `designation` VARCHAR(100) DEFAULT NULL,
  `mobile_no` VARCHAR(15) DEFAULT NULL,
  `email` VARCHAR(250) DEFAULT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_officer_user` (`user_id`),
  KEY `idx_officer_name` (`officer_name`),
  KEY `idx_officer_active` (`is_active`),
  CONSTRAINT `fk_officer_user`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
    ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `officer_department_map` (
  `officer_id` INT UNSIGNED NOT NULL,
  `department_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`officer_id`, `department_id`),
  KEY `idx_odm_department` (`department_id`),
  CONSTRAINT `fk_odm_officer`
    FOREIGN KEY (`officer_id`) REFERENCES `officers` (`id`)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `fk_odm_department`
    FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `officer_category_map` (
  `officer_id` INT UNSIGNED NOT NULL,
  `category_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`officer_id`, `category_id`),
  KEY `idx_ocm_category` (`category_id`),
  CONSTRAINT `fk_ocm_officer`
    FOREIGN KEY (`officer_id`) REFERENCES `officers` (`id`)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `fk_ocm_category`
    FOREIGN KEY (`category_id`) REFERENCES `complaint_categories` (`id`)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `officer_ward_map` (
  `officer_id` INT UNSIGNED NOT NULL,
  `ward_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`officer_id`, `ward_id`),
  KEY `idx_owm_ward` (`ward_id`),
  CONSTRAINT `fk_owm_officer`
    FOREIGN KEY (`officer_id`) REFERENCES `officers` (`id`)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `fk_owm_ward`
    FOREIGN KEY (`ward_id`) REFERENCES `wards` (`id`)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `complaints` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `reference_no` VARCHAR(25) DEFAULT NULL,
  `citizen_name` VARCHAR(250) NOT NULL,
  `mobile_no` VARCHAR(15) NOT NULL,
  `email` VARCHAR(250) DEFAULT NULL,
  `communication_address` TEXT NOT NULL,
  `street_id` INT UNSIGNED NOT NULL,
  `ward_id` INT UNSIGNED NOT NULL,
  `zone_id` INT UNSIGNED NOT NULL,
  `door_no` VARCHAR(100) NOT NULL,
  `location_address` TEXT NOT NULL,
  `assessment_no` VARCHAR(100) DEFAULT NULL,
  `department_id` INT UNSIGNED NOT NULL,
  `category_id` INT UNSIGNED NOT NULL,
  `assigned_officer_id` INT UNSIGNED DEFAULT NULL,
  `description_tamil` LONGTEXT DEFAULT NULL,
  `description_english` LONGTEXT NOT NULL,
  `status` VARCHAR(50) NOT NULL DEFAULT 'Registered',
  `source` VARCHAR(50) NOT NULL DEFAULT 'Website',
  `due_at` DATETIME DEFAULT NULL,
  `resolved_at` DATETIME DEFAULT NULL,
  `closed_at` DATETIME DEFAULT NULL,
  `reopen_count` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `is_deleted` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_complaint_reference` (`reference_no`),
  KEY `idx_complaint_mobile` (`mobile_no`),
  KEY `idx_complaint_status_date` (`status`, `created_at`),
  KEY `idx_complaint_department_category` (`department_id`, `category_id`),
  KEY `idx_complaint_location` (`zone_id`, `ward_id`),
  KEY `idx_complaint_officer` (`assigned_officer_id`),
  KEY `idx_complaint_due` (`due_at`, `status`),
  KEY `idx_complaint_deleted` (`is_deleted`),
  CONSTRAINT `fk_complaint_street`
    FOREIGN KEY (`street_id`) REFERENCES `streets` (`id`)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT `fk_complaint_ward`
    FOREIGN KEY (`ward_id`) REFERENCES `wards` (`id`)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT `fk_complaint_zone`
    FOREIGN KEY (`zone_id`) REFERENCES `zones` (`id`)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT `fk_complaint_department`
    FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT `fk_complaint_category`
    FOREIGN KEY (`category_id`) REFERENCES `complaint_categories` (`id`)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT `fk_complaint_officer`
    FOREIGN KEY (`assigned_officer_id`) REFERENCES `officers` (`id`)
    ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `complaint_attachments` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `complaint_id` BIGINT UNSIGNED NOT NULL,
  `file_path` VARCHAR(500) NOT NULL,
  `original_name` VARCHAR(255) NOT NULL,
  `mime_type` VARCHAR(100) NOT NULL,
  `file_size` INT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_attachment_complaint` (`complaint_id`),
  CONSTRAINT `fk_attachment_complaint`
    FOREIGN KEY (`complaint_id`) REFERENCES `complaints` (`id`)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `complaint_assignments` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `complaint_id` BIGINT UNSIGNED NOT NULL,
  `officer_id` INT UNSIGNED NOT NULL,
  `assigned_by` INT UNSIGNED DEFAULT NULL,
  `assigned_at` DATETIME NOT NULL,
  `unassigned_at` DATETIME DEFAULT NULL,
  `is_current` TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `idx_assignment_complaint` (`complaint_id`, `is_current`),
  KEY `idx_assignment_officer` (`officer_id`, `is_current`),
  KEY `idx_assignment_user` (`assigned_by`),
  CONSTRAINT `fk_assignment_complaint`
    FOREIGN KEY (`complaint_id`) REFERENCES `complaints` (`id`)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `fk_assignment_officer`
    FOREIGN KEY (`officer_id`) REFERENCES `officers` (`id`)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT `fk_assignment_user`
    FOREIGN KEY (`assigned_by`) REFERENCES `users` (`id`)
    ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `complaint_status_history` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `complaint_id` BIGINT UNSIGNED NOT NULL,
  `old_status` VARCHAR(50) DEFAULT NULL,
  `new_status` VARCHAR(50) NOT NULL,
  `remarks` TEXT DEFAULT NULL,
  `changed_by` INT UNSIGNED DEFAULT NULL,
  `changed_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_history_complaint_date` (`complaint_id`, `changed_at`),
  KEY `idx_history_user` (`changed_by`),
  CONSTRAINT `fk_history_complaint`
    FOREIGN KEY (`complaint_id`) REFERENCES `complaints` (`id`)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `fk_history_user`
    FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`)
    ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `complaint_reopen_history` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `complaint_id` BIGINT UNSIGNED NOT NULL,
  `reopened_by_mobile` VARCHAR(15) NOT NULL,
  `reason` TEXT NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_reopen_complaint` (`complaint_id`),
  CONSTRAINT `fk_reopen_complaint`
    FOREIGN KEY (`complaint_id`) REFERENCES `complaints` (`id`)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `otp_requests` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `mobile_no` VARCHAR(15) NOT NULL,
  `purpose` VARCHAR(30) NOT NULL,
  `otp_hash` VARCHAR(255) NOT NULL,
  `expires_at` DATETIME NOT NULL,
  `verified_at` DATETIME DEFAULT NULL,
  `attempt_count` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_otp_mobile_purpose` (`mobile_no`, `purpose`, `created_at`),
  KEY `idx_otp_expiry` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `notifications` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `complaint_id` BIGINT UNSIGNED DEFAULT NULL,
  `channel` VARCHAR(20) NOT NULL,
  `recipient` VARCHAR(250) NOT NULL,
  `message_text` TEXT NOT NULL,
  `delivery_status` VARCHAR(30) NOT NULL DEFAULT 'Pending',
  `provider_reference` VARCHAR(250) DEFAULT NULL,
  `sent_at` DATETIME DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_notification_complaint` (`complaint_id`),
  KEY `idx_notification_status` (`delivery_status`, `created_at`),
  CONSTRAINT `fk_notification_complaint`
    FOREIGN KEY (`complaint_id`) REFERENCES `complaints` (`id`)
    ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `feedback` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(250) NOT NULL,
  `mobile_no` VARCHAR(15) NOT NULL,
  `email` VARCHAR(250) DEFAULT NULL,
  `complaint_id` BIGINT UNSIGNED DEFAULT NULL,
  `rating` TINYINT UNSIGNED DEFAULT NULL,
  `feedback_text` LONGTEXT NOT NULL,
  `is_read` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_feedback_read` (`is_read`, `created_at`),
  KEY `idx_feedback_complaint` (`complaint_id`),
  CONSTRAINT `fk_feedback_complaint`
    FOREIGN KEY (`complaint_id`) REFERENCES `complaints` (`id`)
    ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `visitor_stats` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `visit_date` DATE NOT NULL,
  `page_path` VARCHAR(255) NOT NULL,
  `visit_count` INT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_visit_page_date` (`visit_date`, `page_path`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `system_settings` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `setting_key` VARCHAR(100) NOT NULL,
  `setting_value` TEXT DEFAULT NULL,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `complaint_reference_sequence` (
  `id` TINYINT UNSIGNED NOT NULL,
  `last_reference` BIGINT UNSIGNED NOT NULL,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `password_reset_tokens` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `token_hash` VARCHAR(255) NOT NULL,
  `expires_at` DATETIME NOT NULL,
  `used_at` DATETIME DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_reset_user` (`user_id`),
  KEY `idx_reset_expiry` (`expires_at`),
  CONSTRAINT `fk_reset_user`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `audit_logs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED DEFAULT NULL,
  `action` VARCHAR(100) NOT NULL,
  `entity_type` VARCHAR(100) DEFAULT NULL,
  `entity_id` BIGINT UNSIGNED DEFAULT NULL,
  `details_json` LONGTEXT DEFAULT NULL,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `user_agent` VARCHAR(500) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_audit_user` (`user_id`),
  KEY `idx_audit_entity` (`entity_type`, `entity_id`),
  KEY `idx_audit_created` (`created_at`),
  CONSTRAINT `fk_audit_user`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
    ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `escalation_officer_levels` (
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

CREATE TABLE `complaint_escalations` (
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

SET FOREIGN_KEY_CHECKS = 1;

