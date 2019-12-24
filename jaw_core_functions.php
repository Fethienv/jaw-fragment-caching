<?php

/**
 * Replace Image URL
 * 
 * Filter content and replace image urls.
 */
function jaw_get_cache_part($section, $refrence, $exp = "JAW_PERSISTANT",$unique = false) {
    if (FRAGMENT_CACHING_STATUS) {
        $start_time = (FRAGMENT_DURATION) ? microtime(true) : "";
        global $EXPIRATION_constants;
        $exp = $EXPIRATION_constants[$exp];
        $load_fragment_cache = jaw_load_fragment_cache($section, $refrence, $exp, $unique);
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
function jaw_set_cache_part($section, $refrence, $exp = "JAW_PERSISTANT",$unique = false) {
    if (FRAGMENT_CACHING_STATUS) {
        $start_time = (FRAGMENT_DURATION) ? microtime(true) : "";
        global $EXPIRATION_constants;
        $exp = $EXPIRATION_constants[$exp];
        $create_fragment_cache = jaw_create_fragment_cache($section, $refrence, $exp,$unique);
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
function jaw_create_fragment_cache($section, $refrence, $exp = "",$unique = false) {
    global $wp_query,$unique_sufix;
    if(is_user_logged_in() && $unique){
      $user = (current_user_can( 'manage_options' ))?"admin":"user"; 
    }else{
      $user = "visitor";  
    }
    $content = '<?php if ( ! defined( "ABSPATH" ) ) exit;?>';
    $fragment_cache_page_dir = FRAGMENT_DIR . $wp_query->post->ID . '/';
    $fragment_cache_section_dir = $fragment_cache_page_dir . $section . '/';
    if (!is_dir($fragment_cache_page_dir)) {
        mkdir($fragment_cache_page_dir);
    }
    if (!is_dir($fragment_cache_section_dir)) {
        mkdir($fragment_cache_section_dir);
    }
    $fragment_cache_file = $fragment_cache_section_dir . jaw_fragment_cache_file_name($section, $refrence) . $exp . '_'.$user.'_'.$unique_sufix . '.php';

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
function jaw_remove_fragment_cache($fragment_cache_file) {
    return unlink($fragment_cache_file);
}

/**
 * load html fragment cache part
 * 
 * $fragment_cache_file path of cache part
 */
function jaw_load_fragment_cache($section, $refrence, $exp = "",$unique = false) {
    global $wp_query,$unique_sufix;
    if(is_user_logged_in() && $unique){
      $user = (current_user_can( 'manage_options' ))?"admin":"user"; 
    }else{
      $user = "visitor";  
    }
    $fragment_cache_dir = FRAGMENT_DIR . $wp_query->post->ID . '/' . $section . '/';
    $fragment_cache_file = $fragment_cache_dir . jaw_fragment_cache_file_name($section, $refrence) . $exp . '_'.$user.'_'.$unique_sufix.'.php';
    if (file_exists($fragment_cache_file)) {
        $last_change = filectime($fragment_cache_file);
        $duration = time() - $last_change; 
        if($exp >= $duration || $exp == 0){
          require_once $fragment_cache_file;
          return true;
        }
        jaw_remove_fragment_cache($fragment_cache_file);
        return false;
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
