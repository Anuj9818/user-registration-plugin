<?php
/*
* Plugin Name:       User Information Plugin
* Description:       Used to get Additional User Information after Login / Regitser 
* Version:           1.0
* Requires at least: 5.2
* Requires PHP:      7.2
* Author:            ANUJ SHRESTHA
* License:           GPL v2 or later
* Text Domain:       user-information-plugin
This plugin gives you facility to store additional information about user that wordpress by default does not provide.
*/

if (!function_exists('add_action')) {
  echo "You cannot access the files over here";
  exit;
}

//On Plugin Activation
function urf_user_information_plugin_activation()
{
  urf_user_information_plugin_before_setup(); //Call plugin setup function 
  flush_rewrite_rules(); //When the user activates and deactivates the changes associated are performbed 

}
register_activation_hook(__FILE__, 'urf_user_information_plugin_activation');


//On Plugin Deactication
function urf_user_information_plugin_deactivation()
{

  flush_rewrite_rules(); //Flush Rewrite Rules

}
register_deactivation_hook(__FILE__, 'urf_user_information_plugin_deactivation');

function urf_user_information_plugin_before_setup()
{

  define('URF_PLUGIN_PATH', plugin_dir_path(__FILE__));

  define('URF_PLUGIN_URL', plugin_dir_url(__FILE__));

  /* Backend Templates */

  include(URF_PLUGIN_PATH . '/includes/user-registration.php');

  /* Frontend Templates */

  include(URF_PLUGIN_PATH . '/includes/design-template.php');

}
add_action('plugins_loaded', 'urf_user_information_plugin_before_setup');