<?php
declare(strict_types=1);

/**
 * All PDF allocation/escalation worker invocations use the same, already
 * configured local main database. Never infer DB from user arguments.
 * This does not bypass PDF policy or municipal staff approvals.
 */
function enforce_local_pdf_workflow(PDO $pdo, ?string $requestedDb, bool $execute): string
{
    $db = (string)$pdo->query('SELECT DATABASE()')->fetchColumn();
    $required = 'smartcitytiruppur';
    if ($requestedDb !== $required || $db !== $required || DB_NAME !== $required) {
        fwrite(STDERR, "Database mismatch: configured=" . DB_NAME
            . ", actual={$db}, requested=" . ($requestedDb ?? '[none]')
            . ". Required={$required}. No writes.\n");
        exit(2);
    }
    if ($execute) {
        $localHosts = ['127.0.0.1','localhost','::1'];
        $projectFolder = basename((string)realpath(dirname(__DIR__)));
        if (APP_ENV !== 'local'
            || !in_array(strtolower((string)DB_HOST), $localHosts, true)
            || $projectFolder !== 'smartcitytiruppur'
            || PHP_SAPI !== 'cli') {
            fwrite(STDERR,
                "Write mode is enabled ONLY in D:\\XAMPP\\htdocs\\test\\smartcitytiruppur "
                . "using local PHP CLI and local smartcitytiruppur DB. No writes.\n"
            );
            exit(2);
        }
    }
    return $db;
}
