@echo off
setlocal EnableDelayedExpansion

REM ==============================================================================
REM Smart City Tiruppur - Read-Only PDF Workflow Preview
REM Never modifies any database records.
REM ==============================================================================

set SCRIPT_DIR=%~dp0
call "%SCRIPT_DIR%run_pdf_workflow.cmd" --dry-run %*
exit /b %ERRORLEVEL%
