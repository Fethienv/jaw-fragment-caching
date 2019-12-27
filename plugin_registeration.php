<?php

/*
 * 
 */

function jaw_fragment_caching_install() {
    global $wpdb;

    $charset_collate = $wpdb->get_charset_collate();
    $table_name = $wpdb->prefix . 'fragment_caching';

    $sql = "CREATE TABLE $table_name (
  id int(2) NOT NULL AUTO_INCREMENT,
  option_name varchar(20) DEFAULT '' NOT NULL,
  option_value varchar(40) DEFAULT '' NOT NULL,
  PRIMARY KEY  (id)
) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta($sql);

    add_option('JAW_db_VERSION', JAW_VERSION);
}

function jaw_fragment_caching_install_data() {
    global $wpdb;

    $status            = 1;
    $JAW_RARLY         = 1209600;
    $JAW_SPECIFIC_1    = 43200;
    $JAW_SPECIFIC_2    = 86400;
    $JAW_SPECIFIC_3    = 604800;
    $unique_sufix      = 'cq0ZvzyfBTTGWbTW';
    $FRAGMENT_DURATION = 0;

    $table_name = $wpdb->prefix . 'fragment_caching';

    $wpdb->insert(
            $table_name,
            array(
                'status'            => $status,
                'JAW_RARLY'         => $JAW_RARLY,
                'JAW_SPECIFIC_1'    => $JAW_SPECIFIC_1,
                'JAW_SPECIFIC_2'    => $JAW_SPECIFIC_2,
                'JAW_SPECIFIC_3'    => $JAW_SPECIFIC_3,
                'unique_sufix'      => $unique_sufix,
                'FRAGMENT_DURATION' => $FRAGMENT_DURATION
            )
    );
}

// Activation Hooks
register_activation_hook(__FILE__, 'jaw_fragment_caching_install');
register_activation_hook(__FILE__, 'jaw_fragment_caching_install_data');

// Deactivation Hooks
// register_deactivation_hook(__FILE__, array('JAWC', 'deactivate_jawc'));
