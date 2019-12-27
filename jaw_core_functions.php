<?php

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

/**
 * load cache using section, refernce and expiration values
 *
 * @since    1.0.0
 * @param    string               $section         section name.
 * @param    string               $refrence        reference id.
 * @param    string               $exp             expiration constant.
 * @param    boolean              $unique          cache type, is same or diferent by user role.
 */
function jaw_get_cache_part($section, $refrence, $exp = "JAW_PERSISTANT", $unique = false) {
    if (FRAGMENT_CACHING_STATUS) {
        $start_time = (FRAGMENT_DURATION) ? microtime(true) : "";
        global $EXPIRATION_constants;
        $exp = $EXPIRATION_constants[$exp];
        $load_fragment_cache = jaw_load_fragment_cache($section, $refrence, $exp, $unique);
        if (FRAGMENT_DURATION) {
            $end_time = microtime(true);
            $execution_time = ($end_time - $start_time);
            $message = 'Execution GET time = ' . $execution_time;
            register_in_log($message, $section . "_" . $refrence);
        }
        return $load_fragment_cache;
    }
    return false;
}

/**
 * create cache using section, refernce and expiration values
 *
 * @since    1.0.0
 * @param    string               $section         section name.
 * @param    string               $refrence        reference id.
 * @param    string               $exp             expiration constant.
 * @param    boolean              $unique          cache type, is same or diferent by user role.
 */
function jaw_set_cache_part($section, $refrence, $exp = "JAW_PERSISTANT", $unique = false) {
    if (FRAGMENT_CACHING_STATUS) {
        $start_time = (FRAGMENT_DURATION) ? microtime(true) : "";
        global $EXPIRATION_constants;
        $exp = $EXPIRATION_constants[$exp];
        $create_fragment_cache = jaw_create_fragment_cache($section, $refrence, $exp, $unique);
        if (FRAGMENT_DURATION) {
            $end_time = microtime(true);
            $execution_time = ($end_time - $start_time);
            $message = 'Execution SET time = ' . $execution_time;
            register_in_log($message, $section . "_" . $refrence);
        }
        return $create_fragment_cache;
    }
    return false;
}

/**
 * Used to start caching
 *
 * @since    1.0.0
 * @param    void               no parameters.
 */
function jaw_start_fragment_caching() {
    if (FRAGMENT_CACHING_STATUS) {
        ob_start();
    }
}

/**
 * Scan fragments dir and get all urls in array
 *
 * @since    1.0.0
 * @param    string               $path             path of fragment or dir to scan.
 */
function scan_fragments_dir($path) {
    $files_list = array();
    $files = array_diff(scandir($path), ['.', '..']);
    foreach ($files as $sub_files) {
        $path = (substr($path, -1) == "/") ? $path : $path . '/';
        if (is_dir($path . '/' . $sub_files)) {
            $fs = scan_dir($path . $sub_files);
            foreach ($fs as $f) {
                $files_list[] = $f;
            }
        } else {
            $files_list[] = $path . $sub_files;
        }
    }
    $files_list[] = $path;
    return $files_list;
}

/**
 * Clean up all fragments in cache dir
 *
 * @since    1.0.0
 * @param    void               no parameters.
 * @fire:    with hooks : 
 *                        switch_theme
 *                        user_register
 *                        profile_update
 *                        deleted_user
 *                        wp_update_nav_menu
 *                        update_option_sidebars_widgets
 *                        update_option_category_base
 *                        update_option_tag_base
 *                        permalink_structure_changed
 *                        create_term
 *                        edited_terms'
 *                        delete_term
 *                        add_link
 *                        edit_link
 *                        delete_link
 *                        customize_save
 *                        update_option_theme_mods_' . get_option( 'stylesheet' )
 *                        upgrader_process_complete                        
 */
function jaw_cleanup_all_fragments() {
    $fragments_urls = scan_fragments_dir(FRAGMENT_DIR);
    $result = [];
    foreach ($fragments_urls as $fragments) {
        if (is_dir($fragments)) {
            $result[] = rmdir($fragments);
        } else {
            $result[] = unlink($fragments);
        }
    }
}

/**                      
 * jaw_clean_fragments_by_section_and_reference:
 * jaw_clean_fragments_by_post:                 
 * jaw_clean_fragments_by_post_and_by_section:   
 */

/**
 * Clean up fragments by post
 *
 * @since    1.0.0
 * @param    int               $postid             post id.
 * @fire:    with hooks :
 *                        save_post 
 *                        edit_post
 *                        delete_post
 *                        wp_trash_post
 *                        clean_post_cache
 *                        wp_update_comment_count
 *                        pre_post_update                 
 */
function jaw_remove_cache_part_by_post($postid) {

    /* to clean up fragment or more by post or all */
    /* must use add_filter after tigred processors complete */

    /**
     * Filter URLs cache files to remove
     *
     * @since 1.0
     *
     * @param array $purge_urls List of URLs cache files to remove
     */
    /* get all parts in array */
    $purge_urls = apply_filters('rocket_post_purge_urls', $purge_urls, $post);

    /* than remove parts proccess */
    /* fire this function when post update */
}

/**
 * Clean up fragments by section & refernce
 *
 * @since    1.0.0
 * @param    int               $postid             post id.
 * @fire:    with hooks :
 *                        save_post 
 *                        edit_post
 *                        delete_post
 *                        wp_trash_post
 *                        clean_post_cache
 *                        wp_update_comment_count
 *                        pre_post_update                 
 */
function jaw_remove_cache_part_by_section_refernce($postid) {

    /* to clean up fragment or more by post or all */
    /* must use add_filter after tigred processors complete */

    /**
     * Filter URLs cache files to remove
     *
     * @since 1.0
     *
     * @param array $purge_urls List of URLs cache files to remove
     */
    /* get all parts in array */
    $purge_urls = apply_filters('rocket_post_purge_urls', $purge_urls, $post);

    /* than remove parts proccess */
    /* fire this function when post update */
}

/**
 * Clean up fragments by post or section or refernce or both
 *
 * @since    1.0.0
 * @param    int               $postid             post id.
 * @fire:    with hooks :
 *                        save_post 
 *                        edit_post
 *                        delete_post
 *                        wp_trash_post
 *                        clean_post_cache
 *                        wp_update_comment_count
 *                        pre_post_update                 
 */
function jaw_remove_cache_part($postid) {

    /* to clean up fragment or more by post or all */
    /* must use add_filter after tigred processors complete */

    /**
     * Filter URLs cache files to remove
     *
     * @since 1.0
     *
     * @param array $purge_urls List of URLs cache files to remove
     */
    /* get all parts in array */
    $purge_urls = apply_filters('rocket_post_purge_urls', $purge_urls, $post);

    /* than remove parts proccess */
    /* fire this function when post update */
}

/**
 * cleanup fragment cache
 * 
 * $section
 * $refrence
 */
function jaw_cleanup_fragment_cache($section, $refrence) {
    $fragment_cache_dir = FRAGMENT_DIR . '/' . $section . '/';
    $fragment_cache_file = $fragment_cache_dir . jaw_fragment_cache_file_name($section, $refrence);
    $fragment_cache_files = glob($fragment_cache_file . '*.php');
    $results = array();
    foreach ($fragment_cache_files as $cache_file) {
        $results[] = unlink($fragment_cache_dir . $cache_file);
    }
    return (in_array(false, $results)) ? false : true;
}

/**
 * create cache file using section, refernce and expiration values
 *
 * @since    1.0.0
 * @param    string               $section         section name.
 * @param    string               $refrence        reference id.
 * @param    string               $exp             expiration constant.
 * @param    boolean              $unique          cache type, is same or diferent by user role.
 */
function jaw_create_fragment_cache($section, $refrence, $exp = "", $unique = false) {
    global $wp_query, $unique_sufix;
    if (is_user_logged_in() && $unique) {
        $user = (current_user_can('manage_options')) ? "admin" : "user";
    } else {
        $user = "visitor";
    }
    $content = '<?php if ( ! defined( "ABSPATH" ) ) exit;?>';
    $fragment_cache_page_dir = FRAGMENT_DIR . $wp_query->post->ID . '/';
    $fragment_cache_section_dir = $fragment_cache_page_dir . $section . '/';

    if (!is_dir(FRAGMENT_DIR)) {
        mkdir(FRAGMENT_DIR);
    }

    if (!is_dir($fragment_cache_page_dir)) {
        mkdir($fragment_cache_page_dir);
    }
    if (!is_dir($fragment_cache_section_dir)) {
        mkdir($fragment_cache_section_dir);
    }
    $fragment_cache_file = $fragment_cache_section_dir . jaw_fragment_cache_file_name($section, $refrence) . $exp . '_' . $user . '_' . $unique_sufix . '.php';

    if (!file_exists($fragment_cache_file)) {
        $content = ob_get_clean();
        $content = preg_replace("/\s+|\n+|\r/", ' ', $content);
        $results = file_put_contents($fragment_cache_file, $content);
        echo $content;
        return $results;
    }
    return false;
}

/**
 * remove cache file using path
 *
 * @since    1.0.0
 * @param    string               $fragment_cache_file         fragment cache file path.
 */
function jaw_remove_fragment_cache($fragment_cache_file) {
    return unlink($fragment_cache_file);
}

/**
 * load cache file using section, refernce and expiration values
 *
 * @since    1.0.0
 * @param    string               $section         section name.
 * @param    string               $refrence        reference id.
 * @param    string               $exp             expiration constant.
 * @param    boolean              $unique          cache type, is same or diferent by user role.
 */
function jaw_load_fragment_cache($section, $refrence, $exp = "", $unique = false) {
    global $wp_query, $unique_sufix;
    if (is_user_logged_in() && $unique) {
        $user = (current_user_can('manage_options')) ? "admin" : "user";
    } else {
        $user = "visitor";
    }
    $fragment_cache_dir = FRAGMENT_DIR . $wp_query->post->ID . '/' . $section . '/';
    $fragment_cache_file = $fragment_cache_dir . jaw_fragment_cache_file_name($section, $refrence) . $exp . '_' . $user . '_' . $unique_sufix . '.php';
    if (file_exists($fragment_cache_file)) {
        $last_change = filectime($fragment_cache_file);
        $duration = time() - $last_change;
        if ($exp >= $duration || $exp == 0) {
            require_once $fragment_cache_file;
            return true;
        }
        jaw_remove_fragment_cache($fragment_cache_file);
        return false;
    }
    return false;
}

/**
 * get cahe file name using section and refernce
 *
 * @since    1.0.0
 * @param    string               $section         section name.
 * @param    string               $refrence        reference id.
 */
function jaw_fragment_cache_file_name($section, $refrence) {
    return (wp_is_mobile()) ? "fragment_cache_mobile_" . $section . "_" . $refrence . "_" : "fragment_cache_desktop_" . $section . "_" . $refrence . "_";
}

/**
 * create & register log in file
 *
 * @since    1.0.0
 * @param    string               $message         log message.
 * @param    string               $log_file        log file name.
 */
function register_in_log($message, $log_file = "") {
    if (FRAGMENT_DURATION) {
        $suffix = date("Y-m-d");
        $log_path = FRAGMENT_DIR . 'logs/' . $suffix . '/';

        if (!is_dir(FRAGMENT_DIR . 'logs/')) {
            mkdir(FRAGMENT_DIR . 'logs/');
        }

        if (!is_dir($log_path)) {
            mkdir($log_path);
        }

        $log_file = (empty($log_file)) ? $log_path . 'global_log_' . $suffix . '.log' : $log_path . $log_file . '.log';

        if (!file_exists($log_file)) {
            $fh = fopen($log_file, 'wb');
            fwrite($fh, "creation \n");
            fclose($fh);
        }
        // Open the file to get existing content
        $current = file_get_contents($log_file);
        $current .= '- Start on ' . $suffix . " at " . date("H:i:sa") . " \n";
        // Append a new person to the file
        $current .= $message . "\n";
        // Write the contents back to the file
        file_put_contents($log_file, $current);
    }
}
