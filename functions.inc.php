<?php

//create script to randmomize
function createRandomizerScript() {


	global $radioStationRepeatScriptFile,$pluginName,$randomizerScript;


	logEntry("Creating Randomizer script: ".$radioStationRepeatScriptFile);

	$data = "";
	$data  = "#!/bin/sh\n";
	$data .= "\n";
	$data .= "#Script to run randomizer\n";
	$data .= "#Created by ".$pluginName."\n";
	$data .= "#\n";
	$data .= "/usr/bin/php ".$randomizerScript."\n";


	$fs = fopen($radioStationRepeatScriptFile,"w");
	fputs($fs, $data);
	fclose($fs);

}

//crate the event file
function createRandomizerEventFile() {

	global $radioStationRepeatScriptFile,$pluginName,$randomizerScript,$radioStationRandomizerEventFile,$MAJOR,$MINOR,$radioStationRadomizerEventName;


	logEntry("Creating Randomizer event file: ".$radioStationRandomizerEventFile);

	$data = "";
	$data .= "majorID=".$MAJOR."\n";
	$data .= "minorID=".$MINOR."\n";

	$data .= "name='".$radioStationRadomizerEventName."'\n";
		
	$data .= "effect=''\n";
	$data .="startChannel=\n";
	$data .= "script='".pathinfo($radioStationRepeatScriptFile,PATHINFO_BASENAME)."'\n";



	$fs = fopen($radioStationRandomizerEventFile,"w");
	fputs($fs, $data);
	fclose($fs);
}
//get the fpp log level
function getFPPLogLevel() {
	
	$FPP_LOG_LEVEL_FILE = "/home/fpp/media/settings";
	if (file_exists($FPP_LOG_LEVEL_FILE)) {
		$FPP_SETTINGS_DATA = parse_ini_file($FPP_LOG_LEVEL_FILE);
	} else {
		//return log level 0
		return 0;
	}
	
		logEntry("FPP Settings file: ".$FPP_LOG_LEVEL_FILE);
		
		$logLevelString = trim($FPP_SETTINGS_DATA['LogLevel']);
		logEntry("Log level in fpp settings file: ".$logLevelString);
		
		switch($logLevelString) {
			
			
			case "info":
				$logLevel=0;
				
			//	break;
				
			case "warn":
				$logLevel=1;
				
			//	break;
				
			case "debug":
				
				$logLevel=2;
				
			//	break;
				
			case "excess":
				
				$logLevel=3;
				
				//break;
				
			 default:
				$logLevel = 0;
				
				
		}
		
		
	
	return $logLevel;
	
}

}
//is fppd running?????
function isFPPDRunning() {
	$FPPDStatus=null;
	logEntry("Checking to see if fpp is running...");
        exec("if ps cax | grep -i fppd; then echo \"True\"; else echo \"False\"; fi",$output);

        if($output[1] == "True" || $output[1] == 1 || $output[1] == "1") {
                $FPPDStatus = "RUNNING";
        }
	//print_r($output);

	return $FPPDStatus;
        //interate over the results and see if avahi is running?

}
//get current running playlist
function getRunningPlaylist() {

	global $sequenceDirectory;
	$playlistName = null;
	$i=0;
	//can we sleep here????

	//sleep(10);
	//FPPD is running and we shoud expect something back from it with the -s status query
	// #,#,#,Playlist name
	// #,1,# = running

	$currentFPP = file_get_contents("/tmp/FPP.playlist");
	logEntry("Reading /tmp/FPP.playlist : ".$currentFPP);
	if($currentFPP == "false") {
		logEntry("We got a FALSE status from fpp -s status file.. we should not really get this, the daemon is locked??");
	}
	$fppParts="";
	$fppParts = explode(",",$currentFPP);
//	logEntry("FPP Parts 1 = ".$fppParts[1]);

	//check to see the second variable is 1 - meaning playing
	if($fppParts[1] == 1 || $fppParts[1] == "1") {
		//we are playing

		$playlistParts = pathinfo($fppParts[3]);
		$playlistName = $playlistParts['basename'];
		logEntry("We are playing a playlist...: ".$playlistName);
		
	} else {

		logEntry("FPPD Daemon is starting up or no active playlist.. please try again");
	}
	
	
	//now we should have had something
	return $playlistName;
}
function processSequenceName($sequenceName,$sequenceAction="NONE RECEIVED") {

	global $CONTROL_NUMBER_ARRAY,$PLAYLIST_NAME,$EMAIL,$PASSWORD,$pluginDirectory,$pluginName;
        logEntry("Sequence name: ".$sequenceName);

        $sequenceName = strtoupper($sequenceName);
	//$PLAYLIST_NAME= getRunningPlaylist();

	if($PLAYLIST_NAME == null) {
		$PLAYLIST_NAME = "FPPD Did not return a playlist name in time, please try again later";
	}
//        switch ($sequenceName) {

 //               case "SMS-STATUS-SEND.FSEQ":

                $messageToSend="";
	//	$gv = new GoogleVoice($EMAIL, $PASSWORD);

		//send a message to all numbers in control array and then delete them from new messages
		for($i=0;$i<=count($CONTROL_NUMBER_ARRAY)-1;$i++) {
			logEntry("Sending message to : ".$CONTROL_NUMBER_ARRAY[$i]. " that playlist: ".$PLAYLIST_NAME." is ACTION:".$sequenceAction);
			//get the current running playlist name! :)	

				//$gv->sendSMS($CONTROL_NUMBER_ARRAY[$i], "PLAYLIST EVENT: ".$PLAYLIST_NAME." Action: ".$sequenceAction);
			//	$gv->sendSMS($CONTROL_NUMBER_ARRAY[$i], "PLAYLIST EVENT: Action: ".$sequenceAction);
		
		}		
		logEntry("Plugin Directory: ".$pluginDirectory);
		//run the sms processor outside of cron
		$cmd = $pluginDirectory."/".$pluginName."/getSMS.php";

		exec($cmd,$output); 



}

function logEntry($data,$logLevel=1) {

	global $logFile,$myPid, $LOG_LEVEL;

	
	if($logLevel <= $LOG_LEVEL) 
		return
		
		$data = $_SERVER['PHP_SELF']." : [".$myPid."] ".$data;
		
		$logWrite= fopen($logFile, "a") or die("Unable to open file!");
		fwrite($logWrite, date('Y-m-d h:i:s A',time()).": ".$data."\n");
		fclose($logWrite);


}



function processCallback($argv) {
	global $DEBUG,$pluginName;
	
	
	if($DEBUG)
		print_r($argv);
	//argv0 = program
	
	//argv2 should equal our registration // need to process all the rgistrations we may have, array??
	//argv3 should be --data
	//argv4 should be json data
	
	$registrationType = $argv[2];
	$data =  $argv[4];
	
	logEntry("PROCESSING CALLBACK: ".$registrationType);
	$clearMessage=FALSE;
	
	switch ($registrationType)
	{
		case "media":
			if($argv[3] == "--data")
			{
				$data=trim($data);
				logEntry("DATA: ".$data);
				$obj = json_decode($data);
	
				$type = $obj->{'type'};
				logEntry("Type: ".$type);	
				switch ($type) {
						
					case "sequence":
						logEntry("media sequence name received: ");	
						processSequenceName($obj->{'Sequence'},"STATUS");
							
						break;
					case "media":
							
						logEntry("We do not support type media at this time");
							
						//$songTitle = $obj->{'title'};
						//$songArtist = $obj->{'artist'};
	
	
						//sendMessage($songTitle, $songArtist);
						//exit(0);
	
						break;
						
						case "both":
								
						logEntry("We do not support type media/both at this time");
						//	logEntry("MEDIA ENTRY: EXTRACTING TITLE AND ARTIST");
								
						//	$songTitle = $obj->{'title'};
						//	$songArtist = $obj->{'artist'};
							//	if($songArtist != "") {
						
						
						//	sendMessage($songTitle, $songArtist);
							//exit(0);
						
							break;
	
					default:
						logEntry("We do not understand: type: ".$obj->{'type'}. " at this time");
						exit(0);
						break;
	
				}
	
	
			}
	
			break;
			exit(0);
	
		case "playlist":

			logEntry("playlist type received");
			if($argv[3] == "--data")
                        {
                                $data=trim($data);
                                logEntry("DATA: ".$data);
                                $obj = json_decode($data);
				$sequenceName = $obj->{'sequence0'}->{'Sequence'};	
				$sequenceAction = $obj->{'Action'};	
                                                processSequenceName($sequenceName,$sequenceAction);
                                                //logEntry("We do not understand: type: ".$obj->{'type'}. " at this time");
                                        //      logEntry("We do not understand: type: ".$obj->{'type'}. " at this time");
			}

			break;
			exit(0);			
		default:
			exit(0);
	
	}
	

}
?>
