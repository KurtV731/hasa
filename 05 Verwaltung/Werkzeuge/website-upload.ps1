$ErrorActionPreference = 'Stop'
$winscp = Join-Path ${env:ProgramFiles(x86)} 'WinSCP/WinSCP.com'
if (!(Test-Path $winscp)) { $winscp = Join-Path $env:ProgramFiles 'WinSCP/WinSCP.com' }
if (!(Test-Path $winscp)) { throw 'WinSCP.com wurde nicht gefunden.' }
$root = Split-Path (Split-Path $PSScriptRoot -Parent) -Parent
$website = Join-Path $root '02 Website'
$download = Join-Path $website 'download'
$assets = Join-Path $website 'assets'
$anleitung = Join-Path $website 'anleitung'
$hilfe = Join-Path $website 'hilfe'
$aktualisieren = Join-Path $website 'aktualisieren'
$index = Join-Path $website 'index.html'
if (!(Test-Path $download)) { throw 'Lokaler Downloadordner wurde nicht gefunden.' }
if (!(Test-Path $assets)) { throw 'Lokaler Website-Assetordner wurde nicht gefunden.' }
if (!(Test-Path (Join-Path $anleitung 'index.html'))) { throw 'Die Website-Anleitung wurde nicht gefunden.' }
if (!(Test-Path (Join-Path $hilfe 'index.html'))) { throw 'Die Website-Hilfe wurde nicht gefunden.' }
if (!(Test-Path (Join-Path $aktualisieren 'index.html'))) { throw 'Die Website-Updatehilfe wurde nicht gefunden.' }
if (!(Test-Path $index)) { throw 'Die HASA-Startseite index.html wurde nicht gefunden.' }
$script = Join-Path $env:TEMP ('hasa-publish-' + [guid]::NewGuid() + '.txt')
try {
 @('option batch abort','option confirm off','open "HASA-li"',('put "' + $index + '" /hasa.li/index.html'),('put "' + $assets + '" /hasa.li/'),('put "' + $download + '" /hasa.li/'),('put "' + $anleitung + '" /hasa.li/'),('put "' + $hilfe + '" /hasa.li/'),('put "' + $aktualisieren + '" /hasa.li/'),'exit') | Set-Content -Encoding UTF8 $script
 & $winscp /script=$script
 if ($LASTEXITCODE -ne 0) { throw 'Upload fehlgeschlagen. Bitte die WinSCP-Meldung prüfen.' }
 Write-Host 'Upload abgeschlossen: Startseite, Download, Assets, Anleitung, Hilfe und Updatehilfe.'
} finally { Remove-Item $script -ErrorAction SilentlyContinue }
