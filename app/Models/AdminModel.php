<?php

namespace App\Models;

use App\Core\Model;

class AdminModel extends Model {
    
    // Fungsi untuk memverifikasi kredensial login admin
    public function verifyLogin($username, $password) {
        $sql = "SELECT id, username, password FROM admins WHERE username = ?";
        if ($stmt = $this->db->prepare($sql)) {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows == 1) {
                $admin = $result->fetch_assoc();
                if (password_verify($password, $admin['password'])) {
                    return $admin;
                }
            }
        }
        return false;
    }

    public function getTotalProducts() {
        $result = $this->db->query("SELECT COUNT(id) AS total FROM product");
        return $result ? $result->fetch_assoc()['total'] : 0;
    }

    public function getTotalOrders() {
        $result = $this->db->query("SELECT COUNT(id) AS total FROM orders");
        return $result ? $result->fetch_assoc()['total'] : 0;
    }

    public function getTotalCustomers() {
        $result = $this->db->query("SELECT COUNT(id) AS total FROM users");
        return $result ? $result->fetch_assoc()['total'] : 0;
    }

    public function getTotalRevenue() {
        $result = $this->db->query("SELECT SUM(total_price) AS total FROM orders WHERE status = 'Selesai'");
        return $result ? ($result->fetch_assoc()['total'] ?? 0) : 0;
    }

    public function getRecentOrders($limit = 5) {
        $orders = [];
        $sql = "SELECT id, nama_pelanggan, total_price, status, tanggal_pesanan 
                FROM orders ORDER BY tanggal_pesanan DESC LIMIT ?";
        if ($stmt = $this->db->prepare($sql)) {
            $stmt->bind_param("i", $limit);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $orders[] = $row;
            }
            $stmt->close();
        }
        return $orders;
    }

    public function getLowStockProducts($threshold = 5) {
        $products = [];
        $sql = "SELECT id, name, stock, image 
                FROM product WHERE stock <= ? ORDER BY stock ASC";
        if ($stmt = $this->db->prepare($sql)) {
            $stmt->bind_param("i", $threshold);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $products[] = $row;
            }
            $stmt->close();
        }
        return $products;
    }
}
