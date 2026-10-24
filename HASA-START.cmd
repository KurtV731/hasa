@echo off
setlocal
:menu
cls
echo HASA - C:\ppd\hasa
echo.
echo 1  Programmdatei von GitHub aktualisieren
echo 2  API auf hasa.li aktualisieren
echo 3  Startseite und Downloads hochladen
echo 4  Website-Ordner oeffnen
echo 5  Dokumentation oeffnen
echo 0  Beenden
choice /c 123450 /n /m "Bitte waehlen: "
if errorlevel 6 exit /b 0
if errorlevel 5 (start "" explorer.exe "%~dp003 Dokumentation" & goto menu)
if errorlevel 4 (start "" explorer.exe "%~dp002 Website" & goto menu)
if errorlevel 3 (call "%~dp005 Verwaltung\HASA-Website-Hochladen.bat" & goto menu)
if errorlevel 2 (call "%~dp005 Verwaltung\HASA-Serverupdate-hasa-li.bat" & goto menu)
if errorlevel 1 (call "%~dp005 Verwaltung\HASA-AKTUALISIEREN.cmd" & goto menu)
