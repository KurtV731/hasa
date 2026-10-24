param(
 [Parameter(Mandatory=$true)][string]$SourceDirectory,
 [string]$Destination = 'C:\ppd\hasa',
 [string]$ManifestPath
)
$ErrorActionPreference = 'Stop'
$source = [IO.Path]::GetFullPath($SourceDirectory).TrimEnd('\','/')
$destinationFull = [IO.Path]::GetFullPath($Destination).TrimEnd('\','/')
if ($source.Equals($destinationFull,[StringComparison]::OrdinalIgnoreCase) -or $source.StartsWith($destinationFull + [IO.Path]::DirectorySeparatorChar,[StringComparison]::OrdinalIgnoreCase)) {
 throw 'Bitte das ZIP ausserhalb von C:\ppd\hasa entpacken und die BAT dort starten.'
}
if ($destinationFull -eq [IO.Path]::GetPathRoot($destinationFull).TrimEnd('\','/')) { throw 'Ein Laufwerksstamm ist kein gueltiger Projektordner.' }
if (!$ManifestPath) { $ManifestPath = Join-Path (Split-Path $source -Parent) 'PAKET-SHA256.json' }
$manifest = Get-Content -LiteralPath $ManifestPath -Raw | ConvertFrom-Json
function Check-Payload([string]$base) {
 foreach ($entry in $manifest.files) {
  if ([IO.Path]::IsPathRooted($entry.path) -or $entry.path -match '(^|[\\/])\.\.([\\/]|$)') { throw 'Ungueltiger Paketpfad.' }
  $file = Join-Path $base $entry.path
  if (!(Test-Path -LiteralPath $file -PathType Leaf)) { throw ('Paketdatei fehlt: ' + $entry.path) }
  if ((Get-FileHash -LiteralPath $file -Algorithm SHA256).Hash.ToLowerInvariant() -ne $entry.sha256) { throw ('Paketdatei veraendert: ' + $entry.path) }
 }
}
if (!(Test-Path -LiteralPath (Join-Path $source 'HASA-START.cmd'))) { throw 'Der neue HASA-Ordner fehlt.' }
Check-Payload $source
# A changed local configuration must not be overwritten by an older ZIP.
foreach ($relative in @('02 Website/api/config.php','3 server/hasa-api/config.php')) {
 $existing = Join-Path $destinationFull $relative
 if (Test-Path -LiteralPath $existing) {
  $incoming = Join-Path $source '02 Website/api/config.php'
  if (!(Test-Path -LiteralPath $incoming) -or (Get-FileHash -LiteralPath $existing).Hash -ne (Get-FileHash -LiteralPath $incoming).Hash) {
   throw 'Die lokale Serverkonfiguration wurde seit dem ZIP veraendert. Der bisherige Ordner bleibt unveraendert; bitte die neue Konfiguration zuerst abgleichen.'
  }
 }
}
$backup = $null
$oldMoved = $false
$copyStarted = $false
$stamp = Get-Date -Format 'yyyy-MM-dd_HH-mm-ss-fff'
try {
 New-Item -ItemType Directory -Path (Split-Path $destinationFull -Parent) -Force | Out-Null
 if (Test-Path -LiteralPath $destinationFull) {
  $backup = $destinationFull + '-vor-umstellung-' + $stamp
  Move-Item -LiteralPath $destinationFull -Destination $backup
  $oldMoved = $true
 }
 $copyStarted = $true
 Copy-Item -LiteralPath $source -Destination $destinationFull -Recurse -Force
 Check-Payload $destinationFull
 Write-Host ('Fertig: ' + $destinationFull) -ForegroundColor Green
 if ($backup) { Write-Host ('Bisheriger Ordner gesichert: ' + $backup) }
 Write-Host 'Ab jetzt HASA-START.cmd im neuen Ordner verwenden. Es wurde nichts hochgeladen.'
} catch {
 $failure = $_
 if ($copyStarted -and (Test-Path -LiteralPath $destinationFull)) {
  Move-Item -LiteralPath $destinationFull -Destination ($destinationFull + '-umstellung-fehlgeschlagen-' + $stamp)
 }
 if ($oldMoved -and $backup -and (Test-Path -LiteralPath $backup)) { Move-Item -LiteralPath $backup -Destination $destinationFull }
 throw $failure
}
