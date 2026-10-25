# HASA useradmin2: uploads the verified files from this test package, never git pull.
$ErrorActionPreference='Stop'
$tempScript=$null
function Quote-WinSCP([string]$value){
 if($value -match '[\r\n]'){throw 'Zeilenumbrueche sind im Verbindungspfad nicht erlaubt.'}
 return '"'+$value.Replace('"','""')+'"'
}
try {
 $privateSettings=Join-Path (Join-Path $env:LOCALAPPDATA 'HASA') 'server-update-hasa-li.json'
 if(Test-Path -LiteralPath $privateSettings){$settings=Get-Content -LiteralPath $privateSettings -Raw | ConvertFrom-Json}
 else {
  $settings=[pscustomobject]@{site='HASA-li';remote='/hasa.li/api/'}
 }
 if([string]::IsNullOrWhiteSpace($settings.site) -or [string]::IsNullOrWhiteSpace($settings.remote) -or -not $settings.remote.StartsWith('/')){throw 'Verbindung oder absoluter API-Zielordner fehlt.'}
 $winscp=@(${env:ProgramFiles(x86)},$env:ProgramFiles) | Where-Object {$_} | ForEach-Object {Join-Path $_ 'WinSCP\WinSCP.com'} | Where-Object {Test-Path $_} | Select-Object -First 1
 if(-not $winscp){$found=Get-Command WinSCP.com -ErrorAction SilentlyContinue;if($found){$winscp=$found.Source}}
 if(-not $winscp){throw 'WinSCP.com wurde nicht gefunden.'}
 $manifest=Get-Content -LiteralPath (Join-Path $PSScriptRoot 'server-admin\manifest.json') -Raw | ConvertFrom-Json
 $backup=Join-Path 'C:\ppd\hasa\06 Sicherungen' ('Server-vor-useradmin2-'+(Get-Date -Format 'yyyy-MM-dd_HH-mm-ss-fff'))
 New-Item -ItemType Directory -Path $backup -Force | Out-Null
 $backupCommands=@('option batch abort','option confirm off',('open '+(Quote-WinSCP $settings.site)))
 $commands=@('option batch abort','option confirm off',('open '+(Quote-WinSCP $settings.site)))
 foreach($item in $manifest.files){
  if($item.name -notmatch '^[a-z0-9-]+\.php$' -or $item.name -like 'config*'){throw 'Ungueltige Paketdatei.'}
  $local=Join-Path (Join-Path $PSScriptRoot 'server-admin') $item.name
  if((Get-FileHash -LiteralPath $local -Algorithm SHA256).Hash.ToLowerInvariant() -ne $item.sha256){throw ('Pruefsumme stimmt nicht: '+$item.name)}
  $backupCommands+=('get -transfer=binary '+(Quote-WinSCP ($settings.remote.TrimEnd('/')+'/'+$item.name))+' '+(Quote-WinSCP (Join-Path $backup $item.name)))
  $commands+=('put -transfer=binary '+(Quote-WinSCP $local)+' '+(Quote-WinSCP ($settings.remote.TrimEnd('/')+'/'+$item.name)))
 }
 $backupCommands+='exit'
 $tempScript=[IO.Path]::GetTempFileName();[IO.File]::WriteAllLines($tempScript,$backupCommands,(New-Object Text.UTF8Encoding($true)))
 & $winscp ('/script='+$tempScript)
 if($LASTEXITCODE -ne 0){throw 'Serversicherung fehlgeschlagen. Keine Testdateien hochgeladen.'}
 Write-Host ('Bisherige Serverdateien gesichert: '+$backup)
 $commands+='exit'
 Write-Host ('HASA Benutzerverwaltung | Verbindung: '+$settings.site+' | API-Ziel: '+$settings.remote) -ForegroundColor Yellow
 Write-Host 'Uebertrage die drei geprueften PHP-Dateien aus diesem Paket ...'
 [IO.File]::WriteAllLines($tempScript,$commands,(New-Object Text.UTF8Encoding($true)))
 & $winscp ('/script='+$tempScript)
 if($LASTEXITCODE -ne 0){throw 'Upload fehlgeschlagen. Teilweise uebertragene Dateien sind moeglich; bitte die Fehlermeldung sichern.'}
 Write-Host 'Fertig: Benutzerverwaltung aktualisiert. Die Webseite neu laden. Kein Userscript-Update und kein SQL-Import erforderlich.' -ForegroundColor Green
 exit 0
} catch {Write-Host ('FEHLER: '+$_.Exception.Message) -ForegroundColor Red;exit 1}
finally {if($tempScript -and (Test-Path -LiteralPath $tempScript)){Remove-Item -LiteralPath $tempScript -Force}}
