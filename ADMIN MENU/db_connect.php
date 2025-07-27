<?php
// Pengaturan untuk koneksi ke database
$host = "127.0.0.1";
$username_db = "root";
$password_db = "";
$database_name = "pemro-web"; // Pastikan ini nama database Anda

// Membuat koneksi ke database
$conn = new mysqli($host, $username_db, $password_db, $database_name);

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
