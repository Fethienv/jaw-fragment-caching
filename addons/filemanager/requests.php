<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include('../../global_config.php');

global $jaw_fragments_apikey;

if (!isset($_POST['apikey']) || $jaw_fragments_apikey != $_POST['apikey']) {
    echo "Error 403 : Forbidden";
    exit();
}

if ($_POST['action'] == 'list') {
    $path = (empty($_POST['open_path']) || !isset($_POST['open_path'])) ? err(404, 'File or Directory Not Found') : $_POST['open_path'];
    send_headers();
    get_list($path);
} elseif ($_POST['action'] == 'delete') {
    $path      = (empty($_POST['delete_path']) || !isset($_POST['delete_path'])) ? err(404, 'File or Directory Not Found') : $_POST['delete_path'];
    $re_create = (empty($_POST['re_create_path']) || !isset($_POST['re_create_path'])) ? false : $_POST['re_create_path'];
    send_headers();
    delete_file_or_dir($path,$re_create);
} elseif ($_POST['action'] == 'download') {
    $path = (empty($_POST['download_path']) || !isset($_POST['download_path'])) ? err(404, 'File or Directory Not Found') : $_POST['download_path'];
    download_file($path);
} elseif ($_POST['action'] == 'upload') {
    //$path = (empty($_POST['delete_path']) || !isset($_POST['delete_path'])) ? err(404, 'File or Directory Not Found') : $_POST['delete_path'];
    upload_file($path);
} elseif ($_POST['action'] == 'create') {
    //$path = (empty($_POST['delete_path']) || !isset($_POST['delete_path'])) ? err(404, 'File or Directory Not Found') : $_POST['delete_path'];
    create_folder($path);
}

function send_headers() {
    header("Content-Type: application/json");
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
}

function get_list($file) {
    global $allow_show_folders, $hidden_patterns, $allow_delete;
    if (is_dir($file)) {
        if (!is_forbidden_dir($file)) {
            $directory = $file;
            $result = [];
            $files = array_diff(scandir($directory), ['.', '..']);
            foreach ($files as $entry) {
                if (!is_entry_ignored($entry, $allow_show_folders, $hidden_patterns)) {
                    $i = (substr($directory, -1) == "/") ? $directory . $entry : $directory . '/' . $entry;
                    $stat = stat($i);
                    $result[] = [
                        'mtime' => $stat['mtime'],
                        'size' => $stat['size'],
                        'name' => basename($i),
                        'path' => preg_replace('@^\./@', '', $i),
                        'parent_path' => $directory,
                        'is_dir' => is_dir($i),
                        'is_deleteable' => $allow_delete && ((!is_dir($i) && is_writable($directory)) ||
                        (is_dir($i) && is_writable($directory) && is_recursively_deleteable($i))),
                        'is_readable' => is_readable($i),
                        'is_writable' => is_writable($i),
                        'is_executable' => is_executable($i),
                    ];
                }
            }
            usort($result, function($f1, $f2) {
                $f1_key = ($f1['is_dir'] ?: 2) . $f1['name'];
                $f2_key = ($f2['is_dir'] ?: 2) . $f2['name'];
                return $f1_key > $f2_key;
            });
            echo json_encode(['success' => true, 'is_writable' => is_writable($file), 'results' => $result]);
        } else {
            err(403, "Forbidden");
        }
    } else {
        err(412, "Not a Directory");
    }
    exit();
}

function delete_file_or_dir($path,$re_create) {
    $result = [];
    if (is_dir($path)) {
        $files = scan_dir($path);
        foreach ($files as $file) {
            if (is_dir($file)) {
                $result[] = delete_dir($file);
            } else {
                $result[] = delete_file($file);
            }
        }
    } else {
        $result[] = delete_file($path);
    }
    if($re_create != "false"){
        @mkdir($path);
    }
    echo json_encode(['success' => true, 're_create' => $re_create,'results' => $result]);
}

function delete_file($path) {
    $rslt = unlink($path);
    return $result = ['removed_path' => "$path", 'result' => "$rslt"];
}

function delete_dir($path) {
    $rslt = rmdir($path . '/');
    return $result = ['removed_path' => "$path", 'result' => "$rslt"];
}

function scan_dir($path) {
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

function download_file($path) {
    global $disallowed_patterns;
    foreach ($disallowed_patterns as $pattern) {
        if (fnmatch($pattern, $path)) {
            err(403, "Files of this type are not allowed.");
            exit;
        }
    }
    ob_start();
    header('Content-Description: File Transfer');
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    header('Content-Type: ' . finfo_file($finfo, $path));
    //header('Content-Type: application/octet-stream');
    //header("Content-Type: application/text/x-vCard");
    header('Content-Disposition: attachment; filename="' . basename($path) . '"');
    header('Expires: 0');
    header('Pragma: public');
    header('Content-Length: ' . filesize($path));
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Content-Transfer-Encoding: binary");
    ob_flush();
    flush(); // Flush system output buffer
    readfile($path);
    ob_clean();
    echo json_encode(['path' => $path]);
    ob_flush();
    flush();
    exit;
}

function upload_file($path) {
    global $disallowed_patterns;
    foreach ($disallowed_patterns as $pattern) {
        if (fnmatch($pattern, $_FILES['file_data']['name'])) {
            err(403, "Files of this type are not allowed.");
        }
    }
    $res = move_uploaded_file($_FILES['file_data']['tmp_name'], $path . '/' . $_FILES['file_data']['name']);
    echo json_encode(['path' => $path]);
    exit;
}

function create_folder($path) {
    // don't allow actions outside root. we also filter out slashes to catch args like './../outside'
    $dir = $_POST['name'];
    $dir = str_replace('/', '', $dir);
    if (substr($dir, 0, 2) === '..')
        exit;
    chdir($file);
    @mkdir($_POST['name']);
    echo json_encode(['path' => $dir]);
    exit;
}

function is_forbidden_dir($dir) {
    if (!isset($_POST['fragment_dir']) || empty($_POST['fragment_dir'])) {
        return true;
    }
    $fragment_path = $_POST['fragment_dir'];
    $fragment_path_parts = explode('/', $fragment_path);
    $path_level = sizeof($fragment_path_parts);
    array_pop($fragment_path_parts);
    $restrict_folder_level = "/" . implode('/', $fragment_path_parts);
    $dir_parts = explode('/', $dir);
    $dir_level = sizeof($dir_parts);
    if ($dir == $restrict_folder_level || $dir_level < $path_level) {
        return true;
    }
    return false;
}

function err($code, $msg) {
    http_response_code($code);
    echo json_encode(['code' => intval($code), 'msg' => $msg]);
}

function is_entry_ignored($entry, $allow_show_folders, $hidden_patterns) {
    if ($entry === basename(__FILE__)) {
        return true;
    }

    if (is_dir($entry) && !$allow_show_folders) {
        return true;
    }
    foreach ($hidden_patterns as $pattern) {
        if (fnmatch($pattern, $entry)) {
            return true;
        }
    }
    return false;
}

exit;

$wordpress_root = (empty($wordpress_root)) ? '' : $wordpress_root . '/';
$FRAGMENT_DIR = $_SERVER["DOCUMENT_ROOT"] . '/' . $wordpress_root . $FRAGMENT_DIR . 'jawc-fragments-caching_' . $unique_sufix;

//$tmp_dir = dirname($_SERVER['SCRIPT_FILENAME']);
$tmp_dir = $FRAGMENT_DIR;

if (DIRECTORY_SEPARATOR === '\\')
    $tmp_dir = str_replace('/', DIRECTORY_SEPARATOR, $tmp_dir);
$tmp = get_absolute_path($tmp_dir . '/' . $_REQUEST['file']);

if ($tmp === false)
    err(404, 'File or Directory Not Found');
if (substr($tmp, 0, strlen($tmp_dir)) !== $tmp_dir)
    err(403, "Forbidden");
if (strpos($_REQUEST['file'], DIRECTORY_SEPARATOR) === 0)
    err(403, "Forbidden");
if (preg_match('@^.+://@', $_REQUEST['file'])) {
    err(403, "Forbidden");
}

if (!$_COOKIE['_sfm_xsrf'])
    setcookie('_sfm_xsrf', bin2hex(openssl_random_pseudo_bytes(16)));
if ($_POST) {
    if ($_COOKIE['_sfm_xsrf'] !== $_POST['xsrf'] || !$_POST['xsrf'])
        err(403, "XSRF Failure");
}

$file = $_REQUEST['file'] ? $_REQUEST['file'] : $FRAGMENT_DIR;
if ($_GET['do'] == 'list') {
    if (is_dir($file)) {
        $directory = $file;
        $result = [];
        $files = array_diff(scandir($directory), ['.', '..']);
        foreach ($files as $entry)
            if (!is_entry_ignored($entry, $allow_show_folders, $hidden_patterns)) {
                $i = $directory . '/' . $entry;
                $stat = stat($i);
                $result[] = [
                    'mtime' => $stat['mtime'],
                    'size' => $stat['size'],
                    'name' => basename($i),
                    'path' => preg_replace('@^\./@', '', $i),
                    'is_dir' => is_dir($i),
                    'is_deleteable' => $allow_delete && ((!is_dir($i) && is_writable($directory)) ||
                    (is_dir($i) && is_writable($directory) && is_recursively_deleteable($i))),
                    'is_readable' => is_readable($i),
                    'is_writable' => is_writable($i),
                    'is_executable' => is_executable($i),
                ];
            }
        usort($result, function($f1, $f2) {
            $f1_key = ($f1['is_dir'] ?: 2) . $f1['name'];
            $f2_key = ($f2['is_dir'] ?: 2) . $f2['name'];
            return $f1_key > $f2_key;
        });
    } else {
        err(412, "Not a Directory");
    }
    echo json_encode(['success' => true, 'is_writable' => is_writable($file), 'results' => $result]);
    exit;
} elseif ($_POST['do'] == 'delete') {
    if ($allow_delete) {
        rmrf($file);
    }
    exit;
} elseif ($_POST['do'] == 'mkdir' && $allow_create_folder) {
    // don't allow actions outside root. we also filter out slashes to catch args like './../outside'
    $dir = $_POST['name'];
    $dir = str_replace('/', '', $dir);
    if (substr($dir, 0, 2) === '..')
        exit;
    chdir($file);
    @mkdir($_POST['name']);
    exit;
} elseif ($_POST['do'] == 'upload' && $allow_upload) {
    foreach ($disallowed_patterns as $pattern)
        if (fnmatch($pattern, $_FILES['file_data']['name']))
            err(403, "Files of this type are not allowed.");

    $res = move_uploaded_file($_FILES['file_data']['tmp_name'], $file . '/' . $_FILES['file_data']['name']);
    exit;
} elseif ($do == 'download') {
    foreach ($disallowed_patterns as $pattern)
        if (fnmatch($pattern, $file))
            err(403, "Files of this type are not allowed.");

    $filename = basename($file);
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    header('Content-Type: ' . finfo_file($finfo, $file));
    header('Content-Length: ' . filesize($file));
    header(sprintf('Content-Disposition: attachment; filename=%s',
                    strpos('MSIE', $_SERVER['HTTP_REFERER']) ? rawurlencode($filename) : "\"$filename\"" ));
    ob_flush();
    readfile($file);
    exit;
}

function rmrf($dir) {
    if (is_dir($dir)) {
        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file)
            rmrf("$dir/$file");
        rmdir($dir);
    } else {
        unlink($dir);
    }
}

function is_recursively_deleteable($d) {
    $stack = [$d];
    while ($dir = array_pop($stack)) {
        if (!is_readable($dir) || !is_writable($dir))
            return false;
        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file)
            if (is_dir($file)) {
                $stack[] = "$dir/$file";
            }
    }
    return true;
}

// from: http://php.net/manual/en/function.realpath.php#84012
function get_absolute_path($path) {
    $path = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
    $parts = explode(DIRECTORY_SEPARATOR, $path);
    $absolutes = [];
    foreach ($parts as $part) {
        if ('.' == $part)
            continue;
        if ('..' == $part) {
            array_pop($absolutes);
        } else {
            $absolutes[] = $part;
        }
    }
    return implode(DIRECTORY_SEPARATOR, $absolutes);
}

function asBytes($ini_v) {
    $ini_v = trim($ini_v);
    $s = ['g' => 1 << 30, 'm' => 1 << 20, 'k' => 1 << 10];
    return intval($ini_v) * ($s[strtolower(substr($ini_v, -1))] ?: 1);
}

$MAX_UPLOAD_SIZE = min(asBytes(ini_get('post_max_size')), asBytes(ini_get('upload_max_filesize')));
