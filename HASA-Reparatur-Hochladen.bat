@echo off
setlocal
set "HASA_REPAIR_BATCH=%~f0"
powershell.exe -NoProfile -ExecutionPolicy Bypass -Command "$parts=[IO.File]::ReadAllText($env:HASA_REPAIR_BATCH) -split '(?m)^# POWERSHELL-START\r?$',2; if($parts.Count -ne 2){exit 1}; & ([ScriptBlock]::Create($parts[1]))"
set "HASA_REPAIR_RESULT=%errorlevel%"
echo.
pause
exit /b %HASA_REPAIR_RESULT%
# POWERSHELL-START
$ErrorActionPreference = 'Stop'
$tempFolder = $null
function Quote-WinSCP([string]$value) {
    if ($value -match '[\r\n]') { throw 'Ungueltiger Verbindungsname oder Pfad.' }
    return '"' + $value.Replace('"', '""') + '"'
}
try {
    Write-Host 'HASA - nur die vollstaendige Reparaturdatei hochladen'
    Write-Host 'Lokale Website-Dateien und Git-Aenderungen bleiben erhalten.'
    $winscp = @(${env:ProgramFiles(x86)}, $env:ProgramFiles) |
        Where-Object { $_ } | ForEach-Object { Join-Path $_ 'WinSCP\WinSCP.com' } |
        Where-Object { Test-Path -LiteralPath $_ } | Select-Object -First 1
    if (-not $winscp) {
        $found = Get-Command WinSCP.com -ErrorAction SilentlyContinue
        if ($found) { $winscp = $found.Source }
    }
    if (-not $winscp) { throw 'WinSCP.com wurde nicht gefunden.' }
    $sessions = 'HKCU:\Software\Martin Prikryl\WinSCP 2\Sessions'
    $site = $null
    if (Test-Path $sessions) {
        foreach ($key in Get-ChildItem $sessions) {
            $settings = Get-ItemProperty $key.PSPath
            if ($settings.HostName -eq 'ftp.webspace.bz' -and $settings.UserName -eq 'kd239663') {
                $site = [Uri]::UnescapeDataString($key.PSChildName)
                break
            }
        }
    }
    if (-not $site) { throw 'Die gespeicherte allgemeine Website-Verbindung (ftp.webspace.bz, kd239663) wurde nicht gefunden. Bitte diese Verbindung einmal in WinSCP speichern. Das eingeschraenkte HASA-Konto passt hier nicht.' }
    [Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12
    $tempFolder = Join-Path ([IO.Path]::GetTempPath()) ('HASA-Reparatur-' + [Guid]::NewGuid().ToString('N'))
    New-Item -ItemType Directory -Path $tempFolder | Out-Null
    $file = Join-Path $tempFolder 'HASA-Reparatur-aktuell.user.js.txt'
    $url = 'https://raw.githubusercontent.com/KurtV731/serkal-pages/main/up/download/others/hasa/HASA-Reparatur-aktuell.user.js.txt'
    Write-Host 'Hole die aktuelle Reparaturdatei ...'
    Invoke-WebRequest -UseBasicParsing -Uri $url -OutFile $file
    $content = [IO.File]::ReadAllText($file)
    if ($content -notmatch '// ==UserScript==' -or $content -notmatch '// ==/UserScript==' -or $content -notmatch 'const HASA_VERSION') {
        throw 'Die heruntergeladene Datei ist kein vollstaendiges HASA-Userscript. Kein Upload.'
    }
    if ($content -match '@version\s+([^\r\n]+)') { Write-Host ('Fassung: ' + $Matches[1].Trim()) }
    $script = Join-Path $tempFolder 'upload.txt'
    $commands = @(
        'option batch abort',
        'option confirm off',
        ('open ' + (Quote-WinSCP $site)),
        'cd "/download/others/hasa"',
        ('put -transfer=binary ' + (Quote-WinSCP $file) + ' "HASA-Reparatur-aktuell.user.js.txt"'),
        'exit'
    )
    [IO.File]::WriteAllLines($script, $commands, (New-Object Text.UTF8Encoding($true)))
    Write-Host 'Lade genau eine Reparaturdatei auf serkal.de hoch ...'
    & $winscp ('/script=' + $script)
    if ($LASTEXITCODE -ne 0) { throw 'WinSCP-Upload fehlgeschlagen. Bitte die Meldung oben beachten; ein unvollstaendiger Upload ist moeglich.' }
    Write-Host 'Fertig: Reparaturdatei erfolgreich hochgeladen.' -ForegroundColor Green
    Write-Host 'Kundenlink: https://serkal.de/download/others/hasa/HASA-Reparatur-aktuell.user.js.txt'
    Write-Host 'Diese Batch uebertraegt keine HTML-Seiten und keine PHP-Dateien.'
    exit 0
} catch {
    Write-Host ('FEHLER: ' + $_.Exception.Message) -ForegroundColor Red
    exit 1
} finally {
    if ($tempFolder -and (Test-Path -LiteralPath $tempFolder)) {
        Remove-Item -LiteralPath $tempFolder -Recurse -Force
    }
}
