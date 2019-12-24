<?php

/* 
 * 
 */

define('JAW_TOKEN', 'jaw');

define('JAW_TEXT_DOMAIN', 'jaw-fragment-caching');

define('JAW_VERSION', '1.0.0');

define('JAW_SERVER_URL', 'https://www.jawlatte.com');

global $wpdb,$table_prefix;
if (!defined('JAW_Dependencies') || !JAW_Dependencies) {
    add_action('admin_notices', 'jaw_inactive_notice');
    define('FRAGMENT_CACHING_STATUS', FALSE); // repaire this
} else{
    $CNF_STATUS = $wpdb->get_var('SELECT DISTINCT option_value FROM '.$table_prefix.'fragment_caching WHERE option_name = "status"');
    define('FRAGMENT_CACHING_STATUS', $CNF_STATUS); // repaire this
}

define('FRAGMENT_DIR', ABSPATH . 'wp-content/cache/jawc-fragments-caching/');

define('FRAGMENT_CACHING_DATA_EXPIRATION', FALSE);
define('FRAGMENT_DURATION', FALSE);

$unique_sufix = "azerty"; // used for security


/// EXPIRATION table
$EXPIRATION_constants = array(
                               'JAW_RARLY'         => $wpdb->get_var('SELECT DISTINCT option_value FROM '.$table_prefix.'fragment_caching WHERE option_name = "JAW_RARLY"'),  // get duration from data base
                               'JAW_PERSISTANT'    => 0,
                               'JAW_SPECIFIC_1'    => $wpdb->get_var('SELECT DISTINCT option_value FROM '.$table_prefix.'fragment_caching WHERE option_name = "JAW_SPECIFIC_1"'),
                               'JAW_SPECIFIC_2'    => $wpdb->get_var('SELECT DISTINCT option_value FROM '.$table_prefix.'fragment_caching WHERE option_name = "JAW_SPECIFIC_2"'),
                               'JAW_SPECIFIC_3'    => $wpdb->get_var('SELECT DISTINCT option_value FROM '.$table_prefix.'fragment_caching WHERE option_name = "JAW_SPECIFIC_3"'),
                             );
