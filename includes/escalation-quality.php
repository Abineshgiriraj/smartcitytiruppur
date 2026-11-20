<?php
declare(strict_types=1);

/**
 * A conservative LABEL COMPARISON, never certification of a staff appointment.
 * Known aliases in brackets or separated by '/' can match literally.
 * Anything else requires human review, even if it might be a valid alias.
 */
function escalation_designation_check(?string $configured, ?string $recorded): string
{
    $recorded = trim((string)$recorded);
    if ($recorded === '') return 'missing';

    $normalize = static function (string $value): string {
        return preg_replace('/[^a-z0-9]/', '', strtolower(trim($value))) ?? '';
    };
    $actual = $normalize($recorded);
    if ($actual === '') return 'review';

    $configured = trim((string)$configured);
    $parts = preg_split('/[\/()]+/', $configured) ?: [];
    $parts[] = $configured;
    foreach ($parts as $part) {
        $key = $normalize($part);
        if ($key !== '' && $key === $actual) return 'text_match';
    }
    return 'review';
}

/**
 * Inspect a stage row from the read-only auditing queries.
 * Returns independent issues; an empty issue list is NOT personnel approval.
 */
function escalation_rule_issues(array $rule): array
{
    $issues = [];
    if (trim((string)($rule['officer_name'] ?? '')) === '') $issues[] = 'Missing officer name';
    if ((int)($rule['officer_active'] ?? 0) !== 1) $issues[] = 'Inactive officer';
    if ((int)($rule['department_match'] ?? 0) !== 1) $issues[] = 'No department mapping';
    if ((int)($rule['category_match'] ?? 0) !== 1) $issues[] = 'No category mapping';
    if ((int)($rule['escalation_period_days'] ?? 0) < 1) $issues[] = 'Invalid SLA days';
    $designation = escalation_designation_check(
        (string)($rule['configured_role'] ?? ''),
        $rule['actual_designation'] ?? null
    );
    if ($designation === 'missing') $issues[] = 'Designation missing';
    if ($designation === 'review') $issues[] = 'Designation text differs';
    return $issues;
}
