# Local-only preview/opt-in worker for the PDF allocation/escalation workflow.
# This file NEVER selects or imports real employee identities.
#
# Default: read-only preview.
# Local execution (after official stage approvals and backup):
#   powershell -NoProfile -ExecutionPolicy Bypass -File .\tools\run_pdf_workflow_test.ps1 -ExecuteLocal
#
# To make it periodic, a Windows Task Scheduler operator
# must explicitly configure a scheduled action pointing to this script
# *after* reviewing personnel mapping, readiness, logs, and database backup.
param(
    [switch]$ExecuteLocal
)

$ErrorActionPreference = 'Stop'
$root = (Resolve-Path (Join-Path $PSScriptRoot '..')).Path
$php = 'D:\XAMPP\php\php.exe'
$db = 'smartcitytiruppur'
if ((Split-Path -Leaf $root) -ne 'smartcitytiruppur') { throw 'Run this from the main local smartcitytiruppur project only.' }
if (-not (Test-Path -LiteralPath $php)) {
    throw "PHP binary unavailable: $php"
}
$mode = if ($ExecuteLocal) { '--execute' } else { '--dry-run' }
$target = "--expect-db=$db"
$logDir = Join-Path $root 'storage\logs'
if (-not (Test-Path -LiteralPath $logDir)) {
    New-Item -ItemType Directory -Path $logDir -Force | Out-Null
}
$logPath = Join-Path $logDir 'pdf-workflow-local.log'
$stamp = Get-Date -Format 'yyyy-MM-dd HH:mm:ss'
"[$stamp] START mode=$mode DB=$db" | Out-File -FilePath $logPath -Append -Encoding utf8

$jobs = @(
    'retry-pending-allocations.php',
    'pdf-escalate-complaints.php'
)

foreach ($job in $jobs) {
    $script = Join-Path $root $job
    if (-not (Test-Path -LiteralPath $script)) { throw "Missing script: $script" }
    Write-Host "Running $job ($mode / $db)"
    & $php $script $mode $target 2>&1 | ForEach-Object {
        $line = [string]$_
        Write-Host $line
        $line | Out-File -FilePath $logPath -Append -Encoding utf8
    }
    $exitCode = $LASTEXITCODE
    if ($exitCode -ne 0) {
        "[$(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')] FAILED $job exit=$exitCode" |
            Out-File -FilePath $logPath -Append -Encoding utf8
        throw "$job failed with exit code $exitCode. The next job was not run."
    }
}
"[$(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')] END OK" |
    Out-File -FilePath $logPath -Append -Encoding utf8
Write-Host "Completed; log: $logPath"
