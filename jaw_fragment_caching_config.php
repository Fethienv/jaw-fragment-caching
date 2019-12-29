<?php
if (!defined('ABSPATH'))    exit; // Exit if accessed directly
/* 
 * 
 */

define('JAW_TOKEN', 'jaw');

define('JAW_TEXT_DOMAIN', 'jaw-fragment-caching');

define('JAW_VERSION', '1.0.0');

define('JAW_SERVER_URL', 'https://www.jawlatte.com');

//define('JAW_ROOT', 'https://www.jawlatte.com');

global $wpdb,$table_prefix;
if (!defined('JAW_Dependencies') || !JAW_Dependencies) {
    add_action('admin_notices', 'jaw_inactive_notice');
    define('FRAGMENT_CACHING_STATUS', FALSE); // repaire this
} else{
    $CNF_STATUS = $wpdb->get_var('SELECT DISTINCT option_value FROM '.$table_prefix.'fragment_caching WHERE option_name = "status"');
    define('FRAGMENT_CACHING_STATUS', $CNF_STATUS); // repaire this
}

define('FRAGMENT_CACHING_DATA_EXPIRATION', FALSE);

include('global_config.php');

// used for security
$unique_sufix = $wpdb->get_var('SELECT DISTINCT option_value FROM '.$table_prefix.'fragment_caching WHERE option_name = "unique_sufix"');

define('FRAGMENT_DIR', ABSPATH . $FRAGMENT_DIR.'jawc-fragments-caching_'.$unique_sufix.'/');
define('FRAGMENT_FILE_PREFFIX', $FRAGMENT_FILE_PREFFIX);

$FRAGMENT_DURATION = $wpdb->get_var('SELECT DISTINCT option_value FROM '.$table_prefix.'fragment_caching WHERE option_name = "FRAGMENT_DURATION"');
define('FRAGMENT_DURATION', $FRAGMENT_DURATION);

//$jaw_fragments_apikey = $wpdb->get_var('SELECT DISTINCT option_value FROM '.$table_prefix.'fragment_caching WHERE option_name = "jaw_fragments_apikey"');

/// EXPIRATION table
$EXPIRATION_constants = array(
                               'JAW_RARLY'         => $wpdb->get_var('SELECT DISTINCT option_value FROM '.$table_prefix.'fragment_caching WHERE option_name = "JAW_RARLY"'),  // get duration from data base
                               'JAW_PERSISTANT'    => 0,
                               'JAW_SPECIFIC_1'    => $wpdb->get_var('SELECT DISTINCT option_value FROM '.$table_prefix.'fragment_caching WHERE option_name = "JAW_SPECIFIC_1"'),
                               'JAW_SPECIFIC_2'    => $wpdb->get_var('SELECT DISTINCT option_value FROM '.$table_prefix.'fragment_caching WHERE option_name = "JAW_SPECIFIC_2"'),
                               'JAW_SPECIFIC_3'    => $wpdb->get_var('SELECT DISTINCT option_value FROM '.$table_prefix.'fragment_caching WHERE option_name = "JAW_SPECIFIC_3"'),
                             );

    // you can add more cookies names here or by filter hook above
     $jaw_gpdr_cookies_names = array('cookie_notice_accepted');

     /**
     * add more gpdr cookies names
     *
     * @since 1.0.0
     *
     * @param array $jaw_gpdr_cookie_name List of $jaw gpdr cookies names
     */
    $jaw_gpdr_cookies_names = apply_filters('jaw_gpdr_cookie_names', $jaw_gpdr_cookies_names);
    
    //select first defined cookie_name
    foreach ($jaw_gpdr_cookies_names as $jaw_gpdr_cookie_name){
        if(isset($_COOKIE[$jaw_gpdr_cookie_name])){
            define('jaw_gpdr_cookie_name',$jaw_gpdr_cookie_name);
            break;
        } 
    }
