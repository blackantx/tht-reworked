<?php
//////////////////////////////
// The Hosting Tool
// Install Script
// By Jonny H and Kevin M
// Released under the GNU-GPL
//////////////////////////////


/*
 * Quick little function made to make generating a default site URL
 * easy. Hopefully this will assist alot of support topics regarding
 * bad site URLs, as the automatically generated ones should be correct.
*/
function generateSiteUrl() {
        $url = "";
        if(!empty($_SERVER["HTTPS"])) {
                $url .= "https://";
        }
        else {
                $url .= "http://";
        }
        $url .= $_SERVER["SERVER_NAME"];
        $exploded = explode(basename($_SERVER["PHP_SELF"]), $_SERVER["PHP_SELF"]);
        $url .= dirname($exploded[0]) . "/";
        return $url;
}

//INSTALL GLOBALS
define("CVER", "1.2.3");
define("NVER", "1.3");

define("LINK", "../includes/"); # Set link
include(LINK."compiler.php"); # Get compiler


function writeconfig($host, $user, $pass, $db, $pre, $true) {
        global $style;
        $array['HOST']        =  addcslashes($host, '\\\'');
        $array['USER']        =  addcslashes($user, '\\\'');
        $array['PASS']        =  addcslashes($pass, '\\\'');
        $array['DB']        =  addcslashes($db, '\\\'');
        $array['PRE']        =  addcslashes($pre, '\\\'');
        $array['TRUE']        =  $true;
        $tpl = $style->replaceVar("../install/includes/tpl/conftemp.tpl", $array);
        $link = LINK."conf.inc.php";
        if(is_writable($link)) {
                file_put_contents($link, $tpl);
                return true;
        }
        else {
                return false;
        }
}

define("THEME", "Reloaded2"); # Set the theme
define("URL", "../"); # Set url to blank

define("NAME", "THT");
define("PAGE", "Install");
define("SUB", "Choose Method");

$array['VERSION'] = NVER;
$array['ANYTHING'] = "";
$link = LINK."conf.inc.php";
$disable = false;

if(INSTALL == 1 || filesize(LINK."conf.inc.php") > 0){
        include(LINK."conf.inc.php");
        if(!writeconfig($sql['host'], $sql['user'], $sql['pass'], $sql['db'], $sql['pre'], "false")) {
                $array['ANYTHING'] = "Your $link isn't writeable or does not exist! Please CHMOD it to 666 and make sure it exists!!";
                $disable = true;
        }
        else {
                $array['ANYTHING'] = "Since you've already ran the installer, your config has been re-written to the \"not installed\" state. If you are upgrading, this is normal!<br><br>
                <center><h3><font color = '#FFFFFF'><span style='background-color: #000000'>BACKUP YOUR DATABASE NOW! - The installer removes the email templates table!</span></font></h3></center><br>";
        }
}
if(!file_exists($link)) {
        if(!fopen($link, "w+")){
        $array["ANYTHING"] = "Your $link file doesn't exist! Please create it as a blank file and CHMOD it to 666!";
        $disable = true;
        }
        chmod($link, 0666);
}
if(!is_writable($link)) {
        if(!chmod($link, 0666)){
        $array["ANYTHING"] = "Your $link isn't writeable! Please CHMOD it to 666!";
        $disable = true;
        }
}
echo $style->get("header.tpl");
if($disable) {
        echo '<script type="text/javascript">$(function(){$(".twobutton").attr("disabled", "true");$("#method").attr("disabled", "true");});</script>';
}
$array["GENERATED_URL"] = generateSiteUrl();
echo $style->replaceVar("../install/includes/tpl/install.tpl", $array);
echo $style->get("footer.tpl");

include(LINK."output.php"); #Output it

?>
