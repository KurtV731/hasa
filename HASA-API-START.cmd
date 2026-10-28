@echo off
setlocal EnableExtensions
title HASA - API nach hasa.li hochladen

set "HASA_ROOT=%~dp0"
if "%HASA_ROOT:~-1%"=="\" set "HASA_ROOT=%HASA_ROOT:~0,-1%"
set "HASA_SETTINGS_DIR=%LOCALAPPDATA%\HASA"
set "HASA_SETTINGS=%HASA_SETTINGS_DIR%\server-update-hasa-li.json"
set "HASA_UPDATER=%HASA_ROOT%\05 Verwaltung\Werkzeuge\server-update.ps1"

cls
echo.
echo ============================================================
echo   HASA - API direkt nach hasa.li hochladen
echo ============================================================
echo.
echo WinSCP-Verbindung: HASA-li
echo Server-Zielordner: /hasa.li/api/
echo.

if not exist "%HASA_ROOT%\.git" goto NO_GIT
if not exist "%HASA_UPDATER%" goto NO_UPDATER

if not exist "%HASA_SETTINGS_DIR%" mkdir "%HASA_SETTINGS_DIR%"
if errorlevel 1 goto SETTINGS_ERROR

> "%HASA_SETTINGS%" echo {
>>"%HASA_SETTINGS%" echo   "site": "HASA-li",
>>"%HASA_SETTINGS%" echo   "remote": "/hasa.li/api/"
>>"%HASA_SETTINGS%" echo }
if errorlevel 1 goto SETTINGS_ERROR

powershell.exe -NoProfile -ExecutionPolicy Bypass -File "%HASA_UPDATER%" -Profile hasa-li
set "HASA_RESULT=%errorlevel%"

echo.
if not "%HASA_RESULT%"=="0" goto FAIL

echo ============================================================
echo   FERTIG - HASA-API wurde nach hasa.li uebertragen
echo ============================================================
echo.
pause
exit /b 0

:NO_GIT
echo FEHLER: HASA-Git-Repository nicht gefunden:
echo "%HASA_ROOT%"
goto FAIL

:NO_UPDATER
echo FEHLER: API-Uploader nicht gefunden:
echo "%HASA_UPDATER%"
goto FAIL

:SETTINGS_ERROR
echo FEHLER: Die fertigen HASA-li-Einstellungen konnten nicht gespeichert werden:
echo "%HASA_SETTINGS%"
goto FAIL

:FAIL
echo.
echo Der API-Upload wurde nicht erfolgreich abgeschlossen.
echo.
pause
exit /b 1
