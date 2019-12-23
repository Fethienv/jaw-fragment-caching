<?php

/**
 * Plugin Name: Jaw - fragment caching
 * Plugin URI: https://www.jawlatte.com
 * Description: Special Customizations for Jawlatte website
 * Author: Jawlatte
 * Version: 1.0.0
 * Author URI: https://www.jawlatte.com
 *
 * Text Domain: jaw-fragment-caching
 * Domain Path: /lang/
 *
 * WC requires at least: 3.0.0
 * WC tested up to: 3.7.0
 *
 */
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly



require_once 'jaw_fragment_caching_dependencies.php';
require_once 'jaw_fragment_caching_config.php';

if (!defined('JAW_TOKEN'))
    exit;
if (!defined('JAW_TEXT_DOMAIN'))
    exit;

include 'plugin_registeration.php';
include 'jaw_core_functions.php';
if (is_admin()) {
    include 'jaw_admin_functions.php';
}
