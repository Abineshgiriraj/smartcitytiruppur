@echo off
REM Smart City Tiruppur - Automatic PDF Escalation Runner
REM Runs every 5 minutes via Windows Task Scheduler
REM Prevents overlapping execution using GET_LOCK in pdf-escalate-complaints.php

cd /d "D:\XAMPP\htdocs\test\smartcitytiruppur"
"D:\XAMPP\php\php.exe" pdf-escalate-complaints.php --execute --expect-db=smartcitytiruppur >> "D:\XAMPP\htdocs\test\smartcitytiruppur\scratch\escalation_cron.log" 2>&1
