<?php

namespace App\Models;

use App\Core\Model;

class CartModel extends Model {
    
    public function getCartProducts($id_produk_array) {
        if (empty($id_produk_array)) return [];
        $placeholders = implode(',', array_fill(0, count($id_produk_array), '?'));
        $types = str_repeat('i', count($id_produk_array));
        
        $sql = "SELECT id, name, price, image, stock, discount_percent, discount_start_date, discount_end_date FROM product WHERE id IN ($placeholders)";
        if ($stmt = $this->db->prepare($sql)) {
            $stmt->bind_param($types, ...$id_produk_array);
            $stmt->execute();
            $result = $stmt->get_result();
            $products = [];
            while ($row = $result->fetch_assoc()) {
                $products[$row['id']] = $row;
            }
            $stmt->close();
            return $products;
        }
        return [];
    }

    public function getProductStock($id) {
        $sql = "SELECT stock FROM product WHERE id = ?";
        if ($stmt = $this->db->prepare($sql)) {
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) return $row['stock'];
            $stmt->close();
        }
        return 0;
    }
}
