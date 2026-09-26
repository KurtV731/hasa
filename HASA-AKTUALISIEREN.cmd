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

if exist "2 src\current\" (
    start "" explorer.exe "%CD%\2 src\current"
) else (
    echo HINWEIS: Der Ordner "2 src\current" wurde nicht gefunden.
)

popd
pause
exit /b 0
