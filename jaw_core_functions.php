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
 * @param    mixte                $unique          cache type, is same or diferent by user role.
 * @param    boolean              $gpdr            cookie name or false.
 */
function jaw_get_cache_fragment($section, $refrence, $exp = "JAW_PERSISTANT", $unique = false, $gpdr = false) {
    if (FRAGMENT_CACHING_STATUS) {
        $start_time = (FRAGMENT_DURATION) ? microtime(true) : "";
        global $EXPIRATION_constants;
        $exp = $EXPIRATION_constants[$exp];
        $load_fragment_cache = jaw_load_cache_fragment($section, $refrence, $exp, $unique, $gpdr);
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
 * @param    mixte                $unique          cache type, is same or diferent by user role.
 * @param    boolean              $gpdr            cookie name or false.
 */
function jaw_set_cache_fragment($section, $refrence, $exp = "JAW_PERSISTANT", $unique = false, $gpdr = false) {
    if (FRAGMENT_CACHING_STATUS) {
        $start_time = (FRAGMENT_DURATION) ? microtime(true) : "";
        global $EXPIRATION_constants;
        $exp = $EXPIRATION_constants[$exp];
        $create_fragment_cache = jaw_create_cache_fragment($section, $refrence, $exp, $unique, $gpdr);
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
            $fs = scan_fragments_dir($path . $sub_files);
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
    $cleanup_paths = scan_fragments_dir(FRAGMENT_DIR);

    /**
     * Filter fragment cache files to remove
     *
     * @since 1.0.0
     *
     * @param array $cleanup_paths List of paths cache files to remove
     */
    $cleanup_paths = apply_filters('jaw_cleanup_all_fragments_paths', $cleanup_paths);

    $results = jaw_remove_cache_fragments($cleanup_paths);
    return $results;
}

/**
 * Clean up fragments by post
 *
 * @since    1.0.0
 * @param    int               $postid             WP Post id.
 * @param    WP_Post           $post               WP Post object.
 * @fire:    with hooks :
 *                        save_post 
 *                        edit_post
 *                        delete_post
 *                        wp_trash_post
 *                        clean_post_cache
 *                        wp_update_comment_count
 *                        pre_post_update                 
 */
function jaw_cleanup_cache_fragments_by_post($postid, $post = null) {

    $post_fragments_path = FRAGMENT_DIR . $postid . '/';
    $cleanup_paths = scan_fragments_dir($post_fragments_path);

    /**
     * Filter fragment cache files to remove
     *
     * @since 1.0.0
     *
     * @param array $cleanup_paths List of paths cache files to remove
     */
    $cleanup_paths = apply_filters('jaw_cleanup_cache_fragments_by_post_paths', $cleanup_paths, $postid);

    $results = jaw_remove_cache_fragments($cleanup_paths);

    return $results;
}

/**
 * Clean up fragments by section & refernce
 *
 * @since    1.0.0
 * @param    string               $section         section name.
 * @param    string               $refrence        reference id.              
 */
function jaw_cleanup_cache_fragments_by_section_refernce($section, $refrence) {

    // to define symbole * mean all 
    $section = (empty($section) || $section == "*" || !$section ) ? "" : $section;
    $refrence = (empty($refrence) || $refrence == "*" || !$refrence) ? "" : $refrence;

    $fragments_paths = scan_fragments_dir(FRAGMENT_DIR);
    // delete cases: 
    if (!empty($section) && !empty($refrence)) {
        // delete spesific section an thier files in all posts
        foreach ($fragments_paths as $paths) {
            if (strpos($paths, $section) && strpos($paths, $refrence)) {
                $cleanup_paths[] = $paths;
            }
        }
    } elseif (!empty($section) && empty($refrence)) {
        // delete spesific section an thier files in all posts
        foreach ($fragments_paths as $paths) {
            if (strpos($paths, $section)) {
                $cleanup_paths[] = $paths;
            }
        }
    } elseif (empty($section) && !empty($refrence)) {
        // delete spesific file in all sections an in all posts
        foreach ($fragments_paths as $paths) {
            if (!is_dir($paths) && strpos($paths, "_" . $refrence . "_")) {
                $cleanup_paths[] = $paths;
            }
        }
    } else {
        return;
    }

    /**
     * Filter fragment cache files to remove
     *
     * @since 1.0.0
     *
     * @param array $cleanup_paths List of paths cache files to remove
     */
    $cleanup_paths = apply_filters('jaw_cleanup_cache_fragments_by_section_refernce_paths', $cleanup_paths, $section, $refrence);

    $results = jaw_remove_cache_fragments($cleanup_paths);

    return $results;
}

/**
 * Clean up fragments by post, section & refernce
 *
 * @since    1.0.0
 * @param    int                  $postid          post id.
 * @param    string               $section         section name.
 * @param    string               $refrence        reference id.             
 */
function jaw_cleanup_cache_fragment($postid, $section = "", $refrence = "") {

    // to define symbole * mean all 
    $postid = (empty($postid) || $postid == "*" || !$postid ) ? "" : $postid;
    $section = (empty($section) || $section == "*" || !$section ) ? "" : $section;
    $refrence = (empty($refrence) || $refrence == "*" || !$refrence) ? "" : $refrence;

    $FRAGMENT_DIR_by_post = FRAGMENT_DIR . $postid . '/';
    // delete cases: 
    if (empty($postid) && (!empty($section) || !empty($refrence))) {
        // delete by section and $refrence
        return jaw_cleanup_cache_fragments_by_section_refernce($section, $refrence);
    } elseif (empty($section) && empty($refrence)) {
        // delete by postid
        return jaw_cleanup_cache_fragments_by_post($postid);
    } elseif (!empty($section) && empty($refrence)) {
        // delete all in postid/section
        $cleanup_paths = scan_fragments_dir($FRAGMENT_DIR_by_post . $section . '/');
    } elseif (empty($section) && !empty($refrence)) {
        // delete recresive of all cache files has refrence in postid 
        $paths_array = scan_fragments_dir($FRAGMENT_DIR_by_post);
        foreach ($paths_array as $paths) {
            if (!is_dir($paths) && strpos($paths, "_" . $refrence . "_")) {
                $cleanup_paths[] = $paths;
            }
        }
    } elseif (!empty($section) && !empty($refrence)) {
        // delete recresive of all cache files has refrence in postid 
        $paths_array = scan_fragments_dir($FRAGMENT_DIR_by_post . $section . '/');
        foreach ($paths_array as $paths) {
            if (strpos($paths, "_" . $refrence . "_")) {
                $cleanup_paths[] = $paths;
            }
        }
    }elseif (empty($postid) && empty($section) && empty($refrence)) {
        // cleanup all fragments 
        jaw_cleanup_all_fragments();
    } else {
        return;
    }

    /**
     * Filter fragment cache files to remove
     *
     * @since 1.0.0
     *
     * @param array $cleanup_paths List of paths cache files to remove
     */
    $cleanup_paths = apply_filters('jaw_remove_cache_part_paths', $cleanup_paths, $postid, $section, $refrence);

    $results = jaw_remove_cache_fragments($cleanup_paths);

    return $results;
}

/**
 * remove cache file using path
 *
 * @since    1.0.0
 * @param    string               $fragment_cache_file         fragment cache file path.
 */
function jaw_remove_cache_fragment_file($fragment_cache_file) {
    return unlink($fragment_cache_file);
}

/**
 * remove cache dir using path
 *
 * @since    1.0.0
 * @param    string               $fragment_cache_dir        fragment cache dir path.
 */
function jaw_remove_cache_fragment_dir($fragment_cache_dir) {
    return rmdir($fragment_cache_dir);
}

/**
 * remove cache files using array path
 *
 * @since    1.0.0
 * @param    array               $cleanup_paths         list of fragment cache paths.
 */
function jaw_remove_cache_fragments($cleanup_paths) {
    $result = array();
    foreach ($cleanup_paths as $fragments) {
        if (is_dir($fragments)) {
            $result[] = jaw_remove_cache_fragment_dir($fragments);
        } else {
            $result[] = jaw_remove_cache_fragment_file($fragments);
        }
    }
    return $result;
}

/**
 * for get user id & role one time
 *
 * @since    1.0.0
 * @param    void 
 */
function jaw_def_user_id() {
    if (!defined("wp_get_current_user_role")) {
        $current_user = wp_get_current_user();
        $role = (current_user_can('manage_options')) ? "admin" : $current_user->roles[0];
        define("wp_get_current_user_role", $role);
    }
    if (!defined("wp_get_current_user_id")) {
        $userid = get_current_user_id();
        define("wp_get_current_user_id", $userid);
    }
}

/**
 * add suffix by user role
 *
 * @since    1.0.0
 * @param    mixte               $unique        enable or disable this option.
 */
function jaw_get_user_suffix($unique = false) {
    if ($unique === true && is_user_logged_in()) {
        $user = (current_user_can('manage_options')) ? "admin" : "user";
    } elseif ($unique == "role" && is_user_logged_in()) {
        jaw_def_user_id();
        $user = wp_get_current_user_role;
    } elseif ($unique == "id" && is_user_logged_in()) {
        jaw_def_user_id();
        $user = wp_get_current_user_id;
    } else {
        $user = "visitor";
    }

    return $user;
}

/**
 * add suffix by gpdr cookies
 *
 * @since    1.0.0
 * @param    boolean                $gpdr            enable or disable this option.
 */
function jaw_get_gpdr_suffix($gpdr = false) {    
    if ($gpdr != false && isset($_COOKIE[jaw_gpdr_cookie_name])) {
        $gpdr_suffix = '_'.$_COOKIE[jaw_gpdr_cookie_name];
    } else { 
        $gpdr_suffix = "";
    }
    return $gpdr_suffix;
}

/**
 * add suffix by device
 *
 * @since    1.0.0
 * @param    boolean               $unique        enable or disable this option.
 */
function jaw_get_device_suffix($unique = true) {
    if (wp_is_mobile() && $unique) {
        $device = "mobile_";
    } else {
        $device = "desktop_";
    }
    return $device;
}

/**
 * get cahe file name using section and refernce
 *
 * @since    1.0.0
 * @param    string               $section         section name.
 * @param    string               $refrence        reference id.
 */
function jaw_fragment_cache_file_name($section, $refrence, $unique = true) {
    $device_suffix = jaw_get_device_suffix($unique);
    return $fragment_cache_file_name = FRAGMENT_FILE_PREFFIX . "_" . $device_suffix . $section . "_" . $refrence . "_";
}

/**
 * create cache file using section, refernce and expiration values
 *
 * @since    1.0.0
 * @param    string               $section         section name.
 * @param    string               $refrence        reference id.
 * @param    string               $exp             expiration constant.
 * @param    mixte                $unique          cache type, is same or diferent by user role.
 * @param    boolean              $gpdr            cookie name or false.
 */
function jaw_create_cache_fragment($section, $refrence, $exp = "", $unique = false, $gpdr = false) {
    global $wp_query, $unique_sufix;
    $user_suffix = jaw_get_user_suffix($unique);
    $gpdr_suffix = jaw_get_gpdr_suffix($gpdr);
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
    $fragment_cache_file = $fragment_cache_section_dir . jaw_fragment_cache_file_name($section, $refrence) . $exp . '_' . $user_suffix . $gpdr_suffix . '_' .$unique_sufix . '.php';

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
 * load cache file using section, refernce and expiration values
 *
 * @since    1.0.0
 * @param    string               $section         section name.
 * @param    string               $refrence        reference id.
 * @param    string               $exp             expiration constant.
 * @param    mixte                $unique          cache type, is same or diferent by user role.
 * @param    boolean              $gpdr            cookie name or false.
 */
function jaw_load_cache_fragment($section, $refrence, $exp = "", $unique = false, $gpdr = false) {
    global $wp_query, $unique_sufix;
    $user_suffix = jaw_get_user_suffix($unique);
    $gpdr_suffix = jaw_get_gpdr_suffix($gpdr);
    $fragment_cache_dir = FRAGMENT_DIR . $wp_query->post->ID . '/' . $section . '/';
    $fragment_cache_file = $fragment_cache_dir . jaw_fragment_cache_file_name($section, $refrence) . $exp . '_' . $user_suffix . $gpdr_suffix . '_' . $unique_sufix . '.php';
    if (file_exists($fragment_cache_file)) {
        $last_change = filectime($fragment_cache_file);
        $duration = time() - $last_change;
        if ($exp >= $duration || $exp == 0) {
            require_once $fragment_cache_file;
            return true;
        }
        jaw_remove_cache_fragment_file($fragment_cache_file);
        return false;
    }
    return false;
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

// Launch hooks that cleanup all cache fragments files.
add_action( 'switch_theme', 'jaw_cleanup_all_fragments' );                                           // When user change theme.
add_action( 'user_register', 'jaw_cleanup_all_fragments' );                                          // When a user is added.
add_action( 'profile_update', 'jaw_cleanup_all_fragments' );                                         // When a user is updated.
add_action( 'deleted_user', 'jaw_cleanup_all_fragments' );                                           // When a user is deleted.
add_action( 'wp_update_nav_menu', 'jaw_cleanup_all_fragments' );                                     // When a custom menu is update. note: to be delete when create specific code
add_action( 'update_option_sidebars_widgets', 'jaw_cleanup_all_fragments' );                         // When you change the order of widgets.
add_action( 'update_option_category_base', 'jaw_cleanup_all_fragments' );                            // When category permalink prefix is update.
add_action( 'update_option_tag_base', 'jaw_cleanup_all_fragments' );                                 // When tag permalink prefix is update.
add_action( 'permalink_structure_changed', 'jaw_cleanup_all_fragments' );                            // When permalink structure is update.
add_action( 'create_term', 'jaw_cleanup_all_fragments' );                                            // When a term is created.
add_action( 'edited_terms', 'jaw_cleanup_all_fragments' );                                           // When a term is updated.
add_action( 'delete_term', 'jaw_cleanup_all_fragments' );                                            // When a term is deleted.
add_action( 'add_link', 'jaw_cleanup_all_fragments' );                                               // When a link is added.
add_action( 'edit_link', 'jaw_cleanup_all_fragments' );                                              // When a link is updated.
add_action( 'delete_link', 'jaw_cleanup_all_fragments' );                                            // When a link is deleted.
add_action( 'customize_save', 'jaw_cleanup_all_fragments' );                                         // When customizer is saved.
add_action( 'update_option_theme_mods_' . get_option( 'stylesheet' ), 'jaw_cleanup_all_fragments' ); // When location of a menu is updated.
add_action( 'upgrader_process_complete', 'jaw_cleanup_all_fragments' );                              // When a theme is updated. note: to be update when create specific code


/** Note: to be updated: when create a specific code
 * Cleanup all fragments When a widget is updated
 *
 * @since 1.0.0
 *
 * @param  object $instance Widget instance.
 * @return object Widget instance
 */
function jaw_widget_update_callback( $instance ) {
	jaw_cleanup_all_fragments();
	return $instance;
}
//add_filter( 'widget_update_callback', 'jaw_widget_update_callback' );


// Launch hooks that cleanup fragment cache files by post.
add_action( 'save_post',           'jaw_cleanup_cache_fragments_by_post');
add_action( 'edit_post',           'jaw_cleanup_cache_fragments_by_post' );
add_action( 'delete_post',           'jaw_cleanup_cache_fragments_by_post' );
add_action( 'wp_trash_post',           'jaw_cleanup_cache_fragments_by_post' );
add_action( 'clean_post_cache',        'jaw_cleanup_cache_fragments_by_post' );
add_action( 'wp_update_comment_count', 'jaw_cleanup_cache_fragments_by_post' );
add_action( 'pre_post_update', 'jaw_cleanup_cache_fragments_by_post' );
