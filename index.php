<?php
require("BillboardConfig.php");
?>
<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" type="text/css" href="Billboard.css">
<script src="dygraph-combined.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
</head>
<body>
<div id="calAlertScreen" style="background-color:green; position:absolute; width:100%; height:100%; color:goldenrod; text-align:center;" hidden>
  <h1 id="calAlertMsg" style="position:relative; top:40%; font-size:500%;"></h1>
  <div id="calEventSoundDiv"></div>
</div>
<div class="MainDisplay" id="MainDisplay">
<div class="flex-containerLeft" id="LeftServers">
<?php 
  $defaultImgSrc = "ErrorX.png";
  //add the Internal Server Title Box
  echo "<div class=\"LeftTitle\"><h1>Internal</h1></div>";
  //add the Internal Active Directory Category Box
  if($internalADSCount > 0)
    {
    echo "<div class=\"flex-containerColumns\" style=\"background-color:lightgrey; width:100%;\"><div class=\"LeftTitle\" style=\"width:100%;height:39px;\"><h2>Active Directory Servers</h2></div>\n";
    for($L=1; $L<=$internalADSCount; $L++)
      {
      echo "<div class=\"servers\" id=\"dataL" . $L . "\"><h2 id=\"dataL" . $L . "title\">" . fixHyphen($internalADSArray[$L-1]) . "</h2><img id=\"dataL" . $L . "icon\" src=\"" . $defaultImgSrc . "\" width=\"25\" height=\"25\"><div id=\"dataL".$L."soundDiv\" hidden></div></div>\n";
      }
    echo "</div>\n";
    }
  else
	{
	echo "<!-- there are no Active Directory Servers indicated in the config file therefore no need for a box for monitoring them -->\n";
    }

  //add the Internal Database Server Category Box
  if($internalDBSCount > 0)
	{
    echo "<div class=\"flex-containerColumns\" style=\"background-color:lightgrey; width:100%;\"><div class=\"LeftTitle\" style=\"width:100%;height:39px;\"><h2>Database Servers</h2></div>\n";
    for($L=(1+$internalADSCount); $L<= ($internalADSCount+$internalDBSCount); $L++)
  	  {
      echo "<div class=\"servers\" id=\"dataL".$L."\"><h2 id=\"dataL".$L."title\">".fixHyphen($internalDBSArray[($L-$internalADSCount)-1])."</h2><img id=\"dataL".$L."icon\" src=\"".$defaultImgSrc."\" width=\"25\" height=\"25\"><div id=\"dataL".$L."soundDiv\" hidden></div></div>\n";
      }
    echo "</div>\n";
    }
  else
    {
    echo "<!--there are no Database Servers indicated in the config file therefore no need for a box for monitoring them -->\n";
    }

  //add the Other Internal Servers Category Box
  if($internalServerCount > 0)
    {
    echo "<div class=\"flex-containerColumns\" style=\"background-color:lightgrey; width:100%;\"><div class=\"LeftTitle\" style=\"width:100%;height:39px;\"><h2>Other Servers</h2></div>\n";
    for($L=(1+$internalADSCount+$internalDBSCount); $L<=$internalServerCount; $L++)
      {
      echo "<div class=\"servers\" id=\"dataL".$L."\"><h2 id=\"dataL".$L."title\">".fixHyphen($internalOtherServerArray[($L-($internalADSCount+$internalDBSCount))-1])."</h2><img id=\"dataL".$L."icon\" src=\"".$defaultImgSrc."\" width=\"25\" height=\"25\"><div id=\"dataL".$L."soundDiv\" hidden></div></div>\n";
      }
    echo "</div>\n";
    }
  else
    {
    echo "<!--there are no Internal Servers indicated in the config file therefore no need for a box for monitoring them -->\n";
    }
?>
</div>
<div class="flex-containerCenter">
   <div class="CenterTitle">
      <h1>Network</h1>
   </div>
   <div class="RightTitle" id="PowerStatus" style="height:39px;color:#FFFF00;">
      <h2>Building</h2>
   </div>
   <div id="GenStatSoundDiv" hidden></div>
   <div id="lastUpdate"><h2>Last Update=</h2></div>
   <a href="ViewLogs.php" target="_blank" style="font-size:20px">View Log</a><br>
   <?php 
   if($networkMonitoringServerCount > 0)
     {
     for($i=0; $i < $networkMonitoringServerCount; $i++)
       {
       echo "   <div id=\"graph".($i+1)."\" class=\"trafficGraph\"></div><br>\n";
       }
     }
   else
     {
     echo "<!--there are no Network Monitoring Servers indicated in the config file therefore no need for a box for monitoring them -->\n";
     }
   ?>
</div>
<div class="flex-containerRight">
   <div class="RightTitle">
      <h1>External</h1>
   </div>
   <div class="flex-containerColumns" id="RightServers">
<?php 
if($externalServerCount > 0)
  {
  for($R=1; $R<=$externalServerCount; $R++)
    {
    echo "<div class=\"servers\" id=\"dataR".$R."\"><h2 id=\"dataR".$R."title\">".fixHyphen($externalServerArray[$R-1])."</h2><img id=\"dataR".$R."icon\" src=\"".$defaultImgSrc."\" width=\"25\" height=\"25\"><div id=\"dataR".$R."soundDiv\" hidden></div></div>\n";
    }
  }
else
  {
  echo "<!--there are no External Servers indicated in the config file therefore no need for a box for monitoring them -->\n";
  }

?>
   </div>
   <div class="flex-containerColumns" style="background-color:#A6A6A6; width:100%;align-self:flex-end;margin-top:auto;">
      <div class="RightTitle" style="width:100%;height:39px;">
         <h2>Data Center Temps</h2>
      </div>
      <div class="servers" id="Temp1">
         <h2><a id="Temp1title">Temp1</a><img id="Temp1icon" src="SoundPlaying.png" width="25" height="25" onclick="toggleSound(1,0)" hidden></h2>
        <div id="Temp1soundDiv" hidden></div>
      </div>
      <div class="servers" id="Temp2">
         <h2><a id="Temp2title">Temp2</a><img id="Temp2icon" src="SoundPlaying.png" width="25" height="25" onclick="toggleSound(1,1)" hidden></h2>
         <div id="Temp2soundDiv" hidden></div>
      </div>
      <div class="servers" id="Temp3">
         <h2><a id="Temp3title">Temp3</a><img id="Temp3icon" src="SoundPlaying.png" width="25" height="25" onclick="toggleSound(1,2)" hidden></h2>
         <div id="Temp3soundDiv" hidden></div>
      </div>
      <div class="servers" id="Temp4">
         <h2><a id="Temp4title">Temp4</a><img id="Temp4icon" src="SoundPlaying.png" width="25" height="25" onclick="toggleSound(1,3)" hidden></h2>
         <div id="Temp4soundDiv" hidden></div>
      </div>
   </div>
   <div class="flex-containerColumns" style="background-color:#A6A6A6; width:100%;align-self:flex-end;">
      <div class="RightTitle" style="width:100%;height:39px;">
         <h2>Color Key</h2>
      </div>
      <div class="servers" id="Key1">
         <h2 id="Key1title">Key1</h2>
         <img id="Key1icon" src="ErrorX.png" width="25" height="25">
      </div>
      <div class="servers" id="Key2">
         <h2 id="Key2title">Key2</h2>
         <img id="Key2icon" src="ErrorX.png" width="25" height="25">
      </div>
      <div class="servers" id="Key3">
         <h2 id="Key3title">Key3</h2>
         <img id="Key3icon" src="ErrorX.png" width="25" height="25">
      </div>
      <div class="servers" id="Key4">
         <h2 id="Key4title">Key4</h2>
         <img id="Key4icon" src="ErrorX.png" width="25" height="25">
      </div>
      <div class="servers" id="Key5">
         <h2 id="Key5title">Key5</h2>
         <img id="Key5icon" src="ErrorX.png" width="25" height="25">
      </div>
   </div>
</div>
<?php 
function fixHyphen($inputstring)
  {
   $outputstring = $inputstring;
   if(strpos($outputstring, '-') != false && strlen($outputstring) > 8)
     {
     $outputstring = (substr($outputstring,0,8) . '&#8230;');
     }
   else
     {
     while(strpos($outputstring, '-') != false)
       {
       $outputstring = str_replace('-', '&#8209;&#8203;', $outputstring);
       }
     }
  return $outputstring;
  }
?>
</div>
<div class="myFooter">Dashboard Version <?php echo $verNumber; ?></div>
<script>

setWindowHeight();
//predefine the Color Key
updateStatus("Key1", "HotPink", "Pending", "LinuxIcon", "White", false, 0);
updateStatus("Key2", "Green", "Running", "WindowsIcon", "Orange", false, 0);
updateStatus("Key3", "Yellow", "Warning", "DrupalIcon", "Crimson", false, 0);
updateStatus("Key4", "Crimson", "Down-Critical", "LinuxIcon", "Yellow", false, 0);
updateStatus("Key5", "White", "Status-Unknown", "ErrorX", "Black", false, 0);


var SoundStatusArray = loadMuteData();
serverUpdateCycle();
var GlobalServerUpdateTimer = setInterval(serverUpdateCycle, 15000);
var GlobalPageRefreshTimer = setTimeout(pageRefreshCycle, (5*60*1000));

window.addEventListener("resize",setWindowHeight,false);
//window.onpagehide = saveMuteData(SoundStatusArray);

function setWindowHeight()
  {
  var windowHeight = window.innerHeight;
  document.body.style.height = (windowHeight-16) + "px";
  document.getElementById("MainDisplay").style.height = (windowHeight-32) + "px";
  //console.log(document.body.style.height);
  }
   
function flickerEffect(ServId, serverTitle, serverType, nextColor1, nextColor2, nextColor3, nextColor4, j, stat)
  {
  //console.log("It got to flickerEffect for "+ServId);//debugging only
  switch(stat)
    {
    case 4:
       playSound(ServId, j, "<?php echo $soundClip1;?>");
     //console.log("case4 stat="+stat);//debugging only
       break;
    case 8://warning status. Needs different sound bit and a time delay
       setTimeout(function(){playSound(ServId, j, "<?php echo $soundClip2;?>")}, 500);//delays by 1/2 a second
     //console.log("case8 stat="+stat);//debugging only
       break;
    case 16://critical status (similar to Server Down)
       playSound(ServId, j, "<?php echo $soundClip1;?>");
     //console.log("case16 stat="+stat);//debugging only
       break;
    }
  updateStatus(ServId, nextColor1, serverTitle, serverType, nextColor2, true, j);
  var T1 = setTimeout(callback(ServId, nextColor3, serverTitle, serverType, nextColor4, j), 1000);
  var T2 = setTimeout(callback(ServId, nextColor1, serverTitle, serverType, nextColor2, j), 2000);
  var T3 = setTimeout(callback(ServId, nextColor3, serverTitle, serverType, nextColor4, j), 3000);
  var T4 = setTimeout(callback(ServId, nextColor1, serverTitle, serverType, nextColor2, j), 4000);
  var T5 = setTimeout(callback(ServId, nextColor3, serverTitle, serverType, nextColor4, j), 5000);
  var T6 = setTimeout(callback(ServId, nextColor1, serverTitle, serverType, nextColor2, j), 6000);
  var T7 = setTimeout(callback(ServId, nextColor3, serverTitle, serverType, nextColor4, j), 7000);
  var T8 = setTimeout(callback(ServId, nextColor1, serverTitle, serverType, nextColor2, j), 9000);
  var T9 = setTimeout(callback(ServId, nextColor3, serverTitle, serverType, nextColor4, j), 10000);
  var T10 = setTimeout(callback(ServId, nextColor1, serverTitle, serverType, nextColor2, j), 11000);
  var T11 = setTimeout(callback(ServId, nextColor3, serverTitle, serverType, nextColor4, j), 12000);
  var T12 = setTimeout(callback(ServId, nextColor1, serverTitle, serverType, nextColor2, j), 13000);
  var T13 = setTimeout(callback(ServId, nextColor3, serverTitle, serverType, nextColor4, j), 14000);
 }
 
function setCookie(cname, cvalue)
   {
   //console.log("begin setCookie("+cname+", "+cvalue+")");//debugging only
   var d = new Date();
   d.setTime(d.getTime() + (24*60*60*1000));
   var expires = "expires="+d.toUTCString();
   document.cookie = cname + "=" + cvalue + "; " + expires;
   //console.log("end setCookie("+cname+", cvalue)");//debugging only
   }

function deleteCookie(cname)
   {
   //console.log("begin deleteCookie("+cname+")");//debugging only
   document.cookie = cname + "=; expires=Thu, 01 Jan 1970 00:00:00 UTC";
   //console.log("end deleteCookie("+cname+")");//debugging only
   }

function getCookie(cname)
   {
    //console.log("begin getCookie("+cname+")");//debugging only
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++)
      {
       var c = ca[i];
       while(c.charAt(0) == ' ')
         {
         c = c.substring(1);
         }
       if(c.indexOf(name) == 0)
         {
         //console.log("end getCookie("+cname+") branch1");//debugging only
         return c.substring(name.length,c.length);
         }
      }
   //console.log("end getCookie("+cname+") branch2");//debugging only
    return "";
   }

function loadMuteData()
   {
   //console.log("start loadMuteData()");//debugging only
   var SoundArray = [];
   var servSoundArray = [];
   var tempSoundArray = [];
   var servMuteStatusString = getCookie("muteArray");
   var tempMuteStatusString = getCookie("muteArray2");
   var totalServerCount = <?php echo $totalServerCount; ?>;
   var totalTempDisplayCount = 4;//TODO: Make this dynamic if future changes allow adding/removing temperature sensors

   //Process server Mute Status Array
   //if no cookie exists than create a new server mute status array
   if(servMuteStatusString == "")
      {
      //console.log("servMuteStatusString = blank");//debugging only
      for(var i = 0; i < totalServerCount; i++ )
         {
    	  servSoundArray[i] = 0;//0 = no sound
         }
      //console.log("servSoundArray="+servSoundArray.toString());//debugging only
      //console.log("end loadMuteData() branch1");//debugging only
      }
    else
      {
    	servSoundArray = servMuteStatusString.split(",");

      //check to see if a server has been added/removed: if so, reset the mute status array
      if(servSoundArray.length != totalServerCount)
         {
    	  servSoundArray.length = totalServerCount;
         for(var i = 0; i < totalServerCount; i++ )
            {
        	 servSoundArray[i] = 0;//0 = no sound
            }
         //console.log("servSoundArray="+servSoundArray.toString());//debugging only
         //console.log("end loadMuteData() branch2");//debugging only
         }
      else
         {
         //if the server count remains the same change all character strings into the appropiate integer values
         for(var i = 0; i < servSoundArray.length; i++ )
            {
        	 servSoundArray[i] = parseInt(servSoundArray[i]);
            }
         //console.log("servSoundArray="+servSoundArray.toString());//debugging only
         //console.log("end loadMuteData() branch3");//debugging only
         }
      }

   //Process server Mute Status Array
   //if no cookie exists than create a new server mute status array
   if(tempMuteStatusString == "")
      {
      //console.log("muteStatusString = blank");//debugging only
      for(var i = 0; i < totalTempDisplayCount; i++ )
         {
    	  tempSoundArray[i] = 0;//0 = no sound
         }
      //console.log("tempSoundArray="+tempSoundArray.toString());//debugging only
      //console.log("end loadMuteData() branch4");//debugging only
      }
    else
      {
    	tempSoundArray = tempMuteStatusString.split(",");

      //check to see if a server has been added/removed: if so, reset the mute status array
      if(tempSoundArray.length != totalTempDisplayCount)
         {
    	  tempSoundArray.length = totalTempDisplayCount;
         for(var i = 0; i < totalTempDisplayCount; i++ )
            {
        	 tempSoundArray[i] = 0;//0 = no sound
            }
         //console.log("tempSoundArray="+tempSoundArray.toString());//debugging only
         //console.log("end loadMuteData() branch5");//debugging only
         }
      else
         {
         //if the server count remains the same change all character strings into the appropiate integer values
         for(var i = 0; i < tempSoundArray.length; i++ )
            {
        	 tempSoundArray[i] = parseInt(tempSoundArray[i]);
            }
         //console.log("tempSoundArray="+tempSoundArray.toString());//debugging only
         //console.log("end loadMuteData() branch6");//debugging only
         }
      }
   SoundArray.push(servSoundArray);
   SoundArray.push(tempSoundArray);
   return SoundArray; 
   }

function saveMuteData(muteStatusArray)
   {
   //console.log("begin saveMuteData()");//debugging only
   var servMuteStatusStr = muteStatusArray[0].toString();
   //console.log("servMuteStatusStr="+servMuteStatusStr);//debugging only
   var tempMuteStatusStr = muteStatusArray[1].toString();
   //console.log("tempMuteStatusStr="+tempMuteStatusStr);//debugging only
   
   deleteCookie("muteArray");//must overwrite the old cookie so we delete it
   setCookie("muteArray", servMuteStatusStr);

   deleteCookie("muteArray2");//must overwrite the old cookie so we delete it
   setCookie("muteArray2", tempMuteStatusStr);
   //console.log("end saveMuteData()");//debugging only
   }

function updateStatus(DataID, NewColor1, serverName, serverType, NewColor2, soundToggle, j)
   {
    document.getElementById(DataID).style.background = NewColor1;
    document.getElementById((DataID+"title")).style.color = NewColor2;
    document.getElementById((DataID+"title")).innerHTML = fixHyphen(serverName); 
    Icon = document.getElementById((DataID+"icon"));
    if(serverType == "Error404")//special case for connection failure indication
      {
      Icon.width = 50;//defaults the image size to normal
      Icon.src = (serverType + ".png");
      }
    else
      {
      if(soundToggle == true)//if there IS a sound box
        {
        Icon.width = 50;//resize image to fit new width
        if(SoundStatusArray[0][j] == 1)//if the sound has been muted
          {
          Icon.src = (serverType + "SoundMuted.png");
          }
        else//if the sound is allowed to play
          {
          Icon.src = (serverType + "SoundPlaying.png");
          }
        Icon.onclick = function(){toggleSound(0, j)};
        }
      else
        {
         Icon.width = 25;//defaults the image size to normal
         Icon.src = (serverType + ".png");
        }
      }
   }

function flickerEffect2(p, textColor1, backColor1, textColor2, backColor2)
  {
  updateTemps(p, textColor1, backColor1);
  var Tmp1 = setTimeout(callback2(p, textColor2, backColor2), 1000);
  var Tmp2 = setTimeout(callback2(p, textColor1, backColor1), 2000);
  var Tmp3 = setTimeout(callback2(p, textColor2, backColor2), 3000);
  var Tmp4 = setTimeout(callback2(p, textColor1, backColor1), 4000);
  var Tmp5 = setTimeout(callback2(p, textColor2, backColor2), 5000);
  var Tmp6 = setTimeout(callback2(p, textColor1, backColor1), 6000);
  var Tmp7 = setTimeout(callback2(p, textColor2, backColor2), 7000);
  var Tmp8 = setTimeout(callback2(p, textColor1, backColor1), 9000);
  var Tmp9 = setTimeout(callback2(p, textColor2, backColor2), 10000);
  var Tmp10 = setTimeout(callback2(p, textColor1, backColor1), 11000);
  var Tmp11 = setTimeout(callback2(p, textColor2, backColor2), 12000);
  var Tmp12 = setTimeout(callback2(p, textColor1, backColor1), 13000);
  var Tmp13 = setTimeout(callback2(p, textColor2, backColor2), 14000);
  }

function flickerEffect3(textColor1, backColor1, textColor2, backColor2)
  {
  updateCalAlert(textColor1, backColor1);
  var Cal1 = setTimeout(callback3(textColor2, backColor2), 1000);
  var Cal2 = setTimeout(callback3(textColor1, backColor1), 2000);
  var Cal3 = setTimeout(callback3(textColor2, backColor2), 3000);
  var Cal4 = setTimeout(callback3(textColor1, backColor1), 4000);
  var Cal5 = setTimeout(callback3(textColor2, backColor2), 5000);
  var Cal6 = setTimeout(callback3(textColor1, backColor1), 6000);
  var Cal7 = setTimeout(callback3(textColor2, backColor2), 7000);
  var Cal8 = setTimeout(callback3(textColor1, backColor1), 9000);
  var Cal9 = setTimeout(callback3(textColor2, backColor2), 10000);
  var Cal10 = setTimeout(callback3(textColor1, backColor1), 11000);
  var Cal11 = setTimeout(callback3(textColor2, backColor2), 12000);
  var Cal12 = setTimeout(callback3(textColor1, backColor1), 13000);
  var Cal13 = setTimeout(callback3(textColor2, backColor2), 14000);
  }
  
function updateCalAlert(textColor, backColor)
  {
  document.getElementById("calAlertMsg").style.color = textColor;   
  document.getElementById("calAlertScreen").style.background = backColor;
  }
  
function updateTemps(p, textColor, backColor)
  {
  document.getElementById("Temp"+p+"title").style.color = textColor;   
  document.getElementById("Temp"+p).style.background = backColor;
  if(SoundStatusArray[1][p-1] == 0)
    {
    document.getElementById("Temp"+p+"icon").src = "SoundPlaying.png";
    }
  else
    {
    document.getElementById("Temp"+p+"icon").src = "SoundMuted.png";
    }
  }
  
function toggleSound(i, k)
   {
	//console.log("toggleSound("+i+", "+k+")\n");//debugging only
	  //console.log("SoundStatusArray["+i+"]["+k+"]="+SoundStatusArray[i][k]+"\n");//debugging only
		
    if(SoundStatusArray[i][k] == 1)
      {
      SoundStatusArray[i][k] = 0;//toggles the sound on
      }
    else
      {
      SoundStatusArray[i][k] = 1;//mutes the sound
      }
      saveMuteData(SoundStatusArray);
   }

function playSound(DataID, j, SoundFile1)
   {
   if(SoundStatusArray[0][j] == 0)
	 {
     if($("#"+DataID+"sound").length)
       {
       $("#"+DataID+"sound").remove();
       }
     document.getElementById(DataID+"soundDiv").innerHTML = "<audio id=\""+DataID+"sound\">\n<source src="+SoundFile1+"></source></audio>";
     var soundFX1 = $("#"+DataID+"sound");
     soundFX1[0].play();
	 }
   }

function playSound2(SoundFile2)
  {
  if($("#GenStatSound").length)
    {
    $("#GenStatSound").remove();
    }
  document.getElementById("GenStatSoundDiv").innerHTML = "<audio id=\"GenStatSound\">\n<source src="+SoundFile2+"></source></audio>";
  var soundFX2 = $("#GenStatSound");
  soundFX2[0].play();
  }

function playSound3(SoundFile3, j)
  {
  if(SoundStatusArray[1][j-1] == 0)
    {
    if($("#Temp"+j+"sound").length)
      {
      $("#Temp"+j+"sound").remove();
      }
    document.getElementById("Temp"+j+"soundDiv").innerHTML = "<audio id=\"Temp"+j+"sound\">\n<source src="+SoundFile3+"></source></audio>";
    var soundFX3 = $("#Temp"+j+"sound");
    soundFX3[0].play();
    }
  }

function playSound4(SoundFile4)
{
if($("#calEventSound").length)
  {
  $("#calEventSound").remove();
  }
document.getElementById("calEventSoundDiv").innerHTML = "<audio id=\"calEventSound\">\n<source src="+SoundFile4+"></source></audio>";
var soundFX4 = $("#calEventSound");
soundFX4[0].play();
}
  
function callback(ServId, nextColor1, serverTitle, serverType, nextColor2, j)
   {
    return function()
             {
             updateStatus(ServId, nextColor1, serverTitle, serverType, nextColor2, true, j);
             }
   }


function callback2(p, textColor, backColor)
   {
   return function()
            {
            updateTemps(p, textColor, backColor);
            }
   }

function callback3(textColor, backColor)
   {
   return function()
         {
	     updateCalAlert(textColor, backColor);
         }
   }
   
function pollMaster()
	{
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.withCredentials = true;
	xmlhttp.onreadystatechange = function() 
	   {
	   if(xmlhttp.readyState == 4 && xmlhttp.status == 200) 
	     {
	     if(xmlhttp.responseText == "")
	       {
	       //console.log("pollMaster(BillboardMaster.php)=\n"+xmlhttp.responseText+"\n status = 200 but response is blank\n");//debugging only
	       }
	     else
	       {
		   //console.log("pollMaster(BillboardMaster.php)=\n"+xmlhttp.responseText+"\n status = 200 and response is an array\n");//debugging only
	       MasterObj = JSON.parse(xmlhttp.responseText);
	       SvrStObj = MasterObj[0];
	       GrphStObj = MasterObj[1];
	       GenPwrObj = MasterObj[2];
	       DCTempObj = MasterObj[3];//not enabled yet
	       //alert(GrphStObj);//debugging only
	       //console.log("\n"+obj+"\n");//debugging only
	       /*
	       var i;
	       var debugstr = "(";
	       for(i=0; i < obj.length; i++)
	    	   {
	    	   debugstr += obj[i]+"),\n(";
	    	   }
	       alert(debugstr+")\n");
	       //debugging only */
	       
	       //process the server data and display it
	       var i;
	       for(i=0; i < SvrStObj.length; i++)
	    	   {
	    	   if(SvrStObj[i][0] == true)//flicker effect is on
	    		 {
	    		 //alert("flickerEffect("+SvrStObj[i][1]+", "+SvrStObj[i][2]+", "+SvrStObj[i][3]+", "+SvrStObj[i][4]+", "+SvrStObj[i][5]+", "+SvrStObj[i][6]+", "+SvrStObj[i][7]+", "+SvrStObj[i][8]+", "+SvrStObj[i][9]+")");//debugging only
	    		 
	    		 //flickerEffect(ServId, serverTitle, serverType, nextColor1, nextColor2, nextColor3, nextColor4, j, stat)//reference only
	    		 flickerEffect(SvrStObj[i][1], SvrStObj[i][2], SvrStObj[i][3], SvrStObj[i][4], SvrStObj[i][5], SvrStObj[i][6], SvrStObj[i][7], SvrStObj[i][8], SvrStObj[i][9])
	    		 }
	    	   else
	    		 {
	    		 var offset = 0;//offset for array index. increments if the unmute sounds field is true
	    		 if(SvrStObj[i][1] == true)//we must unmute the sounds
	    		 	{
	    			var j = SvrStObj[i][2];
					if(SoundStatusArray[0][j] != 0)
						{
						SoundStatusArray[0][j] = 0;//toggles the sound on
						saveMuteData(SoundStatusArray);
						}
					offset = 1;
	    		 	}
			     //alert("updateStatus("+SvrStObj[i][2+offset]+", "+SvrStObj[i][3+offset]+", "+SvrStObj[i][4+offset]+", "+SvrStObj[i][5+offset]+", "+SvrStObj[i][6+offset]+", "+SvrStObj[i][7+offset]+", "+SvrStObj[i][9+offset]+")");//debugging only
			    
			     //updateStatus(DataID, NewColor1, serverName, serverType, NewColor2, soundToggle, j);//reference only
			     updateStatus(SvrStObj[i][2+offset], SvrStObj[i][3+offset], SvrStObj[i][4+offset], SvrStObj[i][5+offset], SvrStObj[i][6+offset], SvrStObj[i][7+offset], SvrStObj[i][9+offset]);
	    		 }
	    	   }

	       //process the graph data and display it
	       //TODO: loop through the data and process it into the graphs
	       for(i=0; i < GrphStObj.length; i++)
	    	   {
	    	   updateTrafficGraph(GrphStObj[i][2], GrphStObj[i][1], GrphStObj[i][0])
	    	   }

    	   //process the generator power status and update the display
    	   //console.log("responseText="+xmlhttp.responseText);//debugging only
           if(GenPwrObj == "Bld")
             {
             document.getElementById("PowerStatus").innerHTML = "<h2>Building</h2>";
             document.getElementById("PowerStatus").style.background = "#343434";
             }
           if(GenPwrObj == "Gen")
             {
             document.getElementById("PowerStatus").innerHTML = "<h2>Generator</h2>";
             document.getElementById("PowerStatus").style.background = "Green";
             setTimeout(function(){playSound2("<?php echo $soundClip3;?>")}, 750);//delays by 3/4 of a second
             }
           if(GenPwrObj == "UPS")
             {
             document.getElementById("PowerStatus").innerHTML = "<h2>UPS</h2>";
             document.getElementById("PowerStatus").style.background = "crimson";
             playSound2("<?php echo $soundClip4;?>");
             //setTimeout(function(){playSound2("<?php echo $soundClip4;?>")}, 2000);//delays by 2 seconds
             setTimeout(function(){playSound2("<?php echo $soundClip4;?>")}, 4000);//delays by 4 seconds
             //setTimeout(function(){playSound2("<?php echo $soundClip4;?>")}, 6000);//delays by 6 seconds
             setTimeout(function(){playSound2("<?php echo $soundClip4;?>")}, 8000);//delays by 8 seconds
             //setTimeout(function(){playSound2("<?php echo $soundClip4;?>")}, 10000);//delays by 10 seconds
             setTimeout(function(){playSound2("<?php echo $soundClip4;?>")}, 12000);//delays by 12 seconds
             }

	       //process the temperature data and display it
	       //TODO: loop through the data and process it into the page
	       for(i=0; i < DCTempObj.length; i++)
	    	 {
    	     var p = i+1;
	    	 document.getElementById("Temp"+p+"title").innerHTML = DCTempObj[i][0]+"<br>"+DCTempObj[i][1]+"&deg;F";
	    	 if(DCTempObj[i][1] >= <?php echo $critTempTrigger; ?>)
	    	   {
      		   flickerEffect2(p, "yellow", "crimson", "gold", "fuchsia");
	    	   document.getElementById("Temp"+p+"icon").style.display = "inline";
	    	   if(SoundStatusArray[1][i] == 0)
	    	     {
	    	     document.getElementById("Temp"+p+"icon").src = "SoundPlaying.png";
	    	     setTimeout(function(){playSound3("<?php echo $soundClip5;?>", p)}, 1000);
	    	     }
	    	   else
	    	     {
	    	     document.getElementById("Temp"+p+"icon").src = "SoundMuted.png";
	    	     }
	    	   }
	    	 else
    		   {
	    	   var medTempTrigger = (i == 0) ? 1000 : <?php echo $medTempTrigger;?>;
	    	   //console.log("medTempTrigger = "+medTempTrigger);//debugging only
   	    	   document.getElementById("Temp"+p+"icon").style.display = "none";
	    	   if(DCTempObj[i][1] >= medTempTrigger)
	    	     {
    		     flickerEffect2(p, "crimson", "yellow", "red", "springgreen");
	    	     }
	    	   else
	    	     {
		    	 updateTemps(p, "orange", "mediumblue");
	    	     }
    		   }  
	    	 }
	       }
	     }
	   else
	     {
	     if(xmlhttp.readyState == 4 && xmlhttp.status == 404) 
	       {
		   //console.log("pollMaster(BillboardMaster.php)=\n"+xmlhttp.responseText+"\n status = 404\n");//debugging only
	       }
	     }
	   }
	xmlhttp.open("POST", "BillboardMaster.php", true);
	xmlhttp.send();
	}

function updateTrafficGraph(data, graphName, graphElement)
  {
  console.log("Started updateTrafficGraph()");//debugging only
  if(data == "")
    {
    //do something here to indicate missing json data
    console.log("it got to updateTrafficGraph(data=blank, "+graphElement+")");//debugging only
    }
  else
    {
    //break apart the obj into usable graph data and display it
    var g = new Dygraph(document.getElementById(graphElement),
    data,
      {
      //drawPoints: true,
      title: (graphName),
      pixelsPerLabel: 20,
      yLabelWidth: 60,
      labels: ['Date', 'Value'],
      axes:
        {
        x:
          {
          pixelsPerLabel: 48,
          axisLabelFormatter: function(date, gran)
            {
            var d = new Date(date*1000);
            return Dygraph.zeropad(d.getHours()) + ":"
            + Dygraph.zeropad(d.getMinutes()) + ":"
            + Dygraph.zeropad(d.getSeconds());
            }
          }
        },
      labelsDivStyles: { 'textAlign': 'right' },
      });
      console.log("Finished updateTrafficGraph()");//debugging only
    }
  }
	
function serverUpdateCycle()
   {
   pollMaster();
   var d = new Date();
   document.getElementById("lastUpdate").innerHTML = ("Last Update:" + d.toLocaleTimeString());
   <?php 
     if($networkMonitoringServerCount > 0)
       {
       for($i=0; $i < $networkMonitoringServerCount; $i++)
         {
         echo "   document.getElementById(\"graph".($i+1)."\").style.display = \"block\";\n";
         }
       }
     else
       {
       echo "   console.log('\\nThere are no Network Monitoring Servers indicated in the config file therefore no need for a box for monitoring them.\\n');\n";
       }
     ?>
   document.getElementById("calAlertScreen").style.display = "none";
   <?php 
   for($C = 0; $C < count($calAlert); $C++)
     {
     $EventTimeArr = explode(" ", substr($calAlert[$C], 0, strpos($calAlert[$C], "%")));
     echo "if((d.getSeconds() >= 15 && d.getSeconds() <= 45)";
     if($EventTimeArr[0] != "*")
       {
       if(strstr($EventTimeArr[0], "-") != false)
         {
         $MinTimeRangeArr = explode("-", $EventTimeArr[0]);
         echo " && (d.getMinutes() >= ".$MinTimeRangeArr[0]." && d.getMinutes() <= ".$MinTimeRangeArr[1].")";
         }
       else
         {
         echo " && (d.getMinutes() == ".$EventTimeArr[0].")";
         }
       }
     if($EventTimeArr[1] != "*")
       {
       if(strstr($EventTimeArr[1], "-") != false)
         {
         $HourTimeRangeArr = explode("-", $EventTimeArr[1]);
         echo " && (d.getHours() >= ".$HourTimeRangeArr[0]." && d.getHours() <= ".$HourTimeRangeArr[1].")";
         }
       else
         {
         echo " && (d.getHours() == ".$EventTimeArr[1].")";
         }
       } 
     if($EventTimeArr[2] != "*")
       {
       if(strstr($EventTimeArr[2], "-") != false)
         {
         $DateTimeRangeArr = explode("-", $EventTimeArr[2]);
         echo " && (d.getDate() >= ".$DateTimeRangeArr[0]." && d.getDate() <= ".$DateTimeRangeArr[1].")";
         }
       else
         {
         echo " && (d.getDate() == ".$EventTimeArr[2].")";
         }
       }
     if($EventTimeArr[3] != "*")
       {
       if(strstr($EventTimeArr[3], "-") != false)
         {
         $DateTimeRangeArr = explode("-", $EventTimeArr[3]);
         echo " && (d.getMonth() >= ".$MonthTimeRangeArr[0]." && d.getMonth() <= ".$MonthTimeRangeArr[1].")";
         }
       else
         {
         echo " && (d.getMonth() == ".$EventTimeArr[3].")";
         }
       }
     if($EventTimeArr[4] != "*")
       {
       if(strstr($EventTimeArr[4], "-") != false)
         {
         $DateTimeRangeArr = explode("-", $EventTimeArr[4]);
         echo " && (d.getDay() >= ".$DayTimeRangeArr[0]." && d.getDay() <= ".$DayTimeRangeArr[1].")";
         }
       else
         {
         echo " && (d.getDay() == ".$EventTimeArr[4].")";
         }
       }
     echo "){\n";
     $EventNameStr = substr($calAlert[$C], (strpos($calAlert[$C], "%")+1));
     
     if($networkMonitoringServerCount > 0)
       {
       for($i=0; $i < $networkMonitoringServerCount; $i++)
         {
         echo "   document.getElementById(\"graph".($i+1)."\").style.display = \"none\";\n";
         }
       }
     else
       {
       echo "   console.log('\\nThere are no Network Monitoring Servers indicated in the config file therefore no need for a box for monitoring them.\\n');\n";
       }
     echo "document.getElementById(\"calAlertMsg\").innerHTML = \"".$EventNameStr."\";\n";
     echo "document.getElementById(\"calAlertScreen\").style.display = \"block\";\n";
     echo "flickerEffect3(\"crimson\", \"yellow\", \"red\", \"springgreen\");\n";
     echo "playSound4(\"".$soundClip6."\");\n";
     echo "}\n";
     }
   ?>
   }


function pageRefreshCycle()
   {
   window.location.reload();
   }

function fixHyphen(inputstring)
  {
  var outputstring = inputstring;
  if((outputstring.indexOf('-') < 0) && (outputstring.length > 8))
    {
    outputstring = (outputstring.slice(0,8)+'&#8230;');
    }
  else
    {
    while(outputstring.indexOf('-') >= 0)
       {
       outputstring = outputstring.replace('-', '&#8209;&#8203;')
       }
     }
    //console.log(outputstring);//debugging only
  return outputstring;
  }

</script>
</body>
</html>
