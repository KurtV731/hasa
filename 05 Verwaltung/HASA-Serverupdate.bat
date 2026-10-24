@echo off
setlocal
powershell.exe -NoProfile -ExecutionPolicy Bypass -File "%~dp0Werkzeuge\server-update.ps1" -Profile hasa-li
set "HASA_UPDATE_RESULT=%errorlevel%"
pause
exit /b %HASA_UPDATE_RESULT%
