<?php

namespace App\Core;

class Model {
    protected $db;

    public function __construct() {
        // Otomatis mengambil koneksi dari class Database saat model dipanggil
        $this->db = Database::getInstance()->getConnection();
    }
}
