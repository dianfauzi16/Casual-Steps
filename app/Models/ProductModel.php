<?php

namespace App\Models;

use App\Core\Model;

class ProductModel extends Model {

    public function getAllCategories() {
        $categories = [];
        $sql = "SELECT id_kategori, nama_kategori FROM categories ORDER BY nama_kategori ASC";
        $result = $this->db->query($sql);
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $categories[] = $row;
            }
        }
        return $categories;
    }

    public function getCategoryById($id) {
        $sql = "SELECT nama_kategori FROM categories WHERE id_kategori = ?";
        if ($stmt = $this->db->prepare($sql)) {
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                return $row;
            }
            $stmt->close();
        }
        return false;
    }

    public function getProducts($kategori_id = null, $keyword = '') {
        $products = [];
        $sql = "SELECT p.id, p.name, p.price, p.image, p.stock, c.nama_kategori AS nama_kategori_produk,
                       p.discount_percent, p.discount_start_date, p.discount_end_date, p.description, p.brand
                FROM product p
                LEFT JOIN categories c ON p.id_kategori = c.id_kategori";

        $conditions = [];
        $params = [];
        $types = "";

        if ($kategori_id !== null) {
            $conditions[] = "p.id_kategori = ?";
            $params[] = $kategori_id;
            $types .= "i";
        }

        if (!empty($keyword)) {
            $search_like = "%" . $keyword . "%";
            $conditions[] = "(p.name LIKE ? OR p.description LIKE ? OR p.brand LIKE ? OR c.nama_kategori LIKE ?)";
            for ($i = 0; $i < 4; $i++) {
                $params[] = $search_like;
                $types .= "s";
            }
        }

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }
        $sql .= " ORDER BY p.created_at DESC";

        if ($stmt = $this->db->prepare($sql)) {
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $products[] = $row;
                }
            }
            $stmt->close();
        }
        return $products;
    }

    public function getProductById($id) {
        $sql = "SELECT p.id, p.name, p.brand, p.price, p.stock, p.image, p.description, p.size, p.id_kategori, c.nama_kategori, p.average_rating, p.rating_count, p.discount_percent, p.discount_start_date, p.discount_end_date
                FROM product p
                LEFT JOIN categories c ON p.id_kategori = c.id_kategori
                WHERE p.id = ?";
        if ($stmt = $this->db->prepare($sql)) {
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows == 1) {
                return $result->fetch_assoc();
            }
            $stmt->close();
        }
        return false;
    }

    public function getProductReviews($product_id) {
        $reviews = [];
        $sql = "SELECT pr.rating, pr.review_text, pr.created_at, u.name 
                FROM product_ratings pr 
                JOIN users u ON pr.user_id = u.id 
                WHERE pr.product_id = ? 
                ORDER BY pr.created_at DESC";
        if ($stmt = $this->db->prepare($sql)) {
            $stmt->bind_param("i", $product_id);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $reviews[] = $row;
            }
            $stmt->close();
        }
        return $reviews;
    }

    public function hasUserPurchasedProduct($user_id, $product_id) {
        $sql = "SELECT o.id FROM orders o JOIN order_items oi ON o.id = oi.id_order WHERE o.user_id = ? AND oi.id_produk = ? AND o.status = 'Selesai' LIMIT 1";
        if ($stmt = $this->db->prepare($sql)) {
            $stmt->bind_param("ii", $user_id, $product_id);
            $stmt->execute();
            $has_purchased = $stmt->get_result()->num_rows > 0;
            $stmt->close();
            return $has_purchased;
        }
        return false;
    }

    public function hasUserRatedProduct($user_id, $product_id) {
        $sql = "SELECT id FROM product_ratings WHERE user_id = ? AND product_id = ? LIMIT 1";
        if ($stmt = $this->db->prepare($sql)) {
            $stmt->bind_param("ii", $user_id, $product_id);
            $stmt->execute();
            $has_rated = $stmt->get_result()->num_rows > 0;
            $stmt->close();
            return $has_rated;
        }
        return false;
    }

    public function addProductReview($user_id, $product_id, $rating, $review_text) {
        $sql = "INSERT INTO product_ratings (user_id, product_id, rating, review_text) VALUES (?, ?, ?, ?)";
        if ($stmt = $this->db->prepare($sql)) {
            $stmt->bind_param("iiis", $user_id, $product_id, $rating, $review_text);
            $result = $stmt->execute();
            $stmt->close();

            if ($result) {
                // Update average rating and rating count
                $sql_update = "UPDATE product SET 
                                average_rating = (SELECT AVG(rating) FROM product_ratings WHERE product_id = ?),
                                rating_count = (SELECT COUNT(id) FROM product_ratings WHERE product_id = ?)
                                WHERE id = ?";
                if ($stmt_up = $this->db->prepare($sql_update)) {
                    $stmt_up->bind_param("iii", $product_id, $product_id, $product_id);
                    $stmt_up->execute();
                    $stmt_up->close();
                }
            }
            return $result;
        }
        return false;
    }

    public function getRecommendedProducts($limit = 8) {
        $products = [];
        $sql = "SELECT id, name, price, image, discount_percent, discount_start_date, discount_end_date FROM product ORDER BY created_at DESC LIMIT ?";
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

    public function getBestShoes($limit = 2) {
        $products = [];
        $sql = "SELECT id, name, price, image, average_rating, rating_count, discount_percent, discount_start_date, discount_end_date 
                FROM product ORDER BY average_rating DESC, rating_count DESC LIMIT ?";
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

    public function getAllBrands($limit = 12) {
        $brands = [];
        $sql = "SELECT DISTINCT brand FROM product WHERE brand IS NOT NULL AND brand != '' ORDER BY brand ASC LIMIT ?";
        if ($stmt = $this->db->prepare($sql)) {
            $stmt->bind_param("i", $limit);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $brands[] = $row['brand'];
            }
            $stmt->close();
        }
        return $brands;
    }

    public function getPromoProducts() {
        $products = [];
        $sekarang_tanggal = date('Y-m-d');
        $sql = "SELECT p.id, p.name, p.price, p.image, p.stock,
                       p.discount_percent, p.discount_start_date, p.discount_end_date
                FROM product p
                WHERE
                    p.discount_percent > 0
                    AND (p.discount_start_date IS NULL OR ? >= p.discount_start_date)
                    AND (p.discount_end_date IS NULL OR ? <= p.discount_end_date)
                ORDER BY p.discount_percent DESC, p.created_at DESC";
        if ($stmt = $this->db->prepare($sql)) {
            $stmt->bind_param("ss", $sekarang_tanggal, $sekarang_tanggal);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $products[] = $row;
            }
            $stmt->close();
        }
        return $products;
    }
    public function insertProduct($data) {
        $sql = "INSERT INTO product (name, description, brand, price, stock, size, image, id_kategori) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        if ($stmt = $this->db->prepare($sql)) {
            $stmt->bind_param("sssdissi", 
                $data['name'], 
                $data['description'], 
                $data['brand'], 
                $data['price'], 
                $data['stock'],
                $data['size'],
                $data['image'], 
                $data['id_kategori']
            );
            $success = $stmt->execute();
            $stmt->close();
            return $success;
        }
        return false;
    }

    public function updateProduct($id, $data) {
        $updates = [];
        $params = [];
        $types = "";

        foreach ($data as $key => $value) {
            $updates[] = "$key = ?";
            $params[] = $value;
            // Menentukan tipe data bind_param
            if (in_array($key, ['price'])) {
                $types .= "d";
            } elseif (in_array($key, ['stock', 'id_kategori', 'discount_percent', 'rating_count'])) {
                $types .= "i";
            } else {
                $types .= "s";
            }
        }

        $params[] = $id;
        $types .= "i";

        $sql = "UPDATE product SET " . implode(", ", $updates) . " WHERE id = ?";
        
        if ($stmt = $this->db->prepare($sql)) {
            $stmt->bind_param($types, ...$params);
            $success = $stmt->execute();
            $stmt->close();
            return $success;
        }
        return false;
    }

    public function deleteProduct($id) {
        // Cek apakah produk ada di pesanan
        $sql_check = "SELECT COUNT(*) as count FROM order_items WHERE id_produk = ?";
        if ($stmt = $this->db->prepare($sql_check)) {
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                if ($row['count'] > 0) {
                    $stmt->close();
                    return "Produk tidak bisa dihapus karena sudah pernah dipesan oleh pelanggan.";
                }
            }
            $stmt->close();
        }

        // Ambil info gambar sebelum hapus jika diperlukan untuk dihapus di Cloudinary (Opsional, saat ini hanya DB)
        
        $sql_delete = "DELETE FROM product WHERE id = ?";
        if ($stmt = $this->db->prepare($sql_delete)) {
            $stmt->bind_param("i", $id);
            $success = $stmt->execute();
            $stmt->close();
            return $success === true ? true : "Gagal menghapus produk.";
        }
        return "Terjadi kesalahan database.";
    }
    public function updatePromo($id, $percent, $start_date, $end_date) {
        $sql = "UPDATE product SET discount_percent = ?, discount_start_date = ?, discount_end_date = ? WHERE id = ?";
        if ($stmt = $this->db->prepare($sql)) {
            // Konversi nilai null dengan tepat
            $start = empty($start_date) ? null : $start_date;
            $end = empty($end_date) ? null : $end_date;
            $pct = (int)$percent;
            
            $stmt->bind_param("issi", $pct, $start, $end, $id);
            $success = $stmt->execute();
            $stmt->close();
            return $success;
        }
        return false;
    }

    public function updatePromoAll($percent, $start_date, $end_date) {
        $sql = "UPDATE product SET discount_percent = ?, discount_start_date = ?, discount_end_date = ?";
        if ($stmt = $this->db->prepare($sql)) {
            $start = empty($start_date) ? null : $start_date;
            $end = empty($end_date) ? null : $end_date;
            $pct = (int)$percent;
            
            $stmt->bind_param("iss", $pct, $start, $end);
            $success = $stmt->execute();
            $stmt->close();
            return $success;
        }
        return false;
    }
}
