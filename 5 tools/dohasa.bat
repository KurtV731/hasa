@echo off
set "ROOT=C:\Hasa"

mkdir "%ROOT%\1 docs\roadmap"
mkdir "%ROOT%\1 docs\werkstatt"
mkdir "%ROOT%\1 docs\spezifikation"

mkdir "%ROOT%\2 src\current"
mkdir "%ROOT%\2 src\archive"

mkdir "%ROOT%\3 data\stan"
mkdir "%ROOT%\3 data\techtree"
mkdir "%ROOT%\3 data\testdaten"

mkdir "%ROOT%\4 debug"
mkdir "%ROOT%\5 tools"
mkdir "%ROOT%\6 tests"

echo.
echo HASA-Verzeichnisstruktur wurde unter %ROOT% angelegt.
pause