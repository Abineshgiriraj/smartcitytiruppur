@echo off
setlocal EnableDelayedExpansion

REM ==============================================================================
REM Smart City Tiruppur - Local PDF Allocation & Escalation Workflow Runner
REM Strictly bound to local smartcitytiruppur database.
REM
REM Usage:
REM   .\tools\run_pdf_workflow.cmd                  (Read-only status overview)
REM   .\tools\run_pdf_workflow.cmd --dry-run        (Read-only preview of all pending)
REM   .\tools\run_pdf_workflow.cmd --dry-run 576866 (Preview for complaint ID)
REM   .\tools\run_pdf_workflow.cmd --execute 576866 (Explicit authorized execution for complaint)
REM   .\tools\run_pdf_workflow.cmd --execute        (Explicit bounded execution for approved queue)
REM ==============================================================================

set PHP=D:\XAMPP\php\php.exe
set ROOT=%~dp0..
set DB=smartcitytiruppur
set LOG_DIR=%ROOT%\storage\logs
set MODE=--dry-run
set FILTER=

if not exist "%PHP%" (
    echo ERROR: PHP binary not found at %PHP%
    exit /b 2
)
if not exist "%LOG_DIR%" (
    mkdir "%LOG_DIR%"
)

:loop
if "%~1"=="" goto after_loop
if /i "%~1"=="--dry-run" (
    set MODE=--dry-run
    shift
    goto loop
)
if /i "%~1"=="--execute" (
    set MODE=--execute
    shift
    goto loop
)
echo %~1| findstr /R "^[0-9][0-9]*$" >nul
if not errorlevel 1 (
    set FILTER=--complaint-id=%~1
    shift
    goto loop
)
echo %~1| findstr /R "^TCMC-" >nul
if not errorlevel 1 (
    set FILTER=--reference=%~1
    shift
    goto loop
)
shift
goto loop

:after_loop
echo ==============================================================================
echo Smart City Tiruppur Workflow Runner: Mode=%MODE% DB=%DB% Filter=%FILTER%
echo ==============================================================================

if "%FILTER%"=="" (
    if "%MODE%"=="--dry-run" (
        "%PHP%" "%ROOT%\tools\report_local_pdf_workflow.php"
        exit /b %ERRORLEVEL%
    )
)

echo [1/2] Pending Allocation Phase (%MODE%)
"%PHP%" "%ROOT%\retry-pending-allocations.php" %MODE% --expect-db=%DB% %FILTER%
if errorlevel 1 (
    echo ERROR: Allocation phase failed with code %ERRORLEVEL%
    exit /b %ERRORLEVEL%
)

echo.
echo [2/2] PDF Escalation Phase (%MODE%)
"%PHP%" "%ROOT%\pdf-escalate-complaints.php" %MODE% --expect-db=%DB% %FILTER%
if errorlevel 1 (
    echo ERROR: Escalation phase failed with code %ERRORLEVEL%
    exit /b %ERRORLEVEL%
)

echo.
echo Workflow phase completed successfully.
exit /b 0
