<?php 
define('ROOT_PATH', dirname(__DIR__) . '/');
include_once ROOT_PATH.'/funcs.inc.php';

// Nav
include_once "header.php";

$my_settings = get_ini_config();
echo ("plugin update file: ".$pluginUpdateFile);
// END Debug info

if(isset($_POST['submit']))
{
    if ($_GET["opt"]){
        echo "<br>opt: ".$_GET["opt"];
        // add new to configs
        $post_arr = add_new($_POST, $my_settings);
    }else{
        $post_arr = post_to_array($_POST);
    }
    // update my_settings, based on post info
    echo "<br>submitted: ".print_r($post_arr, TRUE);
  if (write_ini_file($pluginConfigFile, $post_arr)){
    echo "<br>[Success!] settings written to $pluginConfigFile";
  } else {
    echo "<br>[Failed!] failed to write to $pluginConfigFile";
  }
  // re-read
  $my_settings = parse_ini_file($pluginConfigFile, True);
    ?>
    <pre>
    <?php 
        echo print_r($my_settings, TRUE);
    ?>
    </pre>
    <?php
}

?>

<div id="RadioStation" class="settings">
    <fieldset>
    <legend><?php echo $pluginName." Version: ".$pluginVersion;?> Support Instructions</legend>
    
    <form method="post" action="http://<?php echo $_SERVER['SERVER_NAME']?>/plugin.php?plugin=<?echo $pluginName;?>&page=plugin_setup.php">
    <!-- Global settings -->
    <?php 
    foreach ( $my_settings["GLOBAL"] as $key => $values ){
        // echo "???? $key => $values";
        echo checkbox("GLOBAL", $key, $key, $values);
    }
    ?>
    <h2>Existing</h2>
        <ul>
        <?php
        foreach ($my_settings as $key => $values) {
            if(substr( $key, 0, 2 ) === "PL") {
                echo "<li>".edit_config_group($key, $values["PLAYLIST_NAME"], $values["PREFIX"])."</li>";
            }
        }
        ?>
        </ul>
        <input id="submit_button" name="submit" type="submit" class="buttons" value="update config">
    </form>
    <h2>New</h2>
    <form method="post" action="http://<?php echo $_SERVER['SERVER_NAME']?>/plugin.php?plugin=<?echo $pluginName;?>&page=pages/setup.php&opt=new">
        <ul>
        <?php
            echo "<li>".edit_config_group(0, "new", "new_prefix")."</li>";
        ?>
        </ul>
    <input id="submit_button" name="submit" type="submit" class="buttons" value="create new">
    </form>


    <h2></h2>
    <form method="post" action="http://<?php echo $_SERVER['SERVER_NAME']?>/plugin.php?plugin=<?echo $pluginName;?>&page=generate.php">
    <input id="submit_button" name="submit" type="submit" class="buttons" value="Generate playlist(s)!">
    </form>


    <p>To report a bug, please file it against the sms Control plugin project on Git: <?php echo $gitURL;?>

    </fieldset>
</div>