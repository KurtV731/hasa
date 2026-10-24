$ErrorActionPreference = 'Stop'
$winscp = Join-Path ${env:ProgramFiles(x86)} 'WinSCP/WinSCP.com'
if (!(Test-Path $winscp)) { $winscp = Join-Path $env:ProgramFiles 'WinSCP/WinSCP.com' }
if (!(Test-Path $winscp)) { throw 'WinSCP.com wurde nicht gefunden.' }
$download = Join-Path $PSScriptRoot 'download'
$index = Join-Path $PSScriptRoot 'index.html'
if (!(Test-Path $download)) { throw 'Lokaler Downloadordner wurde nicht gefunden.' }
if (!(Test-Path $index)) { throw 'Die HASA-Startseite index.html wurde nicht gefunden.' }
$script = Join-Path $env:TEMP ('hasa-publish-' + [guid]::NewGuid() + '.txt')
try {
 @('option batch abort','option confirm off','open "HASA-li"',('put "' + $index + '" /hasa.li/index.html'),('put "' + $download + '" /hasa.li/'),'exit') | Set-Content -Encoding UTF8 $script
 & $winscp /script=$script
 if ($LASTEXITCODE -ne 0) { throw 'Upload fehlgeschlagen. Bitte die WinSCP-Meldung prüfen.' }
 Write-Host 'Upload abgeschlossen: https://hasa.li/ und https://hasa.li/download/'
} finally { Remove-Item $script -ErrorAction SilentlyContinue }
