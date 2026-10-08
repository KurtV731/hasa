# HASA: Git Pull und Upload in einem Aufruf. Keine Zugangsdaten im Repository.
$ErrorActionPreference = 'Stop'
$repo = Split-Path (Split-Path $PSScriptRoot -Parent) -Parent
$privateSettings = Join-Path $env:LOCALAPPDATA 'HASA\server-update.json'
$tempScript = $null
function Quote-WinSCP([string]$value) {
    if ($value -match '[\r\n]') { throw 'Zeilenumbrueche sind in Verbindung oder Pfad nicht erlaubt.' }
    return '"' + $value.Replace('"', '""') + '"'
}
try {
    if (-not (Get-Command git.exe -ErrorAction SilentlyContinue)) { throw 'Git wurde nicht gefunden.' }
    $winscp = @(${env:ProgramFiles(x86)}, $env:ProgramFiles) |
        Where-Object { $_ } | ForEach-Object { Join-Path $_ 'WinSCP\WinSCP.com' } |
        Where-Object { Test-Path $_ } | Select-Object -First 1
    if (-not $winscp) {
        $found = Get-Command WinSCP.com -ErrorAction SilentlyContinue
        if ($found) { $winscp = $found.Source }
    }
    if (-not $winscp) { throw 'WinSCP.com wurde nicht gefunden. WinSCP muss installiert sein.' }
    if (Test-Path $privateSettings) {
        $settings = Get-Content -LiteralPath $privateSettings -Raw | ConvertFrom-Json
    } else {
        Write-Host 'Einmalige Einrichtung: Verwende deine bereits gespeicherte HASA-Verbindung in WinSCP.'
        $site = (Read-Host 'Exakter Name der gespeicherten WinSCP-Verbindung').Trim()
        Write-Host 'Zielordner wie in WinSCP: beim eingeschraenkten HASA-FTP-Konto normalerweise /; bei einem allgemeinen Konto /hasa/.'
        $remote = (Read-Host 'Zielordner fuer die HASA-PHP-Dateien').Trim()
        $settings = [pscustomobject]@{ site = $site; remote = $remote }
    }
    if ([string]::IsNullOrWhiteSpace($settings.site)) { throw 'Name der gespeicherten Verbindung fehlt.' }
    if ([string]::IsNullOrWhiteSpace($settings.remote) -or -not $settings.remote.StartsWith('/')) { throw 'Ein absoluter Zielordner mit / am Anfang ist erforderlich.' }
    $siteArg = Quote-WinSCP $settings.site
    $remoteArg = Quote-WinSCP ($settings.remote.TrimEnd('/') + '/')
    $sourceArg = Quote-WinSCP (Join-Path $repo '3 server\hasa-api\*')
    Write-Host 'Hole den aktuellen Stand von GitHub ...'
    & git.exe -C $repo pull --ff-only
    if ($LASTEXITCODE -ne 0) { throw 'Git Pull fehlgeschlagen. Es wurde nichts hochgeladen.' }
    if (-not (Test-Path (Join-Path $repo '3 server\hasa-api\galaxy.php'))) { throw 'HASA-Serverdateien fehlen.' }
    $tempScript = [IO.Path]::GetTempFileName()
    $commands = @(
        'option batch abort',
        'option confirm off',
        ('open ' + $siteArg),
        ('put -transfer=binary -filemask="*.php | config.php; config.example.php" ' + $sourceArg + ' ' + $remoteArg),
        'exit'
    )
    [IO.File]::WriteAllLines($tempScript, $commands, (New-Object Text.UTF8Encoding($true)))
    Write-Host 'Lade die PHP-Dateien in den HASA-Ordner hoch ...'
    & $winscp ('/script=' + $tempScript)
    if ($LASTEXITCODE -ne 0) { throw 'Upload fehlgeschlagen. Die Meldungen oben zeigen die Ursache; teilweise uebertragene Dateien sind moeglich.' }
    New-Item -ItemType Directory -Path (Split-Path $privateSettings -Parent) -Force | Out-Null
    $settings | ConvertTo-Json | Set-Content -LiteralPath $privateSettings -Encoding UTF8
    Write-Host 'Fertig: Pull und Serverupload erfolgreich. Die Galaxieansicht jetzt mit Strg+F5 neu laden.' -ForegroundColor Green
    Write-Host 'Die private config.php bleibt erhalten. Datenbankmigrationen werden von dieser Batch nicht ausgefuehrt.'
    exit 0
} catch {
    Write-Host ('FEHLER: ' + $_.Exception.Message) -ForegroundColor Red
    exit 1
} finally {
    if ($tempScript -and (Test-Path $tempScript)) { Remove-Item -LiteralPath $tempScript -Force }
}
