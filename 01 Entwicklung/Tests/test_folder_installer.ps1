$ErrorActionPreference = 'Stop'
$root = Split-Path (Split-Path $PSScriptRoot -Parent) -Parent
$installer = Join-Path $root '05 Verwaltung/Werkzeuge/ordner-einrichten.ps1'
$temp = Join-Path ([IO.Path]::GetTempPath()) ('hasa-folders-test-' + [guid]::NewGuid())
try {
 $payload = Join-Path $temp 'package/Hasa'
 $dest = Join-Path $temp 'existing/Hasa'
 New-Item -ItemType Directory -Path $payload,$dest -Force | Out-Null
 Set-Content -LiteralPath (Join-Path $payload 'HASA-START.cmd') -Value '@echo off'
 Set-Content -LiteralPath (Join-Path $dest 'original.txt') -Value 'old data'
 $files = @(Get-ChildItem -LiteralPath $payload -Recurse -File | ForEach-Object { @{path=$_.Name;sha256=(Get-FileHash -LiteralPath $_.FullName).Hash.ToLowerInvariant()} })
 $manifest = Join-Path $temp 'package/PAKET-SHA256.json'
 @{files=$files} | ConvertTo-Json -Depth 5 | Set-Content -LiteralPath $manifest
 & $installer -SourceDirectory $payload -Destination $dest -ManifestPath $manifest
 if (!(Test-Path (Join-Path $dest 'HASA-START.cmd'))) { throw 'New payload missing' }
 $backup = @(Get-ChildItem (Split-Path $dest -Parent) -Directory | Where-Object { $_.Name -like 'Hasa-vor-umstellung-*' })
 if ($backup.Count -ne 1 -or !(Test-Path (Join-Path $backup[0].FullName 'original.txt'))) { throw 'Original backup missing' }
 Add-Content -LiteralPath (Join-Path $payload 'HASA-START.cmd') -Value 'changed'
 $blocked=$false
 try { & $installer -SourceDirectory $payload -Destination $dest -ManifestPath $manifest } catch { $blocked=$true }
 if (!$blocked -or !(Test-Path (Join-Path $dest 'HASA-START.cmd'))) { throw 'Corrupt payload changed destination' }
 $blocked=$false
 try { & $installer -SourceDirectory $dest -Destination $dest -ManifestPath $manifest } catch { $blocked=$true }
 if (!$blocked) { throw 'In-place source accepted' }
 Write-Host 'PASS folder installation, original backup, corrupted-package protection and in-place source rejection'
} finally { if (Test-Path $temp) { Remove-Item -LiteralPath $temp -Recurse -Force } }
