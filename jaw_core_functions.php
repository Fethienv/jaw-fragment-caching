<?php

/**
 * comments will be updated
 * /


/**
 * Replace Image URL
 * 
 * Filter content and replace image urls.
 */
function jaw_get_cache_part($section, $refrence) {
    if (FRAGMENT_CACHING_STATUS) {
        $start_time = (FRAGMENT_DURATION) ? microtime(true) : "";

        global $EXPIRATION_table;
        $exp = $EXPIRATION_table[$section . "_" . $refrence];
        $load_fragment_cache = jaw_load_fragment_cache($section, $refrence, $exp);

        if (FRAGMENT_DURATION) {
            $end_time = microtime(true);
            $execution_time = ($end_time - $start_time);
            $message = 'Execution GET time = ' . $execution_time;
            register_in_log($message, $section . "_" . $refrence, true);
        }
        return $load_fragment_cache;
    }
    return false;
}

/**
 * Replace Image URL
 * 
 * Filter content and replace image urls.
 */
function jaw_set_cache_part($section, $refrence) {
    if (FRAGMENT_CACHING_STATUS) {
        $start_time = (FRAGMENT_DURATION) ? microtime(true) : "";

        global $EXPIRATION_table;
        $exp = $EXPIRATION_table[$section . "_" . $refrence];
        $create_fragment_cache = jaw_create_fragment_cache($section, $refrence, $exp);

        if (FRAGMENT_DURATION) {
            $end_time = microtime(true);
            $execution_time = ($end_time - $start_time);
            $message = 'Execution SET time = ' . $execution_time;
            register_in_log($message, $section . "_" . $refrence, true);
        }
        return $create_fragment_cache;
    }
    return false;
}

/**
 * Replace Image URL
 * 
 * Filter content and replace image urls.
 */
function jaw_start_fragment_caching() {
    if (FRAGMENT_CACHING_STATUS) {
        ob_start();
    }
}

/**
 * Replace Image URL
 * 
 * Filter content and replace image urls.
 */
function jaw_remove_cache_part($section, $refrence) {
    
}

/**
 * Replace Image URL
 * 
 * Filter content and replace image urls.
 */
function jaw_update_cache_part_expiration($device, $section, $refrence) {
    
}

/**
 * Replace Image URL
 * 
 * Filter content and replace image urls.
 */
function jaw_create_fragment_cache($section, $refrence, $exp = "") {
    global $wp_query;
    $content = "";
    $fragment_cache_page_dir = FRAGMENT_DIR . $wp_query->post->ID . '/';
    $fragment_cache_section_dir = $fragment_cache_page_dir . $section . '/';
    if (!is_dir($fragment_cache_page_dir)) {
        mkdir($fragment_cache_page_dir);
    }
    if (!is_dir($fragment_cache_section_dir)) {
        mkdir($fragment_cache_section_dir);
    }
    $fragment_cache_file = $fragment_cache_section_dir . jaw_fragment_cache_file_name($section, $refrence) . $exp . '.html';

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
 * Replace Image URL
 * 
 * Filter content and replace image urls.
 */
function jaw_remove_fragment_cache($section, $refrence, $exp = "") {
    
}

/**
 * load html fragment cache part
 * 
 * $fragment_cache_file path of cache part
 */
function jaw_load_fragment_cache($section, $refrence, $exp = "") {
    global $wp_query;
    $fragment_cache_dir = FRAGMENT_DIR . $wp_query->post->ID . '/' . $section . '/';
    $fragment_cache_file = $fragment_cache_dir . jaw_fragment_cache_file_name($section, $refrence) . $exp . '.html';
    if (file_exists($fragment_cache_file)) {
        require_once $fragment_cache_file;
        return true;
    }
    return false;
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
    $fragment_cache_files = glob($fragment_cache_file . '*.html');
    $results = array();
    foreach ($fragment_cache_files as $cache_file) {
        $results[] = unlink($fragment_cache_dir . $cache_file);
    }
    return (in_array(false, $results)) ? false : true;
}

/**
 * Replace Image URL
 * 
 * Filter content and replace image urls.
 */
function jaw_cleanup_all_fragments_caches() {
    
}

/**
 * make fragments caches file name
 * 
 * Filter content and replace image urls.
 */
function jaw_fragment_cache_file_name($section, $refrence) {
    return (wp_is_mobile()) ? "fragment_cache_mobile_" . $section . "_" . $refrence . "_" : "fragment_cache_desktop_" . $section . "_" . $refrence . "_";
}

/**
 * make fragments caches file name
 * 
 * Filter content and replace image urls.
 */
function register_in_log($message, $log_file = "", $log_enabler = false) {
    if ($log_enabler) {
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
