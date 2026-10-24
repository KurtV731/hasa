$ErrorActionPreference = 'Stop'
$root = Split-Path (Split-Path $PSScriptRoot -Parent) -Parent
$script = Join-Path $root '05 Verwaltung/Werkzeuge/downloads-bereinigen.ps1'
$temp = Join-Path ([IO.Path]::GetTempPath()) ('hasa-download-check-' + [guid]::NewGuid())
try {
 New-Item -ItemType Directory -Path (Join-Path $temp 'Originale'),(Join-Path $temp 'download') -Force | Out-Null
 $oldNames = @('hasa-user.js.txt','hasa.zip','hasa_1.1.2_ui-korrektur.user.js.txt','hasa_1.1_benutzeranleitung.pdf','hasa_1.1_forschungsmodul-komplett.user.js.txt','hasa_1.1_forschungsmodul.zip','hasa_1_0_public_preview.user.js.txt','HASA_Alpha20_geschlossene-Testphase.user.js.txt')
 $publicNames = @('HASA-1.2.0.user.js','index.html','SHA256SUMS.txt')
 $old = @(); $public = @()
 foreach ($name in $oldNames) {
  $p = Join-Path (Join-Path $temp 'Originale') $name
  [IO.File]::WriteAllText($p,'fixture', (New-Object Text.UTF8Encoding($false)))
  $hash = (Get-FileHash -LiteralPath $p -Algorithm SHA256).Hash.ToLowerInvariant()
  $old += @{name=$name;sha256=$hash;normalized_sha256=$hash}
 }
 foreach ($name in $publicNames) {
  $p = Join-Path (Join-Path $temp 'download') $name
  [IO.File]::WriteAllText($p,'fixture', (New-Object Text.UTF8Encoding($false)))
  $public += @{name=$name;sha256=(Get-FileHash -LiteralPath $p -Algorithm SHA256).Hash.ToLowerInvariant()}
 }
 @{old_files=$old;public_files=$public} | ConvertTo-Json -Depth 5 | Set-Content -LiteralPath (Join-Path $temp 'manifest.json') -Encoding UTF8
 & $script -PackageDirectory $temp -ProjectRoot (Join-Path $temp 'nonexistent-project') -VerifyPackageOnly
 Add-Content -LiteralPath (Join-Path (Join-Path $temp 'Originale') 'hasa.zip') -Value 'corrupt'
 $blocked=$false
 try { & $script -PackageDirectory $temp -VerifyPackageOnly } catch { $blocked=$true }
 if (!$blocked) { throw 'Corrupt original accepted' }
 Write-Host 'PASS verified package, corrupt-original rejection and no project/network dependency in verification mode'
} finally { if (Test-Path $temp) { Remove-Item -LiteralPath $temp -Recurse -Force } }
