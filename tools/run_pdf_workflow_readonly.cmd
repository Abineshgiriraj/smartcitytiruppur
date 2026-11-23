@echo off
setlocal EnableExtensions
REM PDF-based allocation and escalation PREVIEW on the isolated TEST database.
REM Runs PHP directly: does not require PowerShell execution-policy changes.
REM Usage from project folder: .\tools\run_pdf_workflow_readonly.cmd 950324
REM Omit complaint ID to inspect bounded queues. NEVER changes complaint records.

set "PHP=D:\XAMPP\php\php.exe"
set "ROOT=%~dp0.."
set "DB=smartcity_escalation_test"
set "FILTER="
if not "%~1"=="" set "FILTER=--complaint-id=%~1"

if not exist "%PHP%" (
  echo ERROR: PHP binary not found: %PHP%
  exit /b 2
)
if not exist "%ROOT%\retry-pending-allocations.php" (
  echo ERROR: allocation worker not found
  exit /b 2
)
if not exist "%ROOT%\pdf-escalate-complaints.php" (
  echo ERROR: escalation worker not found
  exit /b 2
)

echo.
echo Read-only PDF workflow preview. Database: %DB%
echo No officer approvals or assignments will be created.
echo.

echo [1/2] Check pending allocations
"%PHP%" "%ROOT%\retry-pending-allocations.php" --dry-run --expect-db=%DB% %FILTER%
if errorlevel 1 (
  echo ERROR: allocation preview failed
  exit /b 1
)
echo.
echo [2/2] Check PDF escalation status
"%PHP%" "%ROOT%\pdf-escalate-complaints.php" --dry-run --expect-db=%DB% %FILTER%
if errorlevel 1 (
  echo ERROR: escalation preview failed
  exit /b 1
)

echo.
echo Completed READ-ONLY preview. No database records modified by these workers.
exit /b 0
