$ErrorActionPreference = 'Stop'
$winscp = Join-Path ${env:ProgramFiles(x86)} 'WinSCP/WinSCP.com'
if (!(Test-Path $winscp)) { $winscp = Join-Path $env:ProgramFiles 'WinSCP/WinSCP.com' }
if (!(Test-Path $winscp)) { throw 'WinSCP.com wurde nicht gefunden.' }
$local = Join-Path $PSScriptRoot 'website/download'
$script = Join-Path $env:TEMP ('hasa-publish-' + [guid]::NewGuid() + '.txt')
try {
 @('option batch abort','option confirm off','open "HASA-li"',('put "' + $local + '" /hasa.li/'),'exit') | Set-Content -Encoding UTF8 $script
 & $winscp /script=$script
 if ($LASTEXITCODE -ne 0) { throw 'Upload fehlgeschlagen. Bitte die WinSCP-Meldung prüfen.' }
 Write-Host 'Upload abgeschlossen: https://hasa.li/download/'
} finally { Remove-Item $script -ErrorAction SilentlyContinue }
