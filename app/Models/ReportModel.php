<?php

namespace App\Models;

use App\Core\Model;

class ReportModel extends Model {
    
    public function getGeneralStats() {
        $stats = [
            'total_produk' => 0,
            'total_kategori' => 0,
            'total_pelanggan' => 0,
            'total_pesanan' => 0,
            'total_pendapatan' => 0
        ];

        // Total Produk
        $res = $this->db->query("SELECT COUNT(id) AS total FROM product");
        if ($res) $stats['total_produk'] = $res->fetch_assoc()['total'];

        // Total Kategori
        $res = $this->db->query("SELECT COUNT(id_kategori) AS total FROM categories");
        if ($res) $stats['total_kategori'] = $res->fetch_assoc()['total'];

        // Total Pelanggan
        $res = $this->db->query("SELECT COUNT(id) AS total FROM users");
        if ($res) $stats['total_pelanggan'] = $res->fetch_assoc()['total'];

        // Total Pesanan
        $res = $this->db->query("SELECT COUNT(id) AS total FROM orders");
        if ($res) $stats['total_pesanan'] = $res->fetch_assoc()['total'];

        // Total Pendapatan
        $res = $this->db->query("SELECT SUM(total_price) AS total FROM orders WHERE status = 'Selesai'");
        if ($res) $stats['total_pendapatan'] = $res->fetch_assoc()['total'] ?? 0;

        return $stats;
    }

    public function getTopProducts($limit = 5) {
        $products = [];
        $sql = "SELECT p.id, p.name AS nama_produk, p.image AS gambar_produk, SUM(oi.quantity) AS total_terjual 
                FROM order_items oi 
                JOIN product p ON oi.id_produk = p.id 
                JOIN orders o ON oi.id_order = o.id 
                WHERE o.status = 'Selesai' 
                GROUP BY oi.id_produk 
                ORDER BY total_terjual DESC 
                LIMIT ?";
        
        if ($stmt = $this->db->prepare($sql)) {
            $stmt->bind_param("i", $limit);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $products[] = $row;
            }
            $stmt->close();
        }
        return $products;
    }

    public function getSalesByPeriod($startDate, $endDate) {
        $sales = [];
        $total_revenue = 0;
        
        $start = $startDate . ' 00:00:00';
        $end = $endDate . ' 23:59:59';
        
        $sql = "SELECT id, nama_pelanggan, tanggal_pesanan, total_price, status 
                FROM orders 
                WHERE status = 'Selesai' AND tanggal_pesanan BETWEEN ? AND ? 
                ORDER BY tanggal_pesanan DESC";
                
        if ($stmt = $this->db->prepare($sql)) {
            $stmt->bind_param("ss", $start, $end);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $sales[] = $row;
                $total_revenue += $row['total_price'];
            }
            $stmt->close();
        }
        
        return [
            'orders' => $sales,
            'total_revenue' => $total_revenue
        ];
    }
}
