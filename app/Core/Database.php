<?php

namespace App\Core;

use mysqli;

class Database {
    private static $instance = null;
    private $conn;

    private function __construct() {
        // Menyembunyikan peringatan Deprecated (Biasanya muncul dari Guzzle/Library lama di PHP versi terbaru)
        error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);

        $host = getenv('MYSQLHOST') ?: ($_ENV['MYSQLHOST'] ?? "127.0.0.1");
        $username_db = getenv('MYSQLUSER') ?: ($_ENV['MYSQLUSER'] ?? "root");
        $password_db = getenv('MYSQLPASSWORD') ?: ($_ENV['MYSQLPASSWORD'] ?? "");
        $database_name = getenv('MYSQLDATABASE') ?: ($_ENV['MYSQLDATABASE'] ?? "pemro-web");
        $port = getenv('MYSQLPORT') ?: ($_ENV['MYSQLPORT'] ?? 3306);

        $this->conn = new mysqli($host, $username_db, $password_db, $database_name, $port);

        if ($this->conn->connect_error) {
            die("Koneksi ke database gagal: " . $this->conn->connect_error);
        }
    }

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->conn;
    }
}
