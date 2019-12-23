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
define('FRAGMENT_DURATION', TRUE);


/// EXPIRATION table
$EXPIRATION_table = array(
    'memberships_1' => MONTH_IN_SECONDS,
    'memberships_2' => MONTH_IN_SECONDS,
    'memberships_3' => MONTH_IN_SECONDS,
    'footer_1' => MONTH_IN_SECONDS
);

