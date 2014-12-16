<?php
//$DEBUG=true;

include_once "/opt/fpp/www/common.php";
$pluginName = "RadioStation";
$OPEN="";
$CLOSE="";
$ANNOUNCE_1="";
$ANNOUNCE_2="";
$ANNOUNCE_3="";
$RANDOM="";
$PLAYLIST_NAME="";

$radioStationControlSettingsFile = $settings['mediaDirectory'] . "/config/plugin.".$pluginName;

$radioStationSettings = array();

//arg0 is  the program
//arg1 is the first argument in the registration this will be --list
//$DEBUG=true;
$logFile = $settings['logDirectory']."/".$pluginName.".log";

function logEntry($data) {

	global $logFile;

	$data = $_SERVER['PHP_SELF']." : ".$data;
	$logWrite= fopen($logFile, "a") or die("Unable to open file!");
	fwrite($logWrite, date('Y-m-d h:i:s A',time()).": ".$data."\n");
	fclose($logWrite);
}

if(isset($_POST['submit']))
{
    WriteSettingToFile("OPEN",$_POST["OPEN"],$pluginName);
    
    WriteSettingToFile("CLOSE",$_POST["CLOSE"],$pluginName);
    WriteSettingToFile("PLAYLIST_NAME",$_POST["PLAYLIST_NAME"],$pluginName);
    WriteSettingToFile("ANNOUNCE_1",$_POST["ANNOUNCE_1"],$pluginName);
    WriteSettingToFile("ANNOUNCE_2",$_POST["ANNOUNCE_2"],$pluginName);
    WriteSettingToFile("ANNOUNCE_3",$_POST["ANNOUNCE_3"],$pluginName);
    WriteSettingToFile("RANDOM",$_POST["RANDOM"],$pluginName);
    
  //  $cronCmd = "*/5 * * * * /home/ramesh/backup.sh";
    	
  //  $addToCronCmd = "echo ".$cronCmd." >> "

}

	

	//load the file settings using the library scrubfile
	
	$OPEN = ReadSettingFromFile("OPEN",$pluginName);
	$CLOSE = ReadSettingFromFile("CLOSE",$pluginName);
	$ANNOUNCE_1 = ReadSettingFromFile("ANNOUNCE_1",$pluginName);
	$ANNOUNCE_2 = ReadSettingFromFile("ANNOUNCE_2",$pluginName);
	$ANNOUNCE_3 = ReadSettingFromFile("ANNOUNCE_3",$pluginName);
	$RANDOM = ReadSettingFromFile("RANDOM",$pluginName);
	$PLAYLIST_NAME = ReadSettingFromFile("PLAYLIST_NAME",$pluginName);
	
	

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
<li>Filenames of Media cannot have spaces in them right now :(</li>
</ul>

<p>Configuration:
<ul>
<li>Configure your Songs, Open, Close static announcements</li>
</ul>

<form method="post" action="http://<? echo $_SERVER['SERVER_NAME']?>/plugin.php?plugin=RadioStation&page=plugin_setup.php">

<p/>

<?

$restart=0;
$reboot=0;

echo "Playlist Name (NoSpaces): ";

echo "<input type=\"text\" name=\"PLAYLIST_NAME\" size=\"32\" value=\"".$PLAYLIST_NAME."\"> \n";
	

echo "<hr> \n";
echo "OPEN: ";
PrintMediaOptions("OPEN",$OPEN);
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
  
  echo "<p/> \n";
  
  echo "CLOSE: ";
  PrintMediaOptions("CLOSE",$CLOSE);
  
  echo "<p/> \n";
  echo "Announce 1: ";
  PrintMediaOptions("ANNOUNCE_1",$ANNOUNCE_1);
  
  echo "<p/> \n";
  echo "ANNOUNCE 2: ";
  PrintMediaOptions("ANNOUNCE_2",$ANNOUNCE_2);
  
  echo "<p/> \n";
  echo "ANNOUNCE 3: ";
  PrintMediaOptions("ANNOUNCE_3",$ANNOUNCE_3);
  
  echo "<p/> \n";
  echo "Random # of songs between Announcements: ";
  
  echo "<input type=\"text\" name=\"RANDOM\" size=\"4\" value=\"".$RANDOM."\"> \n";
			
			
  

?>
<p/>
<input id="submit_button" name="submit" type="submit" class="buttons" value="Save Config">
</form>


<p>To report a bug, please file it against the sms Control plugin project on Git: https://github.com/LightsOnHudson/FPP-Plugin-Radio-Station

</fieldset>
</div>
<br />
</html>
