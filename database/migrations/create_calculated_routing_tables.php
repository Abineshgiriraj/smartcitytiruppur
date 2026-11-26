<?php
require_once __DIR__ . '/../../includes/bootstrap.php';
$pdo = db();

echo "Creating additive tables for calculated routing and reassignment plans...\n";

// Table 1: complaint_calculated_routing
// Stores the reconstructed theoretical/historical routing and milestone deadlines for all complaints
// completely separate from actual assignment/escalation events.
$pdo->exec("
CREATE TABLE IF NOT EXISTS `complaint_calculated_routing` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `complaint_id` BIGINT UNSIGNED NOT NULL,
    `stage_no` TINYINT UNSIGNED NOT NULL,
    `role_label` VARCHAR(150) NOT NULL,
    `officer_id` INT UNSIGNED DEFAULT NULL,
    `period_days` SMALLINT UNSIGNED NOT NULL,
    `stage_start_at` DATETIME NOT NULL,
    `stage_due_at` DATETIME NOT NULL,
    `is_completed` TINYINT(1) NOT NULL DEFAULT 0,
    `completed_at` DATETIME DEFAULT NULL,
    `routing_type` ENUM('raw_lifecycle_verified', 'pdf_policy_calculated', 'ward_rule_matched', 'fallback_intake') NOT NULL,
    `notes` VARCHAR(255) DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_comp_stage` (`complaint_id`, `stage_no`),
    KEY `idx_officer_stage` (`officer_id`, `stage_no`),
    KEY `idx_complaint` (`complaint_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
");
echo "Table `complaint_calculated_routing` created/verified.\n";

// Table 2: complaint_reassignment_plans
// Stores dry-run and administrator-approved reassignment proposals for pending/unassigned/inactive officer cases.
$pdo->exec("
CREATE TABLE IF NOT EXISTS `complaint_reassignment_plans` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `complaint_id` BIGINT UNSIGNED NOT NULL,
    `current_status` VARCHAR(50) NOT NULL,
    `current_officer_id` INT UNSIGNED DEFAULT NULL,
    `current_officer_active` TINYINT(1) DEFAULT 0,
    `proposed_officer_id` INT UNSIGNED NOT NULL,
    `proposed_stage_no` TINYINT UNSIGNED NOT NULL DEFAULT 1,
    `calculated_due_at` DATETIME DEFAULT NULL,
    `reassignment_reason` VARCHAR(255) NOT NULL,
    `evidence_source` ENUM('raw_lifecycle', 'pdf_policy_ward', 'pdf_policy_category', 'intake_fallback') NOT NULL,
    `plan_status` ENUM('proposed', 'approved', 'applied', 'rejected') NOT NULL DEFAULT 'proposed',
    `applied_at` DATETIME DEFAULT NULL,
    `applied_by` INT UNSIGNED DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_plan_complaint` (`complaint_id`),
    KEY `idx_proposed_officer` (`proposed_officer_id`),
    KEY `idx_plan_status` (`plan_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
");
echo "Table `complaint_reassignment_plans` created/verified.\n";

// Table 3: complaint_routing_audit_summary
// Caches top-level stats for the Complaint Routing Analysis page
$pdo->exec("
CREATE TABLE IF NOT EXISTS `complaint_routing_audit_summary` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `audit_date` DATETIME NOT NULL,
    `total_complaints` INT UNSIGNED NOT NULL,
    `closed_complaints` INT UNSIGNED NOT NULL,
    `open_complaints` INT UNSIGNED NOT NULL,
    `valid_assigned_open` INT UNSIGNED NOT NULL,
    `unassigned_open` INT UNSIGNED NOT NULL,
    `inactive_assigned_open` INT UNSIGNED NOT NULL,
    `proposed_reassignments` INT UNSIGNED NOT NULL,
    `proposed_escalations` INT UNSIGNED NOT NULL,
    `details_json` LONGTEXT DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
");
echo "Table `complaint_routing_audit_summary` created/verified.\n";
