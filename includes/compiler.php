<?php
//////////////////////////////
// The Hosting Tool
// Compiler
// By Jonny H
// Released under the GNU-GPL
//////////////////////////////
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);

// Define the main THT
define("THT", 1);

date_default_timezone_set("GMT");

$path = dirname($_SERVER['PHP_SELF']);
$position = strrpos($path,'/') + 1;
define("FOLDER", substr($path,$position)); # Add current folder name to global

if(!($_GET['page'] == 'invoices' && FOLDER == "client")){
//As this prevents PayPal from using the site, disable this script when PayPal might be trying to get through.

// Helps prevent against CSRF attacks and PayPal execution.
require_once("csrf-magic.php");
}

// We don't want this to be called directly.
$compile = explode("/", $_SERVER["SCRIPT_FILENAME"]);
if($compile[count($compile)-1] == "compiler.php") {
        die("Please do not call \"compiler.php\" directly.");
}

#Page generated
$starttime = explode(' ', microtime());
$starttime = $starttime[1] + $starttime[0];

#Start us up
if(CRON != 1) {
        session_start();
}

#Stop the output
ob_start();

#Check for Dependencies
$d = checkForDependencies();
if($d !== true) {
        die((string)$d);
}

#Check PHP Version
$version = explode(".", phpversion());

//Grab DB First
require LINK."/class_db.php"; # Get the file
if(file_exists(LINK."/conf.inc.php")) {
        include LINK."/conf.inc.php"; # Get the config
        define("NOCONFIG", false);
}
else {
        define("NOCONFIG", true);
}
if($sql['install']) {
        define("INSTALL", 1);
        $db = new db; # Create the class
        global $db; # Globalise it
}

$folder = LINK;
if ($handle = opendir($folder)) { # Open the folder
        while (false !== ($file = readdir($handle))) { # Read the files
                if($file != "." && $file != "..") { # Check aren't these names
                        $base = explode(".", $file); # Explode the file name, for checking
                        if($base[1] == "php") { # Is it a php?
                                $base2 = explode("_", $base[0]);
                                if($base2[0] == "class" && $base2[1] != "db") {
                                        require $folder."/".$file; # Get the file
                                        ${$base2[1]} = new $base2[1]; # Create the class
                                        global ${$base2[1]}; # Globalise it
                                }
                        }
                }
        }
}
closedir($handle); #Close the folder


//Define the Admin directory
if(!defined("ADMINDIR")){
$admin_dir = find_admin_dir("../");
define("ADMINDIR", $admin_dir);
if(INSTALL == 1) {
$db->query("UPDATE <PRE>navbar SET link = '".ADMINDIR."' WHERE visual = 'Admin Area' LIMIT 1"); //Update the NavBar admin directory
}
}

if(INSTALL == 1) {
////////////////////
//AKISMET

//Akismet requires a special initialization.  Putting it here will aid in future use of Akismet.  (Ex. Contact forms and the like.)

$akismetkey = $db->config('akismetkey');
if($db->config('useakismet') == "1" && $akismetkey){
//Let's keep the URL consistent.
if($_SERVER['SERVER_PORT'] == "443"){
 $akismeturl = "https://".$_SERVER['HTTP_HOST'];
}else{
 $akismeturl = "http://".$_SERVER['HTTP_HOST'];
}

include(LINK."Akismet/Akismet.php");
$akismet = new Akismet($akismeturl, $akismetkey);
global $akismet;
}

//END AKISMET
///////////////////

        define("THEME", $db->config("theme")); # Set the default theme
        // Sets the URL THT is located at
        if($_SERVER["HTTPS"]) {
                // HTTPS support
                define("URL", str_replace("http://", "https://", $db->config("url")));
        }
        else {
                define("URL", $db->config("url"));
        }
        define("NAME", $db->config("name")); # Sets the name of the website
}
// Converts the $_POST global array into $main->postvar - DB Friendly.
if(isset($_POST)) {
        foreach($_POST as $key => $value) {
                if(INSTALL == 1) {
                        $main->postvar[$key] = $db->strip($value);
                }
                else {
                        $main->postvar[$key] = $value;
                }
        }
}
// Converts the $_GET global array into $main->getvar - DB Friendly.
if(isset($_GET)) {
        foreach($_GET as $key => $value) {
                if(INSTALL == 1) {
                        $main->getvar[$key] = $db->strip($value);
                }
                else {
                        $main->getvar[$key] = $value;        
                }
        }
}
// Converts the $_REQUEST global array into $main->requestvar - DB Friendly.
if(isset($_REQUEST)) {
        foreach($_REQUEST as $key => $value) {
                if(INSTALL == 1) {
                        $main->requestvar[$key] = $db->strip($value);
                }
                else {
                        $main->requestvar[$key] = $value;
                }
        }
}

// Cheap. I know.
if(!is_dir("../includes") && !is_dir("../themes") && !is_dir("../".ADMINDIR)) {
        $check = explode("/", dirname($_SERVER["SCRIPT_NAME"]));
        if($check[count($check)-1] == "install") {
                die("Please change your THT directory's name from something else other than \"install\". Please?");
        }
}

if(FOLDER != "install" && FOLDER != "includes" && INSTALL != 1) { # Are we installing?  
                // Old Method - Uncomment if having trouble installing
        //$error['Error'] = "THT isn't Installed!";
        //$error['What to do'] = "Please run the install script @ <a href='".LINK."../install'>here</a>";
        //die($main->error($error));
        
                // Lets just redirect to the installer, shall we?
        $installURL = LINK . "../install";
        header("Location: $installURL");
}


// Resets the error.
$_SESSION['ecount'] = 0;
$_SESSION['errors'] = 0;

// If payment..
if(FOLDER == "client" && $main->getvar['page'] == "invoices" && $main->getvar['iid'] && $_SESSION['clogged'] == 1) {
        $invoice->pay($main->getvar['iid'], "client/index.php?page=invoices");
        echo "You made it this far.. something went wrong.";
}

function checkForDependencies() {
        // Here, we're going to see if we have the functions that we need. :D
        $needed = array();
        // First things first:
        $version = explode(".", phpversion());
        if($version[0] < 5) {
                die("PHP Version 5 or greater is required! You're currently running: " . phpversion());
        }
        if(!function_exists("curl_init")) {
                $needed[] = "cURL";
        }
        if(!function_exists("mysql_connect")) {
                $needed[] = "MySQL";
        }
        if(count($needed) == 0) {
                return true;
        }
        else {
                $output = "The following function(s) are/is needed for
                TheHostingTool to run properly: <ul>";
                foreach($needed as $key => $value) {
                        $output .= "<li>$value</li>";
                }
                $output .= "</ul>";
                return $output;
        }
}

function find_admin_dir($dir){
 foreach(glob($dir.'/*', GLOB_ONLYDIR) as $dir_search) {
  if(is_file($dir_search."/ADMIN_DIR")){
   $admindir = str_replace($dir."/", "", $dir_search);
   return $admindir;
  }
 }
}
        
?>
