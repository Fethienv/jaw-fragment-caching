<?php

/**
 * add menu to admin area.
 *
 * @since    1.0.0
 */
function jaw_admin_ctrl_fragment_caching() {
    echo "Constructing";
}



/**
 * add menu to admin area.
 *
 * @since    1.0.0
 */
function jaw_add_AdminMenu() {

// add children menu elements to parent
    add_submenu_page('tools.php', // $parent_slug
            'Fragment caching', // $page_title
            'Fragment caching', // $menu_title
            'manage_options', // $capability
            'jaw_fragment_caching', // $menu_slug
            'jaw_admin_ctrl_fragment_caching',     // $function
            100
    );
}

add_action('admin_menu', 'jaw_add_AdminMenu');
