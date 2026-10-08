@echo off
setlocal
powershell.exe -NoProfile -ExecutionPolicy Bypass -File "%~dp03 server\tools\server-update.ps1"
set "HASA_UPDATE_RESULT=%errorlevel%"
echo.
pause
exit /b %HASA_UPDATE_RESULT%
