<?php
//////////////////////////////
// The Hosting Tool
// Admin Area - Home
// By Jonny H & Jimmie L
// Released under the GNU-GPL
//////////////////////////////

//Check if called by script
if(THT != 1){die();}

class page {
        
        public function curl_get_content($url="http://thehostingtool.com/updates/version.txt"){  
         $ch = curl_init();
         curl_setopt($ch,CURLOPT_URL, $url);
         curl_setopt($ch,CURLOPT_FRESH_CONNECT,TRUE);
         curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,5);
         curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
         curl_setopt($ch,CURLOPT_REFERER,'TheHostingTool Admin Area');
         curl_setopt($ch,CURLOPT_TIMEOUT,10);
         $html=curl_exec($ch);
         if($html==false){
            $m=curl_error(($ch));
            error_log($m);
         }
         curl_close($ch);
         return $html;
    }

    public function rm_full_dir($dir){
           foreach(glob($dir.'/*') as $file) {
            if(is_file($file)) {
             unlink($file);
            }
           }

           if(rmdir($dir)){
            return true;
           }else{
            return false;
           }
    }

    public function remove_install(){
            $sql_dir = "../install/includes/sql";
            $install_tpl = "../install/includes/tpl";
            $install_includes_dir = "../install/includes";
            $install_dir = "../install";

            if(is_dir($sql_dir)){
              if(!$this->rm_full_dir($sql_dir)){
               return false;
              }
            }

            if(is_dir($install_tpl)){
              if(!$this->rm_full_dir($install_tpl)){
               return false;
              }
            }

            if(is_dir($install_includes_dir)){
              if(!$this->rm_full_dir($install_includes_dir)){
               return false;
              }
            }
            
            if(is_dir($install_dir)){
              if(!$this->rm_full_dir($install_dir)){
               return false;
              }
            }

            return true;

    }
    
    public function checkDir($dir){
        if($this->remove_install()){
          return "";
        }else{
          return "<div class='warn'><img src='../themes/icons/cross.png' alt='' /> Warning: Please remove the directory /install.</div>";
        }
   }
        
        public function checkPerms($file){
                if (is_writable($file)) {
                        if(chmod($file, 0644)){
                                return "";
                        }else{
                                return "<div class='warn'><img src='../themes/icons/error.png' alt='' /> Warning: Configuration file (conf.inc.php) is still writable, please chmod it to 644!</div>";
                        }
                }else{
                        return "";
                }
        }
    
        public function content() { # Displays the page 
                global $db;
                global $main;
                global $style;
                global $page;
                /*
                 * Updates System by Jimmie Lin
                 */
                $current_version = rtrim($this->curl_get_content('http://thehostingtool.com/updates/version.txt')); #Clears the end whitespace. ARGHHH
                $running_version = $main->cleanwip($db->config('version')); 
                $install_check = $this->checkDir(LINK ."../install/");
                $conf_check = $this->checkPerms(LINK ."conf.inc.php");
                if($current_version == $running_version){
                        $updatemsg = "<span style='color:green'>Up-To-Date</span>";
                        $upgrademsg = "";
                }
                elseif($current_version > $running_version){
                        $updatemsg = "<span style='color:red'>Upgrade Avaliable</span>";
                    $upgrademsg = "<div class='warn'><img src='../themes/icons/error.png' alt='' /> There is a new version (v$current_version) avaliable! Please download and upgrade!</div>";
                }
                elseif($current_version < $running_version){
                        $updatemsg = "<span style='color:green'>Dev Area Mode</span>";
                        $upgrademsg = "";
                }
                else{
                        $updatemsg = "<span style='color:green'>Up-To-Date</span>";
                        $upgrademsg = "";
                }
                unset($current_version);
                unset($running_version);
                $stats['VERSION'] = $db->config('version');
                $stats['THEME'] = $db->config('theme');
                $stats['CENABLED'] = $main->cleaninteger($db->config('cenabled'));
                $stats['SVID'] = $main->cleaninteger($db->config('show_version_id'));
                $stats['SENABLED'] = $main->cleaninteger($db->config('senabled'));
                $stats['DEFAULT'] = $db->config('default');
                $stats['EMETHOD'] = $db->config('emailmethod');
                $stats['SIGNENABLE'] = $main->cleaninteger($db->config('general'));
                $stats['MULTI'] = $main->cleaninteger($db->config('multiple'));
                $stats['UPDATE'] = $updatemsg;
                $stats['UPG_BOX'] = $upgrademsg;
                $stats_box = $style->replaceVar('tpl/dashboard/stats.tpl', $stats);
                $content = '<strong>Welcome to your Admin Dashboard!</strong><br />Welcome to the dashboard of your Admin Control Panel. In this area you can do the tasks that you need to complete such as manage servers, create packages, manage users.<br />
                Here, you can also change the look and feel of your THT Installation. If you require any help, be sure to ask at the <a href="http://thehostingtool.com/forum" title="THT Community is the official stop for THT Support, THT Modules, Developer Center and more! Visit our growing community now!" class="tooltip">THT Community</a><br /><br />'.$stats_box.'<br />'.$install_check.$conf_check.'</div></div>';
                echo $content;
                if($_POST) {
                        foreach($main->postvar as $key => $value) {
                                if($value == "" && !$n) {
                                        $main->errors("Please fill in all the fields!");
                                        $n++;
                                }
                        }
                        if(!$n) {
                                foreach($_POST as $key => $value) {
                                        // We can use $_POST here because updateResource cleans it.
                                        $db->updateResource($key, $value);
                                }
                                $main->errors("Settings Updated!");
                                $main->done();
                        }
                }
                $array['NOTEPAD'] = $db->resources('admin_notes');
                $content_notepad = $style->replaceVar('tpl/notepad.tpl', $array);
                echo '<br />'; //br it, br it
                echo $main->table('Admin Notepad', $content_notepad, 'auto', 'auto');
                require_once(LINK.'rss/rss_fetch.inc');
                $url = "http://thehostingtool.com/forum/syndication.php?fid=2&limit=3";
                $rss = fetch_rss($url);
                $news = $main->sub("<strong>Add the THT RSS Feed!</strong>", '<a href="http://thehostingtool.com/forum/syndication.php?fid=2" target="_blank" class="tooltip" title="Add the THT RSS Feed!"><img src="<URL>themes/icons/feed.png" /></a>');
                foreach ($rss->items as $item) {
                        $array['title'] = $item['title'];
                        $array['author'] = $item['author'];
                        $array['link'] = $item['link'];
                        $array['TIME'] = $main->convertdate("n/d/Y", $item['date_timestamp']);
                        $array['SUMMARY'] = $item['summary'];
                        $news .= $style->replaceVar('tpl/newsitem.tpl', $array);
                }
                echo "<br />";
                echo $main->table('THT News & Updates', $news);
        }
}

?>
