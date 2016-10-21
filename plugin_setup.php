<?php
//$DEBUG=true;

include_once "/opt/fpp/www/common.php";
include_once 'functions.inc.php';
include_once 'commonFunctions.inc.php';
$pluginName = "RadioStation";
$OPEN="";
$CLOSE="";
$ANNOUNCE_1="";
$ANNOUNCE_2="";
$ANNOUNCE_3="";
$RANDOM="";
$PLAYLIST_NAME="";
$MAJOR = "99";
$MINOR = "99";
$eventExtension = ".fevt";
//arg0 is  the program
//arg1 is the first argument in the registration this will be --list
//$DEBUG=true;

$radioStationControlSettingsFile = $settings['mediaDirectory'] . "/config/plugin.".$pluginName;

$radioStationRepeatScriptFile = $settings['scriptDirectory'] ."/".$pluginName."_RANDOMIZE.sh";

$radioStationRandomizerEventFile = $eventDirectory."/".$MAJOR."_".$MINOR.$eventExtension;
$radioStationRadomizerEventName = $pluginName."_RANDOMIZER";

$randomizerScript = $pluginDirectory ."/".$pluginName."/"."randomizer.php";

$radioStationSettings = array();


$logFile = $settings['logDirectory']."/".$pluginName.".log";



if(isset($_POST['submit']))
{
	
	//$PLAYLIST_NAME = preg_replace('/\s+/', '', $_POST["PLAYLIST_NAME"]);
	$PLAYLIST_NAME = urlencode($_POST["PLAYLIST_NAME"]);
    WriteSettingToFile("OPEN",$_POST["OPEN"],$pluginName);
   // WriteSettingToFile("ENABLED",$_POST["ENABLED"],$pluginName);
    WriteSettingToFile("RANDOM_REPEAT",$_POST["RANDOM_REPEAT"],$pluginName);
    WriteSettingToFile("CLOSE",$_POST["CLOSE"],$pluginName);
    WriteSettingToFile("PLAYLIST_NAME",$PLAYLIST_NAME,$pluginName);
    WriteSettingToFile("ANNOUNCE_1",$_POST["ANNOUNCE_1"],$pluginName);
    WriteSettingToFile("ANNOUNCE_2",$_POST["ANNOUNCE_2"],$pluginName);
    WriteSettingToFile("ANNOUNCE_3",$_POST["ANNOUNCE_3"],$pluginName);
    WriteSettingToFile("RANDOM",trim($_POST["RANDOM"]),$pluginName);
    WriteSettingToFile("PREFIX",trim($_POST["PREFIX"]),$pluginName);
    
    
  //  $cronCmd = "*/5 * * * * /home/ramesh/backup.sh";
    	
  //  $addToCronCmd = "echo ".$cronCmd." >> "
  
    //run the randomizer
    

	//echo "RANDOMIZING";

	$cmd = "/usr/bin/php ".$pluginDirectory."/".$pluginName."/randomizer.php";
	
	system($cmd,$output);
}
	

	//load the$PLAYLIST_NAME = urldecode($pluginSettings['PLAYLIST_NAME']); file settings using the library scrubfile
	
$OPEN = urldecode($pluginSettings['OPEN']);

	//$OPEN = ReadSettingFromFile("OPEN",$pluginName);
	$CLOSE =  urldecode($pluginSettings['CLOSE']);
	
	$ANNOUNCE_1 = urldecode($pluginSettings['ANNOUNCE_1']);
	$ANNOUNCE_2 = urldecode($pluginSettings['ANNOUNCE_1']);
	$ANNOUNCE_3 = urldecode($pluginSettings['ANNOUNCE_3']);
	
	$RANDOM = urldecode($pluginSettings['RANDOM']);
	$PLAYLIST_NAME = urldecode($pluginSettings['PLAYLIST_NAME']);
	$PREFIX = urldecode($pluginSettings['PREFIX']);
	$ENABLED = urldecode($pluginSettings['ENABLED']);
	$RANDOM_REPEAT = urldecode($pluginSettings['RANDOM_REPEAT']);
	
	//$CLOSE = ReadSettingFromFile("CLOSE",$pluginName);
	//$ANNOUNCE_1 = ReadSettingFromFile("ANNOUNCE_1",$pluginName);
	
	//$ANNOUNCE_2 = ReadSettingFromFile("ANNOUNCE_2",$pluginName);
	//$ANNOUNCE_3 = ReadSettingFromFile("ANNOUNCE_3",$pluginName);
	//$RANDOM = ReadSettingFromFile("RANDOM",$pluginName);
	//$PLAYLIST_NAME = urldecode(ReadSettingFromFile("PLAYLIST_NAME",$pluginName));
	//$PREFIX = ReadSettingFromFile("PREFIX",$pluginName);
	//$ENABLED = ReadSettingFromFile("ENABLED",$pluginName);
	//$RANDOM_REPEAT = ReadSettingFromFile("RANDOM_REPEAT",$pluginName);

	
	logEntry("Randmize script file: ".$radioStationRepeatScriptFile);
	if($RANDOM_REPEAT == 1) {
		createRandomizerScript();
		createRandomizerEventFile();
	}

//	echo "OPEN: ".$OPEN."<br/> \n";
//	echo "ANNOUNCE_1: ".$ANNOUNCE_1."<br/> \n";
//	echo "ANNOUNCE_@: ".$ANNOUNCE_2."<br/> \n";
//	echo "ANNOUNCE 3: ".$ANNOUNCE_3."<br/> \n";
//	echo "RANDOM: ".$RANDOM."<br/> \n";
//	echo "CLOSE: ".$CLOSE;


?>

<html>
<head>
</head>

<div id="RadioStation" class="settings">
<fieldset>
<legend>Radio Station control Support Instructions</legend>

<p>Known Issues:
<ul>
<li>NONE </li>
</ul>

<p>Configuration:
<ul>
<li>Configure your Songs, Open, Close static announcements</li>
<li>If you want to automatically randomize your playlist entires, you can include the config from the Crontab file located inside the plugin folder</li>
<li>The randomizer will randomly select and schedule songs matching the prefix that you configure below. This allows you to use songs outside of your show.</li>
</ul>


<p>Randomizer:
<ul>
<li>If selected: Randomize on Repeat the plugin will automatically insert an Event to a script to call the randomizer during the second to last
executing item in the playlist. So upon playlist repeat (you must manually enable this feature) in the Scheduler or manually when playing the playlist on the Status Screen</li>
</ul>
<form method="post" action="http://<? echo $_SERVER['SERVER_NAME']?>/plugin.php?plugin=<?echo $pluginName;?>&page=plugin_setup.php">

<p/>

<?

$restart=0;
$reboot=0;

echo "ENABLE PLUGIN: ";

//if($ENABLED== 1 || $ENABLED == "on") {
	//	echo "<input type=\"checkbox\" checked name=\"ENABLED\"> \n";
PrintSettingCheckbox("Plugin: ".$pluginName." ", "ENABLED", $restart = 0, $reboot = 0, "ON", "OFF", $pluginName = $pluginName, $callbackName = "");
//	} else {
//		echo "<input type=\"checkbox\"  name=\"ENABLED\"> \n";
//}


echo "<p/> \n";

echo "Randomize on Repeat: ";

if($RANDOM_REPEAT== 1 ) {
	echo "<input type=\"checkbox\" checked name=\"RANDOM_REPEAT\"> \n";
	//PrintSettingCheckbox("Radio Station", "ENABLED", $restart = 0, $reboot = 0, "ON", "OFF", $pluginName = $pluginName, $callbackName = "");
} else {
	echo "<input type=\"checkbox\"  name=\"RANDOM_REPEAT\"> \n";
}


echo "<p/> \n";

echo "Playlist Name: ";

echo "<input type=\"text\" name=\"PLAYLIST_NAME\" size=\"32\" value=\"".$PLAYLIST_NAME."\"> \n";
	

echo "<hr> \n";
echo "OPEN: \t";
PrintMediaOptions("OPEN",$OPEN);
 
  echo "<p/> \n";
  
  echo "CLOSE: \t";
  PrintMediaOptions("CLOSE",$CLOSE);
  
  echo "<p/> \n";
  echo "ANNOUNCE 1: \t";
  PrintMediaOptions("ANNOUNCE_1",$ANNOUNCE_1);
  
  echo "<p/> \n";
  echo "ANNOUNCE 2: \t";
  PrintMediaOptions("ANNOUNCE_2",$ANNOUNCE_2);
  
  echo "<p/> \n";
  echo "ANNOUNCE 3: \t";
  PrintMediaOptions("ANNOUNCE_3",$ANNOUNCE_3);
  
  echo "<p/> \n";
  echo "Maximum # of songs between Announcements: (Will be randomized, and depending on amount of files availble, may be less)";
  
  echo "<input type=\"text\" name=\"RANDOM\" size=\"4\" value=\"".$RANDOM."\"> \n";
			
  echo "<p/> \n";
  echo "Prefix of Audio Files to use EXAMPLE: (see below) ";
  
  if($PREFIX == "") {
  	$PREFIX="RADIO-";
  }
  
  echo "<input type=\"text\" name=\"PREFIX\" size=\"16\" value=\"".$PREFIX."\"> \n";
  echo "<pre>".$PREFIX."CHRISTMASSONG.mp3</pre> ";
  

  function PrintMediaOptions($selectName,$selectedOption)
  {
  	global $musicDirectory;
  	global $videoDirectory;
  	global $OPEN;
  	// echo "OPEN: ".$OPEN."<br/> \n";
  	echo "<select name=\"".$selectName."\">";
  
  	$mediaEntries = array_merge(scandir($musicDirectory),scandir($videoDirectory));
  	sort($mediaEntries);
  	foreach($mediaEntries as $mediaFile)
  	{
  		if($mediaFile != '.' && $mediaFile != '..')
  		{
  			if($selectedOption != "" && $selectedOption == $mediaFile) {
  
  				echo "<option selected value=\"" . $mediaFile . "\">" . $mediaFile . "</option>";
  
  			} else {
  
  				echo "<option value=\"" . $mediaFile . "\">" . $mediaFile . "</option>";
  			}
  		}
  	}
  	echo "</select>";
  }
  

?>
<p/>
<input id="submit_button" name="submit" type="submit" class="buttons" value="Save Config">

</form>


<p>To report a bug, please file it against the sms Control plugin project on Git: https://github.com/LightsOnHudson/FPP-Plugin-Radio-Station

</fieldset>
</div>
<br />
</html>
