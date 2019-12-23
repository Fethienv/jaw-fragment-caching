<?php

/**
 * add menu to admin area.
 *
 * @since    1.0.0
 */
function jaw_admin_ctrl_fragment_caching() {

    $active_tab = (isset($_GET['tab'])) ? $_GET['tab'] : "options";
    $save_step = (isset($_GET['save']) && intval($_GET['save']) == 1) ? true : false;
    $message = "hi";

    if ($active_tab == "options" && $save_step) {
        $message = $this->save_cache_options();
    } elseif ($active_tab == "daily_revisions" && $save_step) {
        $message = $this->save_daily_revisions();
    }
    ?>
    <!-- Create a header in the default WordPress 'wrap' container -->
    <div class="wrap">

        <div id="icon-themes" class="icon32"></div>
        <h2>Jawlatte faragment Caching</h2>
        <?php
        settings_errors();
        ?>

        <div class="wcfm_setting_help_box">
            <p><?php echo $message; ?></p>
        </div>

        <h2 class="nav-tab-wrapper">
            <a href="?page=jaw_fragment_caching&tab=options" class="nav-tab <?php echo $active_tab == 'options' ? 'nav-tab-active' : ''; ?>">Options</a>        
            <a href="?page=jaw_fragment_caching&tab=fragments" class="nav-tab <?php echo $active_tab == 'fragments' ? 'nav-tab-active' : ''; ?>">Fragments</a>
        </h2>

        <form method="post" action="tools.php?page=jaw_fragment_caching&tab=<?php echo $active_tab ?>&save=1">
            <?php
            if ($active_tab == 'options') {
                settings_fields('fragments_caching_group');
                do_settings_sections('fragments_caching_group');
            } elseif ($active_tab == 'fragments') {

                $cache_fragments_list = array();// to be cachnged
                
                echo '<div class="wrap"><h2>Cache fragments</h2>';
                echo '<table class="wp-list-table widefat fixed striped jawlatte_page_jawc_admin_ctrl_cache"><thead><tr>'
                . '<th scope="col" id="ready_urls_id" class="manage-column column-ready_urls_id column-primary sortable desc">'
                . '<a href="https://www.jawlatte.com/wp-admin/admin.php?page=jawc_admin_ctrl_cache&amp;jawc_page=main&amp;tab=queue&amp;orderby=ready_urls_id&amp;order=asc"><span>#</span><span class="sorting-indicator"></span></a>'
                . '</th>'
                . '<th scope="col" id="ready_urls" class="manage-column column-ready_urls">Transient name</th>'
                . '<th scope="col" id="ready_time" class="manage-column column-ready_time">Action</th>'
                . '</tr>'
                . '</thead>'
                . '<tbody id="the-list">';
                $fragments_count = 0;
                foreach ($cache_fragments_list as $key => $fragments) {
                    echo '
		<tr>
                    <td class="ready_urls_id column-ready_urls_id has-row-actions column-primary" data-colname="ready_urls_id">' . $key . '
                        <button type="button" class="toggle-row">
                            <span class="screen-reader-text">Show more details</span>
                        </button>
                    </td>
                    <td class="ready_urls column-ready_urls" data-colname="ready_urls">
                        ' . $fragments . '
                    </td>
                    <td class="ready_time column-ready_time" data-colname="ready_time">
                        ' . $fragments . '
                    </td>
                    </tr>';
                    $fragments_count++;
                }

                if ($fragments_count == 0) {
                    echo '
		<tr>
                    <td class="ready_urls_id column-ready_urls_id has-row-actions column-primary" data-colname="ready_urls_id">' . $key . '
                        <button type="button" class="toggle-row">
                            <span class="screen-reader-text">Show more details</span>
                        </button>
                    </td>
                    <td class="ready_urls column-ready_urls" data-colname="ready_urls">
                        No cache fragments
                    </td>
                    <td class="ready_time column-ready_time" data-colname="ready_time">
                        
                    </td>
                    </tr>';
                }
                echo '</tbody><tfoot><tr>'
                . '<th scope="col" id="ready_urls_id" class="manage-column column-ready_urls_id column-primary sortable desc">'
                . '<a href="https://www.jawlatte.com/wp-admin/admin.php?page=jawc_admin_ctrl_cache&amp;jawc_page=main&amp;tab=queue&amp;orderby=ready_urls_id&amp;order=asc"><span>#</span><span class="sorting-indicator"></span></a></th>'
                . '<th scope="col" id="ready_urls" class="manage-column column-ready_urls">Transient name</th>'
                . '<th scope="col" id="ready_time" class="manage-column column-ready_time">Action</th>'
                . '</tr>'
                . '</tfoot></table> </div>';

               
            } // end if/else
             submit_button();
            ?>

        </form>

    </div><!-- /.wrap -->

    <?php
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
            'jaw_admin_ctrl_fragment_caching', // $function
    );
}

add_action('admin_menu', 'jaw_add_AdminMenu');



/**
 * custom option and settings:
 * callback functions
 */
function cache_options_section_text($args) {

    ?>

    <?php
}

function cache_options_text_field($args) {
    $value = '';//get_option(esc_attr('_' . $args['name']) . '_');
    echo "<input type='text' id='" . esc_attr($args['label_for']) . "' name='" . esc_attr($args['name']) . "' value='" . esc_attr($value) . "' />";
}

function cache_options_checkbox_field($args) {

    //$checked = ($args['checked'] ||  $args['checked'] == 1) ? ' checked="checked" ' : '';
    $value = ($args['checked'] || $args['checked'] == 1) ? 1 : 0;

    $html = "<input type='checkbox' id='" . esc_attr($args['label_for']) . "' name='" . esc_attr($args['name']) . "' value='1' " . checked(1, $args['checked'], false) . " />";
    $html .= "<label for='" . esc_attr($args['label_for']) . "'>" . esc_attr($args['label']) . "</label>";
    echo $html;
}

function register_fragments_caching_fields() {

    // register a new setting 
    register_setting('fragments_caching_group', 'fragments_caching_options');

    // register a new section in the "wcfm" page
    add_settings_section(
            'fragments_caching_section',
            'Fragments caching options',
            'cache_options_section_text',
            'fragments_caching_group'
    );
    
    add_settings_field(
            'fragments_caching_status',
            'Clean cache for users ',
            'cache_options_checkbox_field',
            'cache_options_group',
            'cache_options_section',
            [
                'label_for' => 'fragments_caching_status',
                'name' => 'fragments_caching_status',
                'label' => 'Check for enable',
                'checked' => get_option('_cache_options_cleanup_users_cache_'),
    ]);

    add_settings_field(
            'fragments_caching_rarly_expiration',
            'rarly updated cache expiration',
            'cache_options_text_field',
            'fragments_caching_group',
            'fragments_caching_section',
            [
                'label_for' => 'fragments_caching_rarly_expiration',
                'name'      => 'fragments_caching_rarly_expiration',
    ]);
    
    add_settings_field(
            'fragments_caching_specific_1_expiration',
            'Specific 1 updated cache expiration',
            'cache_options_text_field',
            'fragments_caching_group',
            'fragments_caching_section',
            [
                'label_for' => 'fragments_caching_specific_1_expiration',
                'name'      => 'fragments_caching_specific_1_expiration',
    ]);
    
    add_settings_field(
            'fragments_caching_specific_2_expiration',
            'Specific 2 updated cache expiration',
            'cache_options_text_field',
            'fragments_caching_group',
            'fragments_caching_section',
            [
                'label_for' => 'fragments_caching_specific_2_expiration',
                'name'      => 'fragments_caching_specific_2_expiration',
    ]);
    
    add_settings_field(
            'fragments_caching_specific_3_expiration',
            'Specific 3 updated cache expiration',
            'cache_options_text_field',
            'fragments_caching_group',
            'fragments_caching_section',
            [
                'label_for' => 'fragments_caching_specific_3_expiration',
                'name'      => 'fragments_caching_specific_3_expiration',
    ]);


}

if (strpos($_SERVER['REQUEST_URI'], 'tools.php?page=jaw_fragment_caching&tab=options') !== false ||
        strpos($_SERVER['REQUEST_URI'], 'tools.php?page=tab=options&jaw_fragment_caching') !== false) {
   add_action('admin_init', 'register_fragments_caching_fields');
}
