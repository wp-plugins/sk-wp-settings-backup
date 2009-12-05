<?php
/*
Plugin Name: SK WP Settings Backup
Plugin URI: 
Description: Imports and exports plugin settings
Author: Skpstorm
Version: 1.0
Author URI: http://www.skipstorm.org
*/


class SK_WP_Settings_Backup{
    
	var $recognizedPlugins;
	var $errorMessage;
	var $message;
	
     /**
     * PHP5 constructor
     */
    function __construct() {
        $this->SK_WP_Settings_Backup();
    }

    function SK_WP_Settings_Backup(){	    
	    // Pannello di amministrazione
	    add_action('admin_menu', array(&$this, 'admin_menu'));
            
	}

	
	/* Admin menu */
    function admin_menu(){
        add_submenu_page('tools.php', 'SK WP Settings Backup', 'SK WP Settings Backup', 9,  basename(__FILE__), array(&$this, 'get_post_data'));
    }
    
    function settingsListOptions(){
        $return = '';
        foreach($this->settingsList() as $key => $val){
            if(!empty($this->recognizedPlugins[$key]['name']))
                $return .= '<option value="'.$key.'" style="font-weight:bold; background:#99ff99;">'.$this->recognizedPlugins[$key]['name'].'</option>';
            else
                $return .= '<option value="'.$key.'">'.$key.'</option>';
        }
        return $return;
    }

    function settingsList(){
        if(!isset($this->settingsList)){
            $wpOpt = array('_transient_random_seed', 'siteurl', 'blogname', 'blogdescription', 'users_can_register', 'admin_email', 'start_of_week', 'use_balanceTags',
            'use_smilies', 'require_name_email', 'comments_notify', 'posts_per_rss', 'rss_excerpt_length', 'rss_use_excerpt', 'mailserver_url', 'mailserver_login',
            'mailserver_pass', 'mailserver_port', 'default_category', 'default_comment_status', 'default_ping_status', 'default_pingback_flag', 'default_post_edit_rows',
            'posts_per_page', 'date_format', 'time_format', 'links_updated_date_format', 'links_recently_updated_prepend', 'links_recently_updated_append',
            'links_recently_updated_time', 'comment_moderation', 'moderation_notify', 'permalink_structure', 'gzipcompression', 'hack_file', 'blog_charset',
            'active_plugins', 'home', 'category_base', 'ping_sites', 'advanced_edit', 'comment_max_links', 'gmt_offset', 'default_email_category',
            'use_linksupdate', 'template', 'stylesheet', 'comment_whitelist', 'comment_registration', 'rss_language', 'html_type', 'use_trackback',
            'default_role', 'db_version', 'uploads_use_yearmonth_folders', 'upload_path', 'secret', 'blog_public', 'default_link_category', 'show_on_front',
            'tag_base', 'show_avatars', 'avatar_rating', 'upload_url_path', 'thumbnail_size_w', 'thumbnail_size_h', 'thumbnail_crop',
            'medium_size_w', 'medium_size_h', 'avatar_default', 'enable_app', 'enable_xmlrpc', 'large_size_w', 'large_size_h', 'image_default_link_type',
            'image_default_size', 'image_default_align', 'close_comments_for_old_posts', 'close_comments_days_old', 'thread_comments', 'thread_comments_depth',
            'page_comments', 'comments_per_page', 'default_comments_page', 'comment_order', 'sticky_posts', 'timezone_string', 'wp_user_roles', '_transient_rewrite_rules',
            'cron', '_transient_doing_cron', '_transient_update_core', '_transient_update_plugins', '_transient_update_themes', 'auth_salt', 'logged_in_salt',
            'dashboard_widget_options', 'nonce_salt', 'current_theme', 'can_compress_scripts', 'category_children');

            $this->settingsList = array();

            foreach(wp_load_alloptions() as $key => $val){
                if(!in_array($key, $wpOpt) && false !== unserialize($val)){
                    $foo = $key.'_getData';
                    /* Avoid displaying more lines for the same plugin when
                     * it has more options keys
                     *
                     * if so return the correct data only from the function
                     * yourPluginOptionKey_getData()
                     * and false from
                     * yourPluginOtherOptionKey_getData()
                     */
                    if(function_exists($foo) && $foo()){
                        $this->recognizedPlugins[$key] = $foo();
                        $this->settingsList[$key] = true;
                        }
                    else if(!function_exists($foo))
                        $this->settingsList[$key] = $val;
                }
            }
        }
        return $this->settingsList;
    }
    
    function get_post_data(){
        if ('POST' == $_SERVER['REQUEST_METHOD']) {
            $this->errorMessage = '';
            $this->save_settings();
	}
				
        include(dirname(__FILE__).'/admin_options.php');
    }

    /**
     * SKWPSB_IN_SUBMIT: Save settings into plugin options
     * SKWPSB_EX_SUBMIT: Get settings from plugin options
     */
    function save_settings(){
        if(isset($_POST['SKWPSB_IN_SUBMIT'])){
            $this->message = '';
            foreach($this->settingsList() as $key => $val){
                if($_POST['SKWPSB_IN_PLUGIN'] == $key){
                    if(isset($this->recognizedPlugins[$key])){
                        // Recognized plugin
                        $foo = $this->recognizedPlugins[$key]['set_options'];
                        if(function_exists($foo)){
                            if($foo(stripslashes($_POST['SKWPSB_IN_CODE']))){
                                $this->message = 'Settings successfully imported.';
                            } else {
                                $this->errorMessage = 'The plugin returned an error, make sure you copied the code correctly.';
                            }
                            return;
                        } else {
                            $this->errorMessage = 'The selected plugin is not responding correctly.';
                        }   return;
                    } else {
                        // Standard
                        @$data = unserialize(stripslashes($_POST['SKWPSB_IN_CODE']));
                        if(!$data || empty($data)){
                            $this->errorMessage = 'Wrong code, make sure you copied it correctly.'.$_POST['SKWPSB_IN_CODE'];
                            return;
                        } else {
                            update_option($key, $data);
                            $this->message = 'Settings successfully imported.';
                            return;
                        }
                    }
                }
            }
            $this->errorMessage = 'You might not have selected any destination plugin.';
        }
        else if(isset($_POST['SKWPSB_EX_SUBMIT'])){
            foreach($this->settingsList() as $key => $val){
                if($_POST['SKWPSB_EX_PLUGIN'] == $key){
                    if(isset($this->recognizedPlugins[$key])){
                        // Recognized plugin
                        $foo = $this->recognizedPlugins[$key]['get_options'];
                        if(function_exists($foo)){
                            $this->exportedSettings = $foo();
                            $this->message = '<p>Use the code to import the settings to another blog.</p><p>Copy the code as it is, don\'t add spaces or newlines.</p>';
                            return;
                        } else {
                            $this->errorMessage = 'The selected plugin is not responding correctly.';
                        }   return;
                    } else {
                        // Standard
                        $this->exportedSettings = $val;
                        $this->message = '<p>Use the code to import the settings to another blog.</p><p>Copy the code as it is, don\'t add spaces or newlines.</p>';
                        return;
                    }
                }
            }
            $this->errorMessage = 'You might not have selected any source plugin.';
        }
    }
}


$SK_WP_Settings_Backup = new SK_WP_Settings_Backup();
?>