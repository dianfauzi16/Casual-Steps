<?php
error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);
date_default_timezone_set('Asia/Jakarta');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Menentukan Base URL secara dinamis agar path file & redirect selalu akurat
$base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
$base_url .= str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
if (substr($base_url, -1) !== '/') {
    $base_url .= '/';
}
define('BASE_URL', $base_url);

require_once 'vendor/autoload.php';

// Load .env (jika ada) untuk keamanan dari awal
if (class_exists('Dotenv\Dotenv')) {
    if (file_exists(__DIR__ . '/.env')) {
        $dotenv = Dotenv\Dotenv::createUnsafeImmutable(__DIR__);
        $dotenv->load();
    }
}

// Inisialisasi router utama MVC
$app = new App\Core\App();