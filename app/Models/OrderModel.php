<?php

namespace App\Models;

use App\Core\Model;

class OrderModel extends Model {
    
    public function createOrder($user_id, $data, $items, $total_price, $payment_method, $status) {
        $this->db->begin_transaction();
        try {
            $sql_insert_order = "INSERT INTO orders (user_id, nama_pelanggan, email_pelanggan, telepon_pelanggan, alamat_pengiriman_lengkap, kota_pengiriman, kode_pos_pengiriman, total_price, metode_pembayaran, status, catatan_pelanggan, tanggal_pesanan) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            
            $stmt = $this->db->prepare($sql_insert_order);
            $stmt->bind_param("issssssdsss", 
                $user_id, 
                $data['nama_pelanggan'], 
                $data['email_pelanggan'], 
                $data['telepon_pelanggan'], 
                $data['alamat_pengiriman_lengkap'], 
                $data['kota_pengiriman'], 
                $data['kode_pos_pengiriman'], 
                $total_price, 
                $payment_method, 
                $status, 
                $data['catatan_pelanggan']
            );
            $stmt->execute();
            
            $order_id = $stmt->insert_id;
            $stmt->close();
            
            if (!$order_id) throw new \Exception("Gagal membuat pesanan.");

            $midtrans_id = 'CS-' . $order_id . '-' . time();
            $sql_update_midtrans = "UPDATE orders SET midtrans_order_id = ? WHERE id = ?";
            $stmt_update = $this->db->prepare($sql_update_midtrans);
            $stmt_update->bind_param("si", $midtrans_id, $order_id);
            $stmt_update->execute();
            $stmt_update->close();

            $sql_insert_item = "INSERT INTO order_items (id_order, id_produk, nama_produk_saat_pesan, quantity, size, harga_satuan_saat_pesan, subtotal_item) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt_item = $this->db->prepare($sql_insert_item);

            $sql_update_stock = "UPDATE product SET stock = stock - ? WHERE id = ? AND stock >= ?";
            $stmt_stock = $this->db->prepare($sql_update_stock);

            foreach ($items as $item) {
                $subtotal = $item['harga_satuan'] * $item['kuantitas'];
                $stmt_item->bind_param("iisisdd", $order_id, $item['id_produk'], $item['nama_produk'], $item['kuantitas'], $item['ukuran'], $item['harga_satuan'], $subtotal);
                $stmt_item->execute();

                $stmt_stock->bind_param("iii", $item['kuantitas'], $item['id_produk'], $item['kuantitas']);
                $stmt_stock->execute();
                if ($stmt_stock->affected_rows == 0) {
                    throw new \Exception("Gagal mengurangi stok produk: " . $item['nama_produk']);
                }
            }
            $stmt_item->close();
            $stmt_stock->close();

            $this->db->commit();
            return $midtrans_id;
        } catch (\Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    public function getUserOrders($user_id) {
        $sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC";
        if ($stmt = $this->db->prepare($sql)) {
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $orders = [];
            while ($row = $result->fetch_assoc()) {
                $orders[] = $row;
            }
            return $orders;
        }
        return [];
    }

    public function getOrderDetails($order_id, $user_id) {
        $sql = "SELECT * FROM orders WHERE id = ? AND user_id = ?";
        if ($stmt = $this->db->prepare($sql)) {
            $stmt->bind_param("ii", $order_id, $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($order = $result->fetch_assoc()) {
                $sql_items = "SELECT oi.*, p.image AS gambar_produk_terkini 
                              FROM order_items oi
                              LEFT JOIN product p ON oi.id_produk = p.id
                              WHERE oi.id_order = ?";
                if ($stmt_items = $this->db->prepare($sql_items)) {
                    $stmt_items->bind_param("i", $order_id);
                    $stmt_items->execute();
                    $result_items = $stmt_items->get_result();
                    $items = [];
                    while ($row = $result_items->fetch_assoc()) {
                        $items[] = $row;
                    }
                    return ['order' => $order, 'items' => $items];
                }
            }
        }
        return false;
    }

    // --- Admin Methods ---

    public function getAllOrdersAdmin() {
        $orders = [];
        $sql = "SELECT id, nama_pelanggan, tanggal_pesanan, total_price, status 
                FROM orders 
                ORDER BY tanggal_pesanan DESC";
        $result = $this->db->query($sql);
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $orders[] = $row;
            }
        }
        return $orders;
    }

    public function getOrderDetailsAdmin($order_id) {
        $sql = "SELECT id, user_id, nama_pelanggan, email_pelanggan, telepon_pelanggan, 
                       alamat_pengiriman_lengkap, kota_pengiriman, kode_pos_pengiriman, 
                       total_price, metode_pembayaran, status, catatan_pelanggan, tanggal_pesanan 
                FROM orders 
                WHERE id = ?";
        if ($stmt = $this->db->prepare($sql)) {
            $stmt->bind_param("i", $order_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($order = $result->fetch_assoc()) {
                $items = [];
                $sql_items = "SELECT id_produk, nama_produk_saat_pesan, quantity, size, harga_satuan_saat_pesan, subtotal_item 
                              FROM order_items 
                              WHERE id_order = ?";
                if ($stmt_items = $this->db->prepare($sql_items)) {
                    $stmt_items->bind_param("i", $order_id);
                    $stmt_items->execute();
                    $result_items = $stmt_items->get_result();
                    while ($row_item = $result_items->fetch_assoc()) {
                        $items[] = $row_item;
                    }
                    $stmt_items->close();
                }
                return ['order' => $order, 'items' => $items];
            }
            $stmt->close();
        }
        return false;
    }

    public function updateOrderStatus($order_id, $status) {
        $sql = "UPDATE orders SET status = ? WHERE id = ?";
        if ($stmt = $this->db->prepare($sql)) {
            $stmt->bind_param("si", $status, $order_id);
            $success = $stmt->execute();
            $stmt->close();
            return $success;
        }
        return false;
    }
}
