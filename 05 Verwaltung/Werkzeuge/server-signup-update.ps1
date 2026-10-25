# Upload only this verified signup package. Never git pull or overwrite config.php.
$ErrorActionPreference='Stop'
$tempScript=$null
function Quote-WinSCP([string]$value){
 if($value -match '[\r\n]'){throw 'Zeilenumbrueche im Verbindungsnamen oder Pfad sind nicht erlaubt.'}
 return '"'+$value.Replace('"','""')+'"'
}
try {
 $privateSettings=Join-Path (Join-Path $env:LOCALAPPDATA 'HASA') 'server-update-hasa-li.json'
 if(Test-Path -LiteralPath $privateSettings){$settings=Get-Content -LiteralPath $privateSettings -Raw | ConvertFrom-Json}
 else {$settings=[pscustomobject]@{site='HASA-li';remote='/hasa.li/api/'}}
 if([string]::IsNullOrWhiteSpace($settings.site) -or [string]::IsNullOrWhiteSpace($settings.remote) -or $settings.remote -notmatch '^/.+/api/?$'){throw 'Das gespeicherte hasa.li-Profil benoetigt den API-Pfad /hasa.li/api/.'}
 $apiRemote=$settings.remote.TrimEnd('/');$websiteRemote=$apiRemote.Substring(0,$apiRemote.Length-4)
 $winscp=@(${env:ProgramFiles(x86)},$env:ProgramFiles) | Where-Object {$_} | ForEach-Object {Join-Path $_ 'WinSCP\WinSCP.com'} | Where-Object {Test-Path $_} | Select-Object -First 1
 if(-not $winscp){$found=Get-Command WinSCP.com -ErrorAction SilentlyContinue;if($found){$winscp=$found.Source}}
 if(-not $winscp){throw 'WinSCP.com wurde nicht gefunden.'}
 $manifest=Get-Content -LiteralPath (Join-Path $PSScriptRoot 'payload\manifest.json') -Raw | ConvertFrom-Json
 $commands=@('option batch abort','option confirm off',('open '+(Quote-WinSCP $settings.site)))
 foreach($item in $manifest.files){
  if($item.path -notmatch '^(api/[a-z0-9-]+\.php|index\.html)$' -or $item.path -match 'config'){throw 'Ungueltige Paketdatei.'}
  $local=Join-Path (Join-Path $PSScriptRoot 'payload') ($item.path.Replace('/',[IO.Path]::DirectorySeparatorChar))
  if((Get-FileHash -LiteralPath $local -Algorithm SHA256).Hash.ToLowerInvariant() -ne $item.sha256){throw ('Pruefsumme stimmt nicht: '+$item.path)}
  $commands+=('put -transfer=binary '+(Quote-WinSCP $local)+' '+(Quote-WinSCP ($websiteRemote+'/'+$item.path)))
 }
 $backup=Join-Path 'C:\ppd\hasa\06 Sicherungen' ('Vor-Selbstregistrierung-'+(Get-Date -Format 'yyyy-MM-dd_HH-mm-ss-fff'))
 $apiBackup=Join-Path $backup 'api';New-Item -ItemType Directory -Path $apiBackup -Force | Out-Null
 $backupCommands=@('option batch abort','option confirm off',('open '+(Quote-WinSCP $settings.site)),
  ('get -transfer=binary -filemask="*.php | config.php; config.example.php" '+(Quote-WinSCP ($apiRemote+'/*'))+' '+(Quote-WinSCP ($apiBackup+'\'))),
  ('get -transfer=binary '+(Quote-WinSCP ($websiteRemote+'/index.html'))+' '+(Quote-WinSCP (Join-Path $backup 'index.html'))),'exit')
 $tempScript=[IO.Path]::GetTempFileName();[IO.File]::WriteAllLines($tempScript,$backupCommands,(New-Object Text.UTF8Encoding($true)))
 & $winscp ('/script='+$tempScript)
 if($LASTEXITCODE -ne 0){throw 'Serversicherung fehlgeschlagen. Kein Upload ausgefuehrt.'}
 foreach($required in @('auth.php','login.php','user-admin-service.php','user-admin.php')){if(-not(Test-Path -LiteralPath (Join-Path $apiBackup $required))){throw ('Serversicherung unvollstaendig: '+$required)}}
 $commands+='exit';[IO.File]::WriteAllLines($tempScript,$commands,(New-Object Text.UTF8Encoding($true)))
 Write-Host ('Sicherung: '+$backup) -ForegroundColor Yellow
 & $winscp ('/script='+$tempScript)
 if($LASTEXITCODE -ne 0){throw 'Upload unvollstaendig. Bitte die Meldungen sichern; die vorherigen Dateien liegen in der Sicherung.'}
 Write-Host 'Fertig: Jetzt https://hasa.li/api/user-admin.php oeffnen und Oeffentliche Registrierung einrichten waehlen.' -ForegroundColor Green
 Write-Host 'Danach mit einem neuen Testnamen und deiner E-Mail-Adresse die erste Registrierung und Zustellung pruefen. Das Userscript bleibt unveraendert.'
 exit 0
} catch {Write-Host ('FEHLER: '+$_.Exception.Message) -ForegroundColor Red;exit 1}
finally {if($tempScript -and (Test-Path -LiteralPath $tempScript)){Remove-Item -LiteralPath $tempScript -Force}}
