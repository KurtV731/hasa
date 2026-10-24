param(
 [Parameter(Mandatory=$true)][string]$PackageDirectory,
 [string]$ProjectRoot = 'C:\ppd\hasa',
 [switch]$VerifyPackageOnly
)
$ErrorActionPreference = 'Stop'
$PackageDirectory = [IO.Path]::GetFullPath($PackageDirectory)
$manifest = Get-Content -LiteralPath (Join-Path $PackageDirectory 'manifest.json') -Raw -Encoding UTF8 | ConvertFrom-Json
$names = @('hasa-user.js.txt','hasa.zip','hasa_1.1.2_ui-korrektur.user.js.txt','hasa_1.1_benutzeranleitung.pdf','hasa_1.1_forschungsmodul-komplett.user.js.txt','hasa_1.1_forschungsmodul.zip','hasa_1_0_public_preview.user.js.txt','HASA_Alpha20_geschlossene-Testphase.user.js.txt')
if (@($manifest.old_files).Count -ne 8 -or @(Compare-Object ($names | Sort-Object) ($manifest.old_files.name | Sort-Object)).Count) { throw 'Die Liste der acht Altdateien stimmt nicht.' }
$publicNames = @('HASA-1.2.0.user.js','index.html','SHA256SUMS.txt')
if (@($manifest.public_files).Count -ne 3 -or @(Compare-Object ($publicNames | Sort-Object) ($manifest.public_files.name | Sort-Object)).Count) { throw 'Die drei Download-Dateien fehlen.' }
function Hash-Normalized([string]$path,[string]$name) {
 if ($name.EndsWith('.txt') -or $name.EndsWith('.js') -or $name.EndsWith('.html')) {
  $text = [Text.Encoding]::UTF8.GetString([IO.File]::ReadAllBytes($path)).Replace("`r`n","`n")
  $sha = [Security.Cryptography.SHA256]::Create()
  try { return ([BitConverter]::ToString($sha.ComputeHash([Text.Encoding]::UTF8.GetBytes($text)))).Replace('-','').ToLowerInvariant() } finally { $sha.Dispose() }
 }
 return (Get-FileHash -LiteralPath $path -Algorithm SHA256).Hash.ToLowerInvariant()
}
function Quote-WinSCP([string]$value) {
 if ($value -match '[\r\n]') { throw 'Ungueltiger Dateipfad.' }
 return '"' + $value.Replace('"','""') + '"'
}
foreach ($item in $manifest.old_files) {
 $file = Join-Path (Join-Path $PackageDirectory 'Originale') $item.name
 if ((Get-FileHash -LiteralPath $file -Algorithm SHA256).Hash.ToLowerInvariant() -ne $item.sha256) { throw ('Paketdatei veraendert: ' + $item.name) }
}
foreach ($item in $manifest.public_files) {
 $file = Join-Path (Join-Path $PackageDirectory 'download') $item.name
 if ((Get-FileHash -LiteralPath $file -Algorithm SHA256).Hash.ToLowerInvariant() -ne $item.sha256) { throw ('Download-Datei veraendert: ' + $item.name) }
}
if ($VerifyPackageOnly) { Write-Host 'Paketpruefung bestanden. Keine lokale oder externe Aenderung.'; return }
if (!(Test-Path (Join-Path $ProjectRoot 'HASA-START.cmd'))) { throw 'Der neue HASA-Projektordner fehlt.' }
if (!(Get-Command git.exe -ErrorAction SilentlyContinue)) { throw 'Git wurde nicht gefunden.' }
Write-Host 'Hole den aktuellen lokalen Programmstand ...'
& git.exe -C $ProjectRoot pull --ff-only
if ($LASTEXITCODE -ne 0) { throw 'Git-Aktualisierung fehlgeschlagen. Keine Serverdatei wurde veraendert.' }
$stable = Join-Path $ProjectRoot '04 Veroeffentlichung/Stabil/HASA.user.js'
$scriptHash = ($manifest.public_files | Where-Object { $_.name -eq 'HASA-1.2.0.user.js' }).sha256
if ((Hash-Normalized $stable 'HASA.user.js') -ne $scriptHash) { throw 'Inzwischen liegt eine andere Programmversion vor. Diese wird nicht durch das Aufraeumpaket ersetzt.' }
foreach ($item in $manifest.public_files) {
 $localPublic = Join-Path (Join-Path $ProjectRoot '02 Website/download') $item.name
 if ((Hash-Normalized $localPublic $item.name) -ne $item.sha256) { throw ('Der aktuelle GitHub-Download hat sich geaendert: ' + $item.name + '. Keine Serverdatei veraendert.') }
}
$winscp = @(${env:ProgramFiles(x86)},$env:ProgramFiles) | Where-Object { $_ } | ForEach-Object { Join-Path $_ 'WinSCP/WinSCP.com' } | Where-Object { Test-Path $_ } | Select-Object -First 1
if (!$winscp) { $found = Get-Command WinSCP.com -ErrorAction SilentlyContinue; if ($found) { $winscp = $found.Source } }
if (!$winscp) { throw 'WinSCP.com wurde nicht gefunden.' }
function Invoke-WinSCP([string[]]$commands) {
 $temp = [IO.Path]::GetTempFileName()
 try {
  @('option batch abort','option confirm off','open "HASA-li"') + $commands + @('exit') | Set-Content -LiteralPath $temp -Encoding UTF8
  & $winscp ('/script=' + $temp)
  if ($LASTEXITCODE -ne 0) { throw 'WinSCP meldet einen Fehler. Bitte die Ausgabe aufbewahren; die lokale Sicherung bleibt erhalten.' }
 } finally { Remove-Item -LiteralPath $temp -Force -ErrorAction SilentlyContinue }
}
$backup = Join-Path $ProjectRoot ('07 Archiv/Alte Downloads/' + (Get-Date -Format 'yyyy-MM-dd_HH-mm-ss-fff'))
$originals = Join-Path $backup 'Aus-dem-ZIP'
$serverBackup = Join-Path $backup 'Vom-Server'
New-Item -ItemType Directory -Path $originals,$serverBackup -Force | Out-Null
foreach ($item in $manifest.old_files) { Copy-Item -LiteralPath (Join-Path (Join-Path $PackageDirectory 'Originale') $item.name) -Destination (Join-Path $originals $item.name) }
Write-Host ('Sichere zuerst alle Altdateien nach ' + $backup)
$commands = @()
foreach ($item in $manifest.old_files) { $commands += 'get -transfer=binary ' + (Quote-WinSCP ('/hasa.li/download/' + $item.name)) + ' ' + (Quote-WinSCP (Join-Path $serverBackup $item.name)) }
Invoke-WinSCP $commands
foreach ($item in $manifest.old_files) {
 if ((Hash-Normalized (Join-Path $serverBackup $item.name) $item.name) -ne $item.normalized_sha256) { throw ('Serverdatei weicht vom geprueften Bestand ab: ' + $item.name + '. Keine Serverdatei entfernt.') }
}
$commands = @()
foreach ($item in $manifest.public_files) { $commands += 'put -transfer=binary ' + (Quote-WinSCP (Join-Path (Join-Path $PackageDirectory 'download') $item.name)) + ' ' + (Quote-WinSCP ('/hasa.li/download/' + $item.name)) }
Invoke-WinSCP $commands
[Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12
foreach ($item in $manifest.public_files) {
 $check = Join-Path $backup ('pruefung-' + $item.name)
 Invoke-WebRequest -UseBasicParsing -Uri ('https://hasa.li/download/' + $item.name + '?hasa_check=' + [guid]::NewGuid().ToString('N')) -OutFile $check
 if ((Get-FileHash -LiteralPath $check -Algorithm SHA256).Hash.ToLowerInvariant() -ne $item.sha256) { throw ('Veroeffentlichung konnte nicht geprueft werden: ' + $item.name + '. Altdateien bleiben auf dem Server.') }
}
Write-Host 'Sicherung und aktuelle Downloads sind geprueft. Entferne genau die acht alten Dateien ...'
$commands = @()
foreach ($name in $names) { $commands += 'rm ' + (Quote-WinSCP ('/hasa.li/download/' + $name)) }
Invoke-WinSCP $commands
foreach ($item in $manifest.old_files) {
 $local = Join-Path (Join-Path $ProjectRoot '02 Website/download') $item.name
 if (Test-Path -LiteralPath $local) {
  $localBackup = Join-Path $backup 'Lokale-Altdateien'
  New-Item -ItemType Directory -Path $localBackup -Force | Out-Null
  Move-Item -LiteralPath $local -Destination (Join-Path $localBackup $item.name)
 }
}
Write-Host 'Fertig: Die acht bekannten Altdateien sind gesichert und vom Server entfernt.' -ForegroundColor Green
Write-Host ('Archiv: ' + $backup)
Write-Host 'Aktuelle Downloads: https://hasa.li/download/'
