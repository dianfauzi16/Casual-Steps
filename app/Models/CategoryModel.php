<?php

namespace App\Models;

use App\Core\Model;

class CategoryModel extends Model {

    public function getAllCategories() {
        $categories = [];
        $sql = "SELECT id_kategori, nama_kategori, created_at FROM categories ORDER BY nama_kategori ASC";
        $result = $this->db->query($sql);
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $categories[] = $row;
            }
        }
        return $categories;
    }

    public function getCategoryById($id) {
        $sql = "SELECT id_kategori, nama_kategori FROM categories WHERE id_kategori = ?";
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

    public function insertCategory($nama_kategori) {
        $sql = "INSERT INTO categories (nama_kategori) VALUES (?)";
        if ($stmt = $this->db->prepare($sql)) {
            $stmt->bind_param("s", $nama_kategori);
            $success = $stmt->execute();
            $stmt->close();
            return $success;
        }
        return false;
    }

    public function updateCategory($id, $nama_kategori) {
        $sql = "UPDATE categories SET nama_kategori = ? WHERE id_kategori = ?";
        if ($stmt = $this->db->prepare($sql)) {
            $stmt->bind_param("si", $nama_kategori, $id);
            $success = $stmt->execute();
            $stmt->close();
            return $success;
        }
        return false;
    }

    public function deleteCategory($id) {
        // Sebelum menghapus kategori, pastikan kita mengatur ulang (NULL-kan) produk yang memakai kategori ini
        $sql_update_products = "UPDATE product SET id_kategori = NULL WHERE id_kategori = ?";
        if ($stmt_update = $this->db->prepare($sql_update_products)) {
            $stmt_update->bind_param("i", $id);
            $stmt_update->execute();
            $stmt_update->close();
        }

        // Baru hapus kategorinya
        $sql = "DELETE FROM categories WHERE id_kategori = ?";
        if ($stmt = $this->db->prepare($sql)) {
            $stmt->bind_param("i", $id);
            $success = $stmt->execute();
            $stmt->close();
            return $success;
        }
        return false;
    }
}
