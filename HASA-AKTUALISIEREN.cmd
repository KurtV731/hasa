@echo off
setlocal
title HASA aktualisieren

pushd "%~dp0" >nul 2>&1
if errorlevel 1 (
    echo.
    echo FEHLER: Der HASA-Ordner konnte nicht geoeffnet werden.
    echo Starte diese Datei bitte direkt aus dem HASA-Hauptordner.
    echo.
    pause
    exit /b 1
)

where git >nul 2>&1
if errorlevel 1 (
    echo.
    echo FEHLER: Git wurde auf diesem Computer nicht gefunden.
    echo.
    popd
    pause
    exit /b 1
)

if not exist ".git\" (
    echo.
    echo FEHLER: Dieser Ordner ist kein lokales HASA-Git-Repository.
    echo Erwartet wurde der Repository-Ordner, zum Beispiel C:\Hasa.
    echo.
    popd
    pause
    exit /b 1
)

echo.
echo ==========================================
echo       HASA wird von GitHub aktualisiert
echo ==========================================
echo.
echo Ordner: %CD%
echo.

git pull --ff-only
if errorlevel 1 (
    echo.
    echo ==========================================
    echo FEHLER: HASA wurde nicht aktualisiert.
    echo ==========================================
    echo.
    echo Das Fenster bleibt offen, damit die Meldung gelesen werden kann.
    echo Es wurden keine Dateien absichtlich geloescht oder zurueckgesetzt.
    echo.
    popd
    pause
    exit /b 1
)

echo.
echo ==========================================
echo HASA ist auf dem aktuellen GitHub-Stand.
echo ==========================================
echo.

set "HASA_QUELLDATEI=%CD%\2 src\current\HASA-AKTUELL.user.js.txt"

if not exist "%HASA_QUELLDATEI%" (
    echo FEHLER: Die aktuelle HASA-Programmdatei wurde nicht gefunden:
    echo %HASA_QUELLDATEI%
    echo.
    echo Der Quellordner wird stattdessen geoeffnet.
    if exist "2 src\current\" start "" explorer.exe "%CD%\2 src\current"
    popd
    pause
    exit /b 1
)

powershell.exe -NoProfile -ExecutionPolicy Bypass -Command "$p = [System.IO.Path]::GetFullPath($env:HASA_QUELLDATEI); $t = [System.IO.File]::ReadAllText($p, [System.Text.Encoding]::UTF8); Set-Clipboard -Value $t"
if errorlevel 1 (
    echo.
    echo FEHLER: Die HASA-Datei konnte nicht in die Zwischenablage kopiert werden.
    echo Die Datei wird deshalb im Explorer angezeigt.
    start "" explorer.exe /select,"%HASA_QUELLDATEI%"
    echo.
    popd
    pause
    exit /b 1
)

echo.
echo ==========================================
echo HASA Alpha 8 liegt jetzt vollstaendig
echo in der Windows-Zwischenablage.
echo ==========================================
echo.
echo Naechster Schritt in Tampermonkey:
echo   1. Vorhandenes HASA-Skript im Editor oeffnen.
echo   2. Strg+A druecken.
echo   3. Strg+V druecken.
echo   4. Speichern.
echo.
echo Es wurde kein zweites Tampermonkey-Skript angelegt.
echo.

popd
pause
exit /b 0
