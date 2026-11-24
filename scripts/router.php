<?php
/**
 * DRF Augenblicke - Unified Native Router for Local Development
 * Routes:
 *   /              -> WordPress (drf-augenblicke)
 *   /forum/        -> Flarum Community Forum
 * Completely native, zero Docker, zero CPU overhead.
 */

$raw_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = urldecode($raw_uri);

$base_dir = dirname(__DIR__);
$wp_dir = $base_dir . '/wordpress';
$flarum_dir = $base_dir . '/flarum/app';
$flarum_public = $flarum_dir . '/public';

// Helper: Serve static file with correct MIME type
function drf_serve_static($file) {
    if (!file_exists($file) || is_dir($file)) {
        return false;
    }
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    $mimes = [
        'css'   => 'text/css',
        'js'    => 'application/javascript',
        'json'  => 'application/json',
        'png'   => 'image/png',
        'jpg'   => 'image/jpeg',
        'jpeg'  => 'image/jpeg',
        'gif'   => 'image/gif',
        'svg'   => 'image/svg+xml',
        'webp'  => 'image/webp',
        'woff'  => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf'   => 'font/ttf',
        'eot'   => 'application/vnd.ms-fontobject',
        'ico'   => 'image/x-icon',
        'pdf'   => 'application/pdf',
        'zip'   => 'application/zip',
    ];
    if (isset($mimes[$ext])) {
        header('Content-Type: ' . $mimes[$ext]);
    }
    header('Content-Length: ' . filesize($file));
    readfile($file);
    exit;
}

// 1. FLARUM ROUTING (/forum/...)
if (strpos($uri, '/forum') === 0) {
    $flarum_rel = substr($uri, 6); // Strip '/forum'
    if ($flarum_rel === '') {
        header('Location: /forum/');
        exit;
    }

    // Static file in flarum/public?
    $flarum_file = $flarum_public . $flarum_rel;
    if ($flarum_rel !== '/' && file_exists($flarum_file) && !is_dir($flarum_file)) {
        drf_serve_static($flarum_file);
    }

    // Forward to Flarum index.php
    $_SERVER['SCRIPT_FILENAME'] = $flarum_public . '/index.php';
    $_SERVER['SCRIPT_NAME']     = '/forum/index.php';
    $_SERVER['PHP_SELF']        = '/forum/index.php';
    chdir($flarum_public);
    require $flarum_public . '/index.php';
    exit;
}

// 1b. FILEGATOR ROUTING (/dateien/...)
if (strpos($uri, '/dateien') === 0) {
    $fg_public = $base_dir . '/filegator/dist';
    $fg_rel = substr($uri, 8); // Strip '/dateien'
    if ($fg_rel === '') {
        header('Location: /dateien/');
        exit;
    }

    // Static file in filegator/dist?
    $fg_file = $fg_public . $fg_rel;
    if ($fg_rel !== '/' && file_exists($fg_file) && !is_dir($fg_file)) {
        drf_serve_static($fg_file);
    }

    // Forward to FileGator dist/index.php
    define('APP_PUBLIC_PATH', '/dateien/');
    $_SERVER['SCRIPT_FILENAME'] = $fg_public . '/index.php';
    $_SERVER['SCRIPT_NAME']     = '/dateien/index.php';
    $_SERVER['PHP_SELF']        = '/dateien/index.php';
    chdir($fg_public);
    require $fg_public . '/index.php';
    exit;
}

// 2. WORDPRESS STATIC FILES
$wp_file = $wp_dir . $uri;
if ($uri !== '/' && file_exists($wp_file) && !is_dir($wp_file)) {
    // If it's a php file (like wp-login.php, wp-admin/...)
    if (substr($wp_file, -4) === '.php') {
        $_SERVER['SCRIPT_FILENAME'] = $wp_file;
        $_SERVER['SCRIPT_NAME']     = $uri;
        $_SERVER['PHP_SELF']        = $uri;
        chdir(dirname($wp_file));
        require $wp_file;
        exit;
    }
    drf_serve_static($wp_file);
}

// 3. WORDPRESS DIRECTORY INDEX OR SUBDIRECTORIES (e.g. /wp-admin/)
if (is_dir($wp_file)) {
    $index = rtrim($wp_file, '/') . '/index.php';
    if (file_exists($index)) {
        $_SERVER['SCRIPT_FILENAME'] = $index;
        $_SERVER['SCRIPT_NAME']     = rtrim($uri, '/') . '/index.php';
        $_SERVER['PHP_SELF']        = rtrim($uri, '/') . '/index.php';
        chdir(dirname($index));
        require $index;
        exit;
    }
}

// 4. WORDPRESS FRONT CONTROLLER
$_SERVER['SCRIPT_FILENAME'] = $wp_dir . '/index.php';
$_SERVER['SCRIPT_NAME']     = '/index.php';
$_SERVER['PHP_SELF']        = '/index.php';
chdir($wp_dir);
require $wp_dir . '/index.php';
exit;
