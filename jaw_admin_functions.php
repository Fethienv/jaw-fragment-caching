<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
/**
 * add menu to admin area.
 *
 * @since    1.0.0
 */
// you may want to wrap add_action() in a conditional to prevent enqueue on every page


if (strpos($_SERVER['REQUEST_URI'], 'tools.php?page=jaw_fragment_caching&tab=fragments') !== false ||
        strpos($_SERVER['REQUEST_URI'], 'tools.php?tab=fragments&page=jaw_fragment_caching') !== false) {
    add_action('admin_enqueue_scripts', 'file_manager_admin_enqueue');
}

function file_manager_admin_enqueue() {
    wp_enqueue_style(
            'file_manager',
            plugins_url('jaw-fragment-caching/assets/css/file_manager.css'), // you probably want to use plugins_url() for this
    );
    wp_enqueue_script(
            'file_manager',
            plugins_url('jaw-fragment-caching/assets/js/file_manager.js'), // you probably want to use plugins_url() for this
    );
    /* wp_enqueue_script(
      'fdataTables_ile_manager',
      'https://pagination.js.org/dist/2.1.5/custom-paginationjs.css', // you probably want to use plugins_url() for this
      ); */
    wp_enqueue_script(
            'pagination_file_manager',
            'https://pagination.js.org/dist/2.1.5/pagination.min.js', // you probably want to use plugins_url() for this
    );
}

function jaw_admin_ctrl_fragment_caching_main() {

    $active_tab = (isset($_GET['tab']) && !empty($_GET['tab'])) ? $_GET['tab'] : "options";
    $save_step = (isset($_GET['save']) && intval($_GET['save']) == 1) ? true : false;
    $message = "hi";

    if ($active_tab == "options" && $save_step) {
        $message = fragment_caching_options();
    }
    ?>
    <!-- Create a header in the default WordPress 'wrap' container -->
    <div class="wrap">

        <div id="icon-themes" class="icon32"></div>
        <h2>Jawlatte faragment Caching</h2>
    <?php
    settings_errors();
    ?>

        <div class="jaw_message_box jaw_init" id='jaw_message'>
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
        include_once 'addons/file-manager/file-manager.php';
    }
}

function fragment_caching_options() {


    if (!isset($_POST['JAW_RARLY']) || empty($_POST['JAW_RARLY']) ||
            !isset($_POST['JAW_SPECIFIC_1']) || empty($_POST['JAW_SPECIFIC_1']) ||
            !isset($_POST['JAW_SPECIFIC_2']) || empty($_POST['JAW_SPECIFIC_2']) ||
            !isset($_POST['JAW_SPECIFIC_3']) || empty($_POST['JAW_SPECIFIC_3']) ||
            !isset($_POST['unique_sufix']) || empty($_POST['unique_sufix'])) {
        return 'There is an error';
    }

    $post_status = (isset($_POST['fragments_caching_status'])) ? intval($_POST['fragments_caching_status']) : 0;
    $post_duration_log = (isset($_POST['fragments_duration_log'])) ? intval($_POST['fragments_duration_log']) : 0;

    $POST_JAW_RARLY = intval($_POST['JAW_RARLY']);
    $POST_JAW_SPECIFIC_1 = intval($_POST['JAW_SPECIFIC_1']);
    $POST_JAW_SPECIFIC_2 = intval($_POST['JAW_SPECIFIC_2']);
    $POST_JAW_SPECIFIC_3 = intval($_POST['JAW_SPECIFIC_3']);
    $POST_unique_sufix = $_POST['unique_sufix'];

    $status = jaw_update_options(['option_value' => $post_status], ['option_name' => 'status']);
    $duration_log = jaw_update_options(['option_value' => $post_duration_log], ['option_name' => 'FRAGMENT_DURATION']);
    $JAW_RARLY = jaw_update_options(['option_value' => $POST_JAW_RARLY], ['option_name' => 'JAW_RARLY']);
    $JAW_SPECIFIC_1 = jaw_update_options(['option_value' => $POST_JAW_SPECIFIC_1], ['option_name' => 'JAW_SPECIFIC_1']);
    $JAW_SPECIFIC_2 = jaw_update_options(['option_value' => $POST_JAW_SPECIFIC_2], ['option_name' => 'JAW_SPECIFIC_2']);
    $JAW_SPECIFIC_3 = jaw_update_options(['option_value' => $POST_JAW_SPECIFIC_3], ['option_name' => 'JAW_SPECIFIC_3']);
    $unique_sufix = jaw_update_options(['option_value' => $POST_unique_sufix], ['option_name' => 'unique_sufix']);

    if (false === $status ||
            false === $duration_log ||
            false === $JAW_RARLY ||
            false === $JAW_SPECIFIC_1 ||
            false === $JAW_SPECIFIC_2 ||
            false === $JAW_SPECIFIC_3 ||
            false === $unique_sufix) {
        return "There was an error.";
    } else {
        return " Options updates.";
    }
}

function jaw_update_options($data, $where = NULL, $format = NULL, $where_format = NULL) {
    global $wpdb, $table_prefix;
    $table = $table_prefix . 'fragment_caching';
    return $wpdb->update($table, $data, $where, $format, $where_format);
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
            'jaw_admin_ctrl_fragment_caching_main', // $function
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

            global $wpdb, $table_prefix;
            $value = $wpdb->get_var('SELECT DISTINCT option_value FROM ' . $table_prefix . 'fragment_caching WHERE option_name = "' . esc_attr($args['name']) . '"');
            echo "<input type='text' id='" . esc_attr($args['label_for']) . "' name='" . esc_attr($args['name']) . "' value='" . esc_attr($value) . "' />";
        }

        function cache_options_checkbox_field($args) {
            global $wpdb, $table_prefix;
            $checked = $wpdb->get_var('SELECT DISTINCT option_value FROM ' . $table_prefix . 'fragment_caching WHERE option_name = "' . esc_attr($args['checked']) . '"');

            $html = "<input type='checkbox' id='" . esc_attr($args['label_for']) . "' name='" . esc_attr($args['name']) . "' value='1' " . checked(1, $checked, false) . " />";
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
                    'fragments caching status',
                    'cache_options_checkbox_field',
                    'fragments_caching_group',
                    'fragments_caching_section',
                    [
                        'label_for' => 'fragments_caching_status',
                        'name' => 'fragments_caching_status',
                        'label' => 'Check for enable',
                        'checked' => 'status'
            ]);

            add_settings_field(
                    'fragments_caching_duration_log',
                    'fragments caching duration log',
                    'cache_options_checkbox_field',
                    'fragments_caching_group',
                    'fragments_caching_section',
                    [
                        'label_for' => 'fragments_duration_log',
                        'name' => 'fragments_duration_log',
                        'label' => 'fragments_duration_log',
                        'checked' => 'FRAGMENT_DURATION'
            ]);

            add_settings_field(
                    'fragments_caching_unique_sufix',
                    'Unique suffix',
                    'cache_options_text_field',
                    'fragments_caching_group',
                    'fragments_caching_section',
                    [
                        'label_for' => 'unique_sufix',
                        'name' => 'unique_sufix',
            ]);

            add_settings_field(
                    'fragments_caching_rarly_expiration',
                    'rarly updated cache expiration',
                    'cache_options_text_field',
                    'fragments_caching_group',
                    'fragments_caching_section',
                    [
                        'label_for' => 'JAW_RARLY',
                        'name' => 'JAW_RARLY',
            ]);

            add_settings_field(
                    'fragments_caching_specific_1_expiration',
                    'Specific 1 updated cache expiration',
                    'cache_options_text_field',
                    'fragments_caching_group',
                    'fragments_caching_section',
                    [
                        'label_for' => 'JAW_SPECIFIC_1',
                        'name' => 'JAW_SPECIFIC_1',
            ]);

            add_settings_field(
                    'fragments_caching_specific_2_expiration',
                    'Specific 2 updated cache expiration',
                    'cache_options_text_field',
                    'fragments_caching_group',
                    'fragments_caching_section',
                    [
                        'label_for' => 'JAW_SPECIFIC_2',
                        'name' => 'JAW_SPECIFIC_2',
            ]);

            add_settings_field(
                    'fragments_caching_specific_3_expiration',
                    'Specific 3 updated cache expiration',
                    'cache_options_text_field',
                    'fragments_caching_group',
                    'fragments_caching_section',
                    [
                        'label_for' => 'JAW_SPECIFIC_3',
                        'name' => 'JAW_SPECIFIC_3',
            ]);
        }

        if (strpos($_SERVER['REQUEST_URI'], 'tools.php?page=jaw_fragment_caching&tab=options') !== false ||
                strpos($_SERVER['REQUEST_URI'], 'tools.php?page=jaw_fragment_caching') !== false ||
                strpos($_SERVER['REQUEST_URI'], 'tools.php?tab=options&page=jaw_fragment_caching') !== false) {
            add_action('admin_init', 'register_fragments_caching_fields');
        }
