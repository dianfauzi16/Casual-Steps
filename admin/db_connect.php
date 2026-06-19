<?php
// Menyembunyikan peringatan Deprecated (Biasanya muncul dari Guzzle/Library lama di PHP versi terbaru)
error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);

// Memuat autoloader Composer dan file .env jika ada
$autoload_path = dirname(__DIR__) . '/vendor/autoload.php';
if (file_exists($autoload_path)) {
    require_once $autoload_path;
    if (class_exists('Dotenv\Dotenv')) {
        $dotenv_dir = dirname(__DIR__);
        if (file_exists($dotenv_dir . '/.env')) {
            // Menggunakan createUnsafeImmutable agar bisa dibaca fungsi getenv()
            $dotenv = Dotenv\Dotenv::createUnsafeImmutable($dotenv_dir);
            $dotenv->load();
        }
    }
}

// Pengaturan untuk koneksi ke database (Dinamis untuk Railway & Localhost)
$host = getenv('MYSQLHOST') ?: ($_ENV['MYSQLHOST'] ?? "127.0.0.1");
$username_db = getenv('MYSQLUSER') ?: ($_ENV['MYSQLUSER'] ?? "root");
$password_db = getenv('MYSQLPASSWORD') ?: ($_ENV['MYSQLPASSWORD'] ?? "");
$database_name = getenv('MYSQLDATABASE') ?: ($_ENV['MYSQLDATABASE'] ?? "pemro-web");
$port = getenv('MYSQLPORT') ?: ($_ENV['MYSQLPORT'] ?? 3306);

// Membuat koneksi ke database
$conn = new mysqli($host, $username_db, $password_db, $database_name, $port);

// Memeriksa apakah koneksi berhasil atau gagal
if ($conn->connect_error) {
    die("Koneksi ke database gagal: " . $conn->connect_error);
}
if (!function_exists('get_site_setting')) { // Cek agar fungsi tidak didefinisikan ulang
    function get_site_setting($db_connection, $key)
    {
        $setting_value = null;
        $sql = "SELECT setting_value FROM site_settings WHERE setting_key = ? LIMIT 1";

        if ($stmt = $db_connection->prepare($sql)) {
            $stmt->bind_param("s", $key);

            if ($stmt->execute()) {
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $setting_value = $row['setting_value'];
                }
            } else {
                // error_log("Gagal eksekusi get_site_setting untuk key ".$key.": ".$stmt->error);
            }
            $stmt->close();
        } else {
            // error_log("Gagal prepare get_site_setting untuk key ".$key.": ".$db_connection->error);
        }
        return $setting_value;
    }
}
