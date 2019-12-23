<?php

/**
 * add menu to admin area.
 *
 * @since    1.0.0
 */
function JAW_admin_ctrl_fragment_caching() {
    echo "Constructing";
}



/**
 * add menu to admin area.
 *
 * @since    1.0.0
 */
function jaw_add_AdminMenu() {

    //add parent menu element to admin
    add_menu_page('Jawlatte', // $page_title
                'Jawlatte', // $menu_title
                'manage_options', // $capability
                'Jawlatte_admin_options', // $menu_slug
                null, // $function
                null, // $icon_url
                null                                 // $position
      );

     // remove paren slug 
     remove_submenu_page('Jawlatte_admin_options', //$menu_slug 
                         'Jawlatte_admin_options'  //$submenu_slug 
     );
   

// add children menu elements to parent
    add_submenu_page('Jawlatte_admin_options', // $parent_slug
            'Fragment caching', // $page_title
            'Fragment caching', // $menu_title
            'manage_options', // $capability
            'jaw_admin_ctrl_fragment_caching', // $menu_slug
            'JAW_admin_ctrl_fragment_caching'     // $function
    );
}

add_action('admin_menu', 'jaw_add_AdminMenu');
