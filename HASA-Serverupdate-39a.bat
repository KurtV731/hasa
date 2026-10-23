@echo off
setlocal
powershell.exe -NoProfile -ExecutionPolicy Bypass -File "%~dp0server-update-39a.ps1"
set "HASA39A_RESULT=%errorlevel%"
echo.
pause
exit /b %HASA39A_RESULT%
