<?php
$pluginName = "RadioStation";
$pluginVersion ="1.0";

//TODO: get .git/config [remote "origin"] url =
$gitURL = "https://github.com/LightsOnHudson/FPP-Plugin-RadioStation.git";

$logFile = $settings['logDirectory']."/".$pluginName.".log";
$pluginConfigFile = $settings['configDirectory'] . "/plugin." . $pluginName;


function get_ini_config(){
    global $pluginName;
    global $pluginConfigFile;
    global $logFile;
    echo "pluginName: $pluginName<br>";
    echo "pluginConfigFile: $pluginConfigFile<br>";
    echo "log_file: $logFile<br>";

    $my_settings = parse_ini_file($pluginConfigFile, True);
    // LoadPluginSettings($pluginName); # doesn't get headings/sections :-(
    // Debug info
    ?>
    <pre>
    <?php 
        echo print_r($my_settings, TRUE);
    ?>
    </pre>
    POSTED
    <pre>
    <?php 
    echo print_r($_POST, TRUE);
    ?>
    </pre>
    <?php
    return $my_settings;
}


function run_scripts(){
    $path = dirname(__FILE__);
    recurseCopy($path."/scripts", "/tmp/scripts");
    
    echo "<br>$path/scripts";
    $output = shell_exec('ls -l /tmp/scripts');
    $output = $output."<br>".shell_exec("chmod +x /tmp/scripts/generate.sh && /tmp/scripts/generate.sh '$path'");
    echo "<br><pre>$output</pre>";
}



function recurseCopy(
    string $sourceDirectory,
    string $destinationDirectory,
    string $childFolder = ''
): void {
    $directory = opendir($sourceDirectory);

    if (is_dir($destinationDirectory) === false) {
        echo "<br>mkdir $destinationDirectory";
        mkdir($destinationDirectory);
    }

    if ($childFolder !== '') {
        if (is_dir("$destinationDirectory/$childFolder") === false) {
            mkdir("$destinationDirectory/$childFolder");
        }

        while (($file = readdir($directory)) !== false) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            if (is_dir("$sourceDirectory/$file") === true) {
                recurseCopy("$sourceDirectory/$file", "$destinationDirectory/$childFolder/$file");
            } else {
                copy("$sourceDirectory/$file", "$destinationDirectory/$childFolder/$file");
            }
        }

        closedir($directory);

        return;
    }

    while (($file = readdir($directory)) !== false) {
        if ($file === '.' || $file === '..') {
            continue;
        }

        if (is_dir("$sourceDirectory/$file") === true) {
            recurseCopy("$sourceDirectory/$file", "$destinationDirectory/$file");
        }
        else {
            copy("$sourceDirectory/$file", "$destinationDirectory/$file");
        }
    }

    closedir($directory);
}

// function PrintMediaOptions($selectName,$selectedOption)
// {
//     global $musicDirectory;
//     global $videoDirectory;
//     global $OPEN;
//     // echo "OPEN: ".$OPEN."<br/> \n";
//     echo "<select name=\"".$selectName."\">";

//     $mediaEntries = array_merge(scandir($musicDirectory),scandir($videoDirectory));
//     sort($mediaEntries);
//     foreach($mediaEntries as $mediaFile)
//     {
//         if($mediaFile != '.' && $mediaFile != '..')
//         {
//             if($selectedOption != "" && $selectedOption == $mediaFile) {

//                 echo "<option selected value=\"" . $mediaFile . "\">" . $mediaFile . "</option>";

//             } else {

//                 echo "<option value=\"" . $mediaFile . "\">" . $mediaFile . "</option>";
//             }
//         }
//     }
//     echo "</select>";
// }

function checkbox($group, $label, $name, $checked){
    $check = "";
    // echo "<br> checked? $checked";
    if ($checked == 1){
        $check = "checked";
    }
    return "<br><input type=\"checkbox\" id=\"$group~$name\" name=\"$group~$name\" value=\"1\" $check> <label for=\"$name\">$name</label>";
}


function input_text($group, $label, $name, $value){
    return "<br><label for=\"$label\">$label</label>: <input type=\"text\" name=\"$group~$name\" size=\"32\" value=\"$value\">\n";
}

function input_hidden($name, $value){
    return "<input type=\"hidden\" id=\"$name\" name=\"$name\" value=\"$value\">\n";
}

function edit_config_group($pl, $group_name, $prefix){
    /**
     * repeatable config area
     * 
     * @param string $group_name
     * @param string $prefix
     * @return string
     */
    return "<b>$group_name [X]</b>".input_text($pl, "name", "PLAYLIST_NAME", $group_name).input_text($pl, "prefix", "PREFIX", $prefix);
}


function post_to_array($post){
    /** */
    $thing = [];
    foreach ($post as $key => $values) {
        // echo "<br>$key=>$values";
        if ($key === "submit"){
            continue;
        }
        $s = explode("~", $key);
        $group = $s[0];
        $setting = $s[1];
        $thing[$group][$setting] = $values;
        echo "<br>[$group] $setting = $values";
    }

    return $thing;
}

function how_many($config, $key_start){
    $count = 0;
    // count the existing PL entries
    foreach ($config as $key => $value) {
        // echo "<br>$key starts? $key_start";
        if(substr( $key, 0, 2 ) === $key_start) {
            $count++;
        }
    }
    return $count;
}


function add_new($new_post, $existing){
    $posted = post_to_array($new_post);
    echo "<br>new posted: $posted";
    $counted = how_many($existing, "PL");
    $new_num = $counted + 1;
    echo "<br>Existing: $counted, adding as $new_num";
    foreach( $posted as $key=>$vals){
        echo "<br>$key => $vals";
        $existing["PL".$new_num] = $vals;
    }
    return $existing;
}


if (!function_exists('write_ini_file')) {
    /**
     * Write an ini configuration file
     * 
     * @param string $file
     * @param array  $array
     * @return bool
     */
    function write_ini_file($file, $array = []) {
        echo "<br>writing ".print_r($array, TRUE)." to $file";
        // check first argument is string
        if (!is_string($file)) {
            throw new \InvalidArgumentException('Function argument 1 must be a string.');
        }

        // check second argument is array
        if (!is_array($array)) {
            throw new \InvalidArgumentException('Function argument 2 must be an array.');
        }

        // process array
        $data = array();
        foreach ($array as $key => $val) {
            if (is_array($val)) {
                $data[] = "[$key]";
                foreach ($val as $skey => $sval) {
                    if (is_array($sval)) {
                        foreach ($sval as $_skey => $_sval) {
                            if (is_numeric($_skey)) {
                                $data[] = $skey.'[] = '.(is_numeric($_sval) ? $_sval : (ctype_upper($_sval) ? $_sval : '"'.$_sval.'"'));
                            } else {
                                $data[] = $skey.'['.$_skey.'] = '.(is_numeric($_sval) ? $_sval : (ctype_upper($_sval) ? $_sval : '"'.$_sval.'"'));
                            }
                        }
                    } else {
                        $data[] = $skey.' = '.(is_numeric($sval) ? $sval : (ctype_upper($sval) ? $sval : '"'.$sval.'"'));
                    }
                }
            } else {
                $data[] = $key.' = '.(is_numeric($val) ? $val : (ctype_upper($val) ? $val : '"'.$val.'"'));
            }
            // empty line
            $data[] = null;
        }

        // open file pointer, init flock options
        $fp = fopen($file, 'w');
        $retries = 0;
        $max_retries = 100;

        if (!$fp) {
            return false;
        }

        // loop until get lock, or reach max retries
        do {
            if ($retries > 0) {
                usleep(rand(1, 5000));
            }
            $retries += 1;
        } while (!flock($fp, LOCK_EX) && $retries <= $max_retries);

        // couldn't get the lock
        if ($retries == $max_retries) {
            return false;
        }

        // got lock, write data
        fwrite($fp, implode(PHP_EOL, $data).PHP_EOL);

        // release lock
        flock($fp, LOCK_UN);
        fclose($fp);

        return true;
    }
}


?>