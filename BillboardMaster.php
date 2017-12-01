<?php
//require_once("BillboardConfig.php");
//require("BillboardConfig.php");
if($_SERVER["REQUEST_METHOD"] == "POST")
  {
  //initialize final array
  $finalOutputArr = Array();
  //gather the data from servers and push it onto the final output array
  $serverStatus = UpdateServers();
  array_push($finalOutputArr, $serverStatus);

  //gather the data for the graphs and push it onto the final output array
  $graphData = UpdateGraphs();
  array_push($finalOutputArr, $graphData);
  
  //gather the data from generator monitor and push it onto the final output array
  $genPwrStatus = checkGenPwr();
  array_push($finalOutputArr, $genPwrStatus);
  
  //gather the data from temperature monitor and push it onto the final output array
  $DCTempStatus = checkDCTemps();
  array_push($finalOutputArr, $DCTempStatus);
// */  
  //format the final array as a JSON string and return it
  echo json_encode($finalOutputArr, JSON_PRETTY_PRINT);
  }

function UpdateServers()
  {
  require("BillboardConfig.php");
  require("Credentials.php");
  $ServerStatusOutputArr = Array();
  
  //this array is saved to a file and used to evaluate changes in status over time
  $ServerCurStatArr = Array();
  if(file_exists("$PrevStatLogFileName"))
    {
    $ServerPrevStatusArr = json_decode(file_get_contents("$PrevStatLogFileName"), true);
    }
   else
    {
    $ServerPrevStatusArr = array_fill(0,$totalServerCount, false);
    }
  if($testServersOn != true)//this is only == if I am testing with live data
    {
    //query the servers
    for($a = 0; $a < $totalServerCount; $a++)
      {
      if($a < $internalADSCount)
        {
        $hostname = $internalADSArray[$a];  
        $url = "http://$username:$password1@$InternalNagiosAddress/nagios/cgi-bin/statusjson.cgi?query=host&hostname=".$hostname;        
        }
      if($a >= $internalADSCount && $a < ($internalADSCount+$internalDBSCount))
        {
        $hostname = $internalDBSArray[$a-$internalADSCount];
        $url = "http://$username:$password1@$InternalNagiosAddress/nagios/cgi-bin/statusjson.cgi?query=host&hostname=".$hostname;
        }
      if($a >= ($internalADSCount+$internalDBSCount) && $a < $internalServerCount)
        {
        $hostname = $internalOtherServerArray[$a-($internalADSCount+$internalDBSCount)];
        $url = "http://$username:$password1@$InternalNagiosAddress/nagios/cgi-bin/statusjson.cgi?query=host&hostname=".$hostname;
        }
      if($a >= $internalServerCount && $a < $totalServerCount)
        {
        $hostname = $externalServerArray[$a-$internalServerCount];
        $url = "http://$username:$password2@$ExternalNagiosAddress/nagios/cgi-bin/statusjson.cgi?query=host&hostname=".$hostname;
        }
      $ServerStatusReply = json_decode(file_get_contents($url), true);
      array_push($ServerStatusOutputArr, evaluateServer($hostname, $ServerStatusReply, $a, $ServerPrevStatusArr[$a]));
      array_push($ServerCurStatArr, array($hostname, end($ServerStatusOutputArr[$a])));
      }
    }
  //debugging only
  if($testServersOn == true)//this is only != if I am testing with live date
    {
    //check on the test servers
    for($a = 0; $a < $totalServerCount; $a++)
      {
      $hostname = ("ServerFeed".$a);
      $url = "DemoJSONfeeds/".$hostname.".json";
      $hostnameDebug = "SF".$a;
      $testServerReply = json_decode(file_get_contents($url), true);
      array_push($ServerStatusOutputArr, evaluateServer($hostnameDebug, $testServerReply, $a, $ServerPrevStatusArr[$a]));
      array_push($ServerCurStatArr, array($hostnameDebug, end($ServerStatusOutputArr[$a])));
      }
    }
  file_put_contents("$PrevStatLogFileName", json_encode($ServerCurStatArr, JSON_PRETTY_PRINT));
  return $ServerStatusOutputArr;
  }
    
function evaluateServer($hostname, $serverObj, $j, $PrevStatArr)
  {
  require("BillboardConfig.php");
  //initialize which object you are working with
  $ServId = "data";
  $i = ($j+1);
  if($i<=$internalServerCount)
    {
    $ServId .= ("L".$i);
    }
  else
    {
    $ServId .= ("R".($i-$internalServerCount));
    }
  if($PrevStatArr == false)
    {
    $PrevStatArr = array("", "");
    }
  if($serverObj == "")
    {
    $serverTitle = $hostname;
    $nextColor1 = "White";
    $nextColor2 = "Black";
    $serverType = "Error404";
    $FlkrEffctOn = false;
    $unmuteSounds = false;//do not unmute sounds
    $outputArr = array($FlkrEffctOn, $unmuteSounds, $ServId, $nextColor1, $serverTitle, $serverType, $nextColor2, false, 0);
    }
  else//calculate the appropiate values for the display object
    {
    //ensure you have the correct name
    //$serverTitle = $serverObj['data']['host']['name'];//might need this later (unlikely)
    $serverTitle = $hostname;//testing this
      
    if(array_search($serverTitle, $windowsServers) != false)
      {
      $serverType = "WindowsIcon";
      }
    else
      {
      if(array_search($serverTitle, $drupalServers) != false)
        {
        $serverType = "DrupalIcon";
        }
      else
        {
        $serverType = "LinuxIcon";//assumed to be Linux
        }
      }
    if($testServerOn == true && $hostname == "debug"){$status = $testServerStatus; }//debugging only
    else{$status = $serverObj['data']['host']['status'];}
    $nameMaxLen = 20;//setting for configuring the character limit for proper formatting of the spacing in the logs
    switch($status)
      {
      case 1://pending
        $nextColor1 = "HotPink";
        $nextColor2 = "White";
        $FlkrEffctOn = false;
        $unmuteSounds = false;//do not unmute sounds
        $outputArr = array($FlkrEffctOn, $unmuteSounds, $ServId, $nextColor1, $serverTitle, $serverType, $nextColor2, false, 0, $status);
        //log the server signaling a Pending status
        if(($PrevStatArr[0] == $serverTitle) && ($PrevStatArr[1]) != $status)
          { 
          //$timestamp = date('Y-m-d_H:i:s');
          $timestamp = date('m-d-Y h:i:sa'); 
          $buffer = "";
          while(strlen(($serverTitle . $buffer)) < $nameMaxLen)
            {
            $buffer .= " ";
            }
          $logMsg = "" . $serverTitle . $buffer . " became \"Pending\" at          " . $timestamp . "\n"; //21+9
          file_put_contents("$logFileName", $logMsg, FILE_APPEND);
          }
        break;
      case 2://up or ok
        $nextColor1 = "Green";
        $nextColor2 = "Orange";
        $FlkrEffctOn = false;
        $unmuteSounds = true;//unmute sounds
        $outputArr = array($FlkrEffctOn, $unmuteSounds, $j, $ServId, $nextColor1, $serverTitle, $serverType, $nextColor2, false, 0, $status);
        //log the server returning to normal only if the previous status isnt already normal
        if(($PrevStatArr[0] == $serverTitle) && ($PrevStatArr[1] != $status))
          {
          //$timestamp = date('Y-m-d_H:i:s');
          $timestamp = date('m-d-Y h:i:sa'); 
          $buffer = "";
          while(strlen(($serverTitle . $buffer)) < $nameMaxLen)
            {
            $buffer .= " ";
            }
          $logMsg = "" . $serverTitle . $buffer . " returned to normal at        " . $timestamp . "\n";//23+7
          file_put_contents("$logFileName", $logMsg, FILE_APPEND);
          }
        break;
      case 4://warning(service) or down(host)
        /* //service case values
        $nextColor1 = "Yellow";
        $nextColor2 = "Crimson";
        */
        //host case values
        $nextColor1 = "Crimson";
        $nextColor2 = "Yellow";
        $nextColor3 = "Fuchsia";
        $nextColor4 = "Gold";
        $FlkrEffctOn = true;
        $outputArr = array($FlkrEffctOn, $ServId, $serverTitle, $serverType, $nextColor1, $nextColor2, $nextColor3, $nextColor4, $j, $status);
        //log the server going down
        if(($PrevStatArr[0] == $serverTitle) && ($PrevStatArr[1] != $status))
          {
          //$timestamp = date('Y-m-d_H:i:s');
          $timestamp = date('m-d-Y h:i:sa'); 
          $buffer = "";
          while(strlen(($serverTitle . $buffer)) < $nameMaxLen)
            {
            $buffer .= " ";
            }
          $logMsg = "" . $serverTitle . $buffer . " went down at                 " . $timestamp . "\n";//14+16
          file_put_contents("$logFileName", $logMsg, FILE_APPEND);
          }
        break;
      case 8://unknown(service) or unreachable(host)
        $nextColor1 = "Yellow";
        $nextColor2 = "Crimson";
        $nextColor3 = "SpringGreen";
        $nextColor4 = "Red";
        $FlkrEffctOn = true;
        $outputArr = array($FlkrEffctOn, $ServId, $serverTitle, $serverType, $nextColor1, $nextColor2, $nextColor3, $nextColor4, $j, $status);
        //log the server becoming unreachable
          if(($PrevStatArr[0] == $serverTitle) && ($PrevStatArr[1] != $status))
          {
          //$timestamp = date('Y-m-d_H:i:s');
          $timestamp = date('m-d-Y h:i:sa');  
          $buffer = "";
          while(strlen(($serverTitle . $buffer)) < $nameMaxLen)
            {
            $buffer .= " ";
            }
          $logMsg = "" . $serverTitle . $buffer . " became unreachable at        " . $timestamp . "\n";//23+7
          file_put_contents("$logFileName", $logMsg, FILE_APPEND);
          }
        break;
      case 16://critical(service) (undefined on host)
        $nextColor1 = "Crimson";
        $nextColor2 = "Yellow";
        $nextColor3 = "Fuchsia";
        $nextColor4 = "Gold";
        $servTitle = ("!".$serverTitle);
        $FlkrEffctOn = true;
        $outputArr = array($FlkrEffctOn, $ServId, $servTitle, $serverType, $nextColor1, $nextColor2, $nextColor3, $nextColor4, $j, $status);
        //log the server signaling a critical(service) status
        if(($PrevStatArr[0] == $serverTitle) && ($PrevStatArr[1] != $status))
          {
          //$timestamp = date('Y-m-d_H:i:s');
          $timestamp = date('m-d-Y h:i:sa'); 
          $buffer = "";
          while(strlen(($serverTitle . $buffer)) < $nameMaxLen)
            {
            $buffer .= " ";
            }
          $logMsg = "" . $serverTitle . $buffer . " turned Critical at           " . $timestamp . "\n";//20+10
          file_put_contents("$logFileName", $logMsg, FILE_APPEND);
          }
        break;
      default://initializing and/or unknown return
        $nextColor1 = "White";
        $nextColor2 = "Black";
        $FlkrEffctOn = false;
        $unmuteSounds = false;//do not unmute sounds
        $outputArr = array($FlkrEffctOn, $unmuteSounds, $ServId, $nextColor1, $serverTitle, $serverType, $nextColor2, false, 0, $status);
        break;
      }
    }
  return $outputArr;
  }
   
//graph update functions  
function UpdateGraphs()
  {
  require("BillboardConfig.php");
  $graphOutputArr = Array();
  //trigger_error("MYERROR5: \$networkMonitoringServerCount=".$networkMonitoringServerCount."\n");
  
  if($networkMonitoringServerCount > 0)
    {
    //if there are network monitoring servers it will fill out the array
      //trigger_error("MYERROR6: \$networkMonitoringServerCount > 0 = true\n");
    for($i=0; $i < $networkMonitoringServerCount; $i++)
      {
      //trigger_error("MYERROR7: \$i=".$i."\n");
      $subArr = Array();
      array_push($subArr, "graph".($i+1));//graph element
      array_push($subArr, $networkMonitoringArray[$i]);//graph name
      array_push($subArr, updateLocalData($networkMonitoringArray[$i]));//data
      array_push($graphOutputArr, $subArr);
      }
    }
  //if there are no network monitoring servers it will return an empty array
  return $graphOutputArr;
  }
// * /
 
function updateLocalData($target)
  {
  require("BillboardConfig.php");
  $fname =  "$target.json";
  require("Credentials.php");
  $dataArray1 = Array();
  if(!file_exists($fname))
    {
    trigger_error("MYERROR1: File ".$fname." not found. Generating new one.");
    }
  else
    {
    //read in the data from the existing json file
    $dataArray1 = json_decode(file_get_contents($fname), true);
    //catch case where json_decode fails due to malformed json input
    if($dataArray1 == null || $dataArray1 == "null")
      {
      trigger_error("MYERROR2: File ".$fname." Malformed! Contents are: \"".file_get_contents($fname)."\"");  
      $dataArray1 = Array();
      }
    }
   
  //get the data from the json file on the server
  $url = "http://$username:$password1@$InternalNagiosAddress/nagios/cgi-bin/statusjson.cgi?query=host&hostname=$target";
  $str1 = file_get_contents($url);
  if($str1 != "")
    {
    $str2 = strchr($str1, "ms, lost", true);
    $value = substr($str2, (strpos($str2, ": rta ")+6));
    //catch case where url returns valid but improper string in which the data value cannot be found
    if($value == false)
      {
      trigger_error("MYERROR3: Valid but improper string returned from ".$target." url! Contents are: \"".$str1."\"");  
      $value = "BLANK";
      }
    }
  else
    {
    $value = "VOID";
    }
  /* start check for data gap and if it exists insert
   * "void" value to create visual gap in the graph
   */
  $arrayLength = count($dataArray1);
  if($arrayLength > 0)
    {
    $arrayLength--;
    $lastDataPoint =  $dataArray1[$arrayLength];
    $timeDiff = (time() - $lastDataPoint[0]);
    if($timeDiff > (5*60))
      {
      $breakPointArray = array(time(), "BREAK");
      //add the break-point value to the array
      array_push($dataArray1, $breakPointArray);
      array_shift($dataArray1);
      }
    }
  $dataPointArray = array(time(), $value);
  //add the new value to the array
  array_push($dataArray1, $dataPointArray);
  if(count($dataArray1) > 240)
    {
    array_shift($dataArray1);
    }

  //save the edited array to the csv file
  $json_data1 = json_encode($dataArray1, JSON_PRETTY_PRINT);
  file_put_contents($fname, $json_data1);
  return $dataArray1;
  }

//generator power monitoring functions

//define the process function
function processSwitch($part1)
  {
  //echo $part1 . "<br>";//debugging only
  $part1 = str_replace(":","=",$part1);
  $part1 = str_replace(",","&",$part1);
  //echo $part1 . "<br>";//debugging only
  parse_str($part1, $dataArray);
  //print_r($dataArray); echo "<br>";//debugging only
  return $dataArray;
  }

function checkGenPwr()
  {
  require("BillboardConfig.php");
  if($testGenAlertOn == true){return $testGenAlertStatus;}//debugging only  
  //retrieve the data
  $runfile = "http://".$RoomAlertAddress."/getData.htm";
  
  $ch = curl_init();
  
  curl_setopt($ch, CURLOPT_URL, $runfile);
  
  curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
  
  $content = curl_exec ($ch);
  
  curl_close ($ch);
  //echo $content . "<br>";//debugging only
  
  /* REMEMBER:
   *    Mini UPS & Power Sensor RMA-MUPS-SEN
   *        CLOSED (Normal) Main power is on.
   *        OPEN (Alarm) Main power is off.
   *    Power Sensor RMA-PS1-SEN
   *      CLOSED (Normal) The power source is on.
   *      OPEN (Alarm) The power source is off.
   *
   * HOWEVER!!: The "Gen Power" switch is inverted.
   * Meaning that when the state goes to "OPEN" the
   * Generator is providing power. "CLOSED" means
   * the Generator is not providing power. I also
   * think this MAY also apply to "UPS Power". I have
   * NOT checked on this as of 3/24/16
   *
   * ALSO: The "Gen Running" from Channel 3 does NOT
   * appear to be hooked up to a sensor and therefore
   * is always set to its default state of "OPEN".
   * The "Power Monitor" switch on Channel 1 is for
   * monitoring the Building Power. Therefore if it's
   * state switches to "OPEN" than there is NO power
   * to the servers from the Building.
   */
  //extract the important parts
  $pos1 = strpos($content, "label:\"UPS Power\"");
  $pos2 = strpos($content, "},{label:\"Gen Running\"");
  $UPSPwr = substr($content, $pos1, ($pos2 - $pos1));
  
  //this one is unused at the moment.
  $pos1 = strpos($content, "label:\"Gen Running\"");
  $pos2 = strpos($content, "},{label:\"Gen Power\"");
  $GenRun = substr($content, $pos1, ($pos2 - $pos1));

  $pos1 = strpos($content, "label:\"Gen Power\"");
  $pos2 = strpos($content, "},{label:\"Switch Sen");
  $GenPwr = substr($content, $pos1, ($pos2 - $pos1));

  //process the data a match to 0 means the switch is "open"
  if(processSwitch($UPSPwr)['status'] == 0)
    {
    return "UPS";
    }
  if(processSwitch($GenPwr)['status'] == 0)
    {
    return "Gen";
    }
  return "Bld";
  } 

function checkDCTemps()
  {
  require("BillboardConfig.php");
  //retrieve the data
  $runfile = "http://".$RoomAlertAddress."/getData.htm";

  $ch = curl_init();

  curl_setopt($ch, CURLOPT_URL, $runfile);

  curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);

  $content = curl_exec ($ch);

  curl_close ($ch);
  //error_log("MYERROR: checkDCTemps() \$content= \\n".$content."\\n");//debugging only

  //extract the important parts
  $pos1 = strpos($content, "label:\"Internal Sensor\"");
  $pos2 = strpos($content, "},{label:\"Cabinet 25\"");
  $Temp1 = round(str_replace("\"","", processSwitch(substr($content, $pos1, ($pos2 - $pos1)))['tempf']), 0);
  //$Temp1 = 90;//debugging only
  $pos1 = strpos($content, "label:\"Cabinet 25\"");
  $pos2 = strpos($content, "},{label:\"Cabinet 26\"");
  $Temp2 = round(str_replace("\"","", processSwitch(substr($content, $pos1, ($pos2 - $pos1)))['tempf']), 0);
  //$Temp2 = 85;//debugging only  
  $pos1 = strpos($content, "label:\"Cabinet 26\"");
  $pos2 = strpos($content, "},{label:\"Cabinet 27\"");
  $Temp3 = round(str_replace("\"","", processSwitch(substr($content, $pos1, ($pos2 - $pos1)))['tempf']), 0);
      
  $pos1 = strpos($content, "label:\"Cabinet 27\"");
  $pos2 = strpos($content, "},{label:\"Ext. Sensor 4\"");
  $Temp4 = round(str_replace("\"","", processSwitch(substr($content, $pos1, ($pos2 - $pos1)))['tempf']), 0);
  
  $TempInfoArr = Array();
  array_push($TempInfoArr, Array("Int.Sen.", $Temp1));
  array_push($TempInfoArr, Array("Cab.25", $Temp2));
  array_push($TempInfoArr, Array("Cab.26", $Temp3));
  array_push($TempInfoArr, Array("Cab.27", $Temp4));
  return $TempInfoArr;
  }
?>