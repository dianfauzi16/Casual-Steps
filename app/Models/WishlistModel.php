<?php

namespace App\Models;

use App\Core\Model;

class WishlistModel extends Model {

    public function toggle($user_id, $product_id) {
        $wishlisted = $this->isWishlisted($user_id, $product_id);
        
        if ($wishlisted) {
            $sql = "DELETE FROM wishlists WHERE user_id = ? AND product_id = ?";
            if ($stmt = $this->db->prepare($sql)) {
                $stmt->bind_param("ii", $user_id, $product_id);
                $stmt->execute();
                $stmt->close();
            }
            return false; // not wishlisted anymore
        } else {
            $sql = "INSERT INTO wishlists (user_id, product_id) VALUES (?, ?)";
            if ($stmt = $this->db->prepare($sql)) {
                $stmt->bind_param("ii", $user_id, $product_id);
                $stmt->execute();
                $stmt->close();
            }
            return true; // now wishlisted
        }
    }

    public function isWishlisted($user_id, $product_id) {
        $sql = "SELECT id FROM wishlists WHERE user_id = ? AND product_id = ? LIMIT 1";
        if ($stmt = $this->db->prepare($sql)) {
            $stmt->bind_param("ii", $user_id, $product_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $exists = $result->num_rows > 0;
            $stmt->close();
            return $exists;
        }
        return false;
    }

    public function getWishlist($user_id) {
        $products = [];
        $sql = "SELECT p.id, p.name, p.price, p.image, p.stock, c.nama_kategori AS nama_kategori_produk,
                       p.discount_percent, p.discount_start_date, p.discount_end_date, w.created_at as wishlisted_at
                FROM wishlists w
                JOIN product p ON w.product_id = p.id
                LEFT JOIN categories c ON p.id_kategori = c.id_kategori
                WHERE w.user_id = ?
                ORDER BY w.created_at DESC";
                
        if ($stmt = $this->db->prepare($sql)) {
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $products[] = $row;
            }
            $stmt->close();
        }
        return $products;
    }

    public function getWishlistProductIds($user_id) {
        $ids = [];
        $sql = "SELECT product_id FROM wishlists WHERE user_id = ?";
        if ($stmt = $this->db->prepare($sql)) {
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $ids[] = $row['product_id'];
            }
            $stmt->close();
        }
        return $ids;
    }

    public function countWishlist($user_id) {
        $sql = "SELECT COUNT(*) as count FROM wishlists WHERE user_id = ?";
        if ($stmt = $this->db->prepare($sql)) {
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $stmt->close();
            if ($row) {
                return (int)$row['count'];
            }
        }
        return 0;
    }
}
