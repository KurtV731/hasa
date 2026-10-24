@echo off
setlocal
powershell.exe -NoProfile -ExecutionPolicy Bypass -File "%~dp0Werkzeuge\website-upload.ps1"
set "HASA_UPDATE_RESULT=%errorlevel%"
pause
exit /b %HASA_UPDATE_RESULT%
