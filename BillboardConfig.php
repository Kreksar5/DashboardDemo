<?php
$testServersOn = false;//for debugging only. DO NOT CHANGE enables ALL test servers (with calls to local dummy nagios outputs)
$testServerOn = false;//for debugging only. DO NOT CHANGE. enables a SINGLE test server (with fake calls to nagios)
$testServerStatus = 8;//for debugging only. DO NOT CHANGE. sets the status of the dummy test server. (1=pemnding, 2=up, 4=down, 8=critical, 16=warning)
$testGenAlertOn = false;//for debugging only. DO NOT CHANGE. switches on the dummy gen power status returns to test their alert sounds
$testGenAlertStatus =  "UPS";// */ "Gen"; //for debugging only. DO NOT CHANGE. sets the status of the dummy gen power return 
date_default_timezone_set("America/Denver");
$verNumber = "2.1.6";
$configArray = parse_ini_file("BillboardConfig.ini", true);
//internal server lists
$internalADSArray = $configArray['internal']['internalADSArray'];
$internalDBSArray = $configArray['internal']['internalDBSArray'];
$internalOtherServerArray = $configArray['internal']['internalOtherServerArray'];
//external server list
$externalServerArray = $configArray['external']['externalServerArray'];
if($testServerOn == true){array_push($externalServerArray, "debug");}
//network servers list
$networkMonitoringArray = $configArray['network']['networkMonitoringArray'];

//calculate server counts
$internalADSCount = count($internalADSArray);
$internalDBSCount = count($internalDBSArray);
$internalOtherServerCount = count($internalOtherServerArray);
$internalServerCount = ($internalADSCount + $internalDBSCount + $internalOtherServerCount);
$externalServerCount = count($externalServerArray);
$totalServerCount = $internalServerCount + $externalServerCount;//is this still needed?
$networkMonitoringServerCount = count($networkMonitoringArray);

//sound file locations
$soundClip1 = $configArray['sound_file_locations']['soundClip1'];
$soundClip2 = $configArray['sound_file_locations']['soundClip2'];
$soundClip3 = $configArray['sound_file_locations']['soundClip3'];//Generator Power Warning
$soundClip4 = $configArray['sound_file_locations']['soundClip4'];//UPS power constant loud alert
$soundClip5 = $configArray['sound_file_locations']['soundClip5'];//Excessive Temperature Warning
$soundClip6 = $configArray['sound_file_locations']['soundClip6'];//Calendar Event Reminder Sound

//log file name setting
$logFileName = $configArray['log_file_settings']['logFileName'];
//log file for storing the server status vaules of the previous iteration of BillboardMaster.php
$PrevStatLogFileName = $configArray['log_file_settings']['PrevStatLogFileName'];

//configurable urls for the remote monitoring systems
$RoomAlertAddress = $configArray['urls']['RoomAlertAddress'];
$InternalNagiosAddress = $configArray['urls']['InternalNagiosAddress'];
$ExternalNagiosAddress = $configArray['urls']['ExternalNagiosAddress'];

//server types
$windowsServers = $configArray['server_types']['windowsServers'];
$drupalServers = $configArray['server_types']['drupalServers'];

//temperature threshold triggers
$critTempTrigger = $configArray['temp_threshold_triggers']['critTempTrigger'];
$medTempTrigger = $configArray['temp_threshold_triggers']['medTempTrigger'];


//Calendar Alert list
$calAlert = $configArray['calendar_alerts']['calAlerts'];
?>