<?php
declare(strict_types=1);

/** PDF escalation policy: exact category-level stage count, designation and period. */
function pdf_policy_source_rows(array $source): array
{
    if (($source['source_rows'] ?? null) !== 301 || count($source['policies'] ?? []) !== 301) {
        throw new RuntimeException('PDF reference must contain exactly 301 category workflows.');
    }
    $seen = []; $totalStages = 0;
    foreach ($source['policies'] as $policy) {
        if (empty($policy['department']) || empty($policy['category']) || empty($policy['stages'])
            || count($policy['stages']) > 6) {
            throw new RuntimeException('Invalid PDF policy row ' . ($policy['s_no'] ?? '?'));
        }
        $sum = 0;
        foreach ($policy['stages'] as $offset => $stage) {
            if ((int)$stage['stage'] !== $offset + 1 || empty($stage['role'])
                || !is_int($stage['days']) || $stage['days'] < 0) {
                throw new RuntimeException('Invalid PDF stage at row ' . $policy['s_no']);
            }
            $sum += $stage['days']; $totalStages++;
        }
        if ($sum !== (int)$policy['total_days']) {
            throw new RuntimeException('PDF total days mismatch at row '.$policy['s_no']);
        }
        $key = pdf_policy_key($policy['department']).'|'.pdf_policy_key($policy['category']);
        if (isset($seen[$key])) throw new RuntimeException('Duplicate PDF category '.$key);
        $seen[$key] = true;
    }
    if ($totalStages !== 1409) throw new RuntimeException('Expected 1,409 PDF stage rows.');
    return $source['policies'];
}

function pdf_policy_key(string $label): string
{
    $label = preg_replace('/\s+/u', ' ', trim($label)) ?? trim($label);
    return function_exists('mb_strtolower') ? mb_strtolower($label, 'UTF-8') : strtolower($label);
}

/** Fetch only reviewed policies. No fallback to unsafe 4-stage seeds. */
function pdf_policy_reviewed(PDO $pdo, int $categoryId): ?array
{
    try {
        $header = $pdo->prepare(
            "SELECT category_id, source_row, stage_count, total_days
             FROM pdf_escalation_policies
             WHERE category_id=? AND is_approved=1 AND approval_reference IS NOT NULL
               AND TRIM(approval_reference) <> ''
               AND UPPER(TRIM(approval_reference)) NOT LIKE 'SIMULATION-LOCAL-TEST-%'
               AND approved_by IS NOT NULL AND approved_at IS NOT NULL
               AND pdf_sha256='03a496809403a2de6a597a844a36ffc979de47657541342924628c8d33d70c4b'
             LIMIT 1"
        );
        $header->execute([$categoryId]);
        $policy = $header->fetch(PDO::FETCH_ASSOC);
        if (!$policy) return null;
        $stages = $pdo->prepare(
            'SELECT stage_no, role_label, period_days
             FROM pdf_escalation_policy_stages WHERE category_id=? ORDER BY stage_no'
        );
        $stages->execute([$categoryId]);
        $rows = $stages->fetchAll(PDO::FETCH_ASSOC);
        if (count($rows) !== (int)$policy['stage_count'] || count($rows) < 1 || count($rows) > 6) return null;
        $sum = 0;
        foreach ($rows as $i => $row) {
            $days = (int)$row['period_days'];
            // Zero-day entries remain blocked until the officially reviewed
            // source is replaced with an explicitly approved nonzero period.
            if ((int)$row['stage_no'] !== $i + 1 || $days <= 0) return null;
            $sum += $days;
        }
        if ($sum !== (int)$policy['total_days']) return null;
        $policy['stages'] = $rows;
        return $policy;
    } catch (Throwable $error) {
        if (function_exists('log_message')) log_message('Reviewed PDF escalation policy unavailable: '.$error->getMessage());
        return null;
    }
}

function pdf_policy_stage(array $policy, int $number): ?array
{
    if ($number < 1 || $number > (int)$policy['stage_count']) return null;
    $step = $policy['stages'][$number - 1] ?? null;
    return $step && (int)$step['stage_no'] === $number ? $step : null;
}

/** Strict role guard to prevent an official stage being sent to a different rank. */
function pdf_policy_role_matches(string $expectedRole, string $officerDesignation): bool
{
    $clean = static function(string $s): string {
        return trim(preg_replace('/[^A-Z0-9]+/',' ',strtoupper($s)) ?? '');
    };
    $role=$clean($expectedRole);
    $actual=$clean($officerDesignation);
    if($role===''||$actual==='')return false;
    $has = static fn(string $haystack,string $word):bool =>
        (bool)preg_match('/(?:^| )'.preg_quote($word,'/').'(?: |$)/',$haystack);
    if($role==='COMMR') {
        return ($has($actual,'COMMR') || str_contains($actual,'COMMISSIONER'))
            && !str_contains($actual,'DEPUTY') && !$has($actual,'DY');
    }
    if($role==='DEPUTY COMMR') {
        return (str_contains($actual,'DEPUTY')||$has($actual,'DY'))
            && (str_contains($actual,'COMMISSIONER')||$has($actual,'COMMR'));
    }
    if($role==='AEE' || $role==='AEE UGD') {
        return $has($actual,'AEE') || str_contains($actual,'ASSISTANT EXECUTIVE ENGINEER');
    }
    // The PDF distinguishes AE(ELECTRICAL) from general AE/JE or other
    // engineering specialities. Never treat a generic AE title as evidence
    // of the Electrical appointment, even with a stage approval record.
    if ($role==='AE ELECTRICAL') {
        return ($has($actual,'AE')
            || (str_contains($actual,'ASSISTANT ENGINEER') && !str_contains($actual,'EXECUTIVE')))
            && $has($actual,'ELECTRICAL');
    }
    if($role==='AE' || $role==='AE JE' || str_starts_with($role,'AE ')) {
        return $has($actual,'AE') || $has($actual,'JE')
            || (str_contains($actual,'ASSISTANT ENGINEER') && !str_contains($actual,'EXECUTIVE'))
            || str_contains($actual,'JUNIOR ENGINEER');
    }
    if($role==='EE'||str_starts_with($role,'EE ')) {
        return $has($actual,'EE') ||
            (str_contains($actual,'EXECUTIVE ENGINEER') && !str_contains($actual,'ASSISTANT'));
    }
    if($role==='CE') return $has($actual,'CE') || str_contains($actual,'CITY ENGINEER') || str_contains($actual,'CHIEF ENGINEER');
    $alias=[
        'SI'=>'SANITARY INSPECTOR','SO'=>'SANITARY OFFICER','CHO'=>'CITY HEALTH OFFICER',
        'SRI'=>'SENIOR REVENUE INSPECTOR','ACR'=>'ASSISTANT COMMISSIONER REVENUE'
    ];
    if($role===$actual)return true;
    if(isset($alias[$role]) && $actual===$alias[$role])return true;
    return false;
}
