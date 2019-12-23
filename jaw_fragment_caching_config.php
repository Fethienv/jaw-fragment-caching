<?php

/* 
 * 
 */

define('JAW_TOKEN', 'jaw');

define('JAW_TEXT_DOMAIN', 'jaw-fragment-caching');

define('JAW_VERSION', '1.0.0');

define('JAW_SERVER_URL', 'https://www.jawlatte.com');


if (!defined('JAW_Dependencies') || !JAW_Dependencies) {
    add_action('admin_notices', 'jaw_inactive_notice');
    define('FRAGMENT_CACHING_STATUS', FALSE); // repaire this
} else{
    define('FRAGMENT_CACHING_STATUS', TRUE); // repaire this
}

define('FRAGMENT_DIR', ABSPATH . 'wp-content/cache/jawc-fragments-caching/');

define('FRAGMENT_CACHING_DATA_EXPIRATION', FALSE);
define('FRAGMENT_DURATION', FALSE);

/// EXPIRATION table
$EXPIRATION_constants = array(
                               'JAW_RARLY'         => 120,  // get duration from data base
                               'JAW_PERSISTANT'    => 0,
                               'JAW_SPECIFIC_1'    => MONTH_IN_SECONDS,  // get duration from data base
                               'JAW_SPECIFIC_1'    => MONTH_IN_SECONDS,  // get duration from data base
                               'JAW_SPECIFIC_3'    => MONTH_IN_SECONDS   // get duration from data base
                             );

