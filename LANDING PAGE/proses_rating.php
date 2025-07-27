<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../ADMIN MENU/db_connect.php';

// 1. Cek apakah user sudah login
if (!isset($_SESSION['user_loggedin']) || $_SESSION['user_loggedin'] !== true || !isset($_SESSION['user_id'])) {
    // Seharusnya tidak terjadi jika form disembunyikan dengan benar, tapi ini untuk keamanan
    $_SESSION['pesan_notifikasi_produk'] = "Anda harus login untuk memberikan rating.";
    $_SESSION['tipe_notifikasi_produk'] = "warning";
    header('Location: login_pelanggan.php');
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
    $rating = filter_input(INPUT_POST, 'rating', FILTER_VALIDATE_INT);
    $review_text = trim(filter_input(INPUT_POST, 'review_text', FILTER_SANITIZE_STRING));

    // Validasi input
    if (!$product_id || !$rating || $rating < 1 || $rating > 5) {
        $_SESSION['pesan_notifikasi_produk'] = "Data rating tidak valid.";
        $_SESSION['tipe_notifikasi_produk'] = "danger";
        header('Location: detail_produk.php?id=' . $product_id);
        exit;
    }

    // Mulai transaksi database
    $conn->begin_transaction();

    try {
        // 2. Verifikasi server-side: Apakah user benar-benar sudah membeli produk ini?
        $sql_check_purchase = "SELECT o.id FROM orders o JOIN order_items oi ON o.id = oi.id_order WHERE o.user_id = ? AND oi.id_produk = ? AND o.status = 'Selesai' LIMIT 1";
        $stmt_purchase = $conn->prepare($sql_check_purchase);
        $stmt_purchase->bind_param("ii", $user_id, $product_id);
        $stmt_purchase->execute();
        $has_purchased = $stmt_purchase->get_result()->num_rows > 0;
        $stmt_purchase->close();

        if (!$has_purchased) {
            throw new Exception("Anda tidak dapat memberi rating pada produk yang belum Anda beli.");
        }

        // 3. Verifikasi server-side: Apakah user sudah pernah memberi rating?
        $sql_check_rating = "SELECT id FROM product_ratings WHERE user_id = ? AND product_id = ? LIMIT 1";
        $stmt_rating_check = $conn->prepare($sql_check_rating);
        $stmt_rating_check->bind_param("ii", $user_id, $product_id);
        $stmt_rating_check->execute();
        $has_rated = $stmt_rating_check->get_result()->num_rows > 0;
        $stmt_rating_check->close();

        if ($has_rated) {
            throw new Exception("Anda sudah pernah memberikan rating untuk produk ini.");
        }

        // 4. Insert rating baru ke tabel product_ratings
        $sql_insert_rating = "INSERT INTO product_ratings (product_id, user_id, rating, review_text) VALUES (?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($sql_insert_rating);
        $stmt_insert->bind_param("iiis", $product_id, $user_id, $rating, $review_text);
        if (!$stmt_insert->execute()) {
            throw new Exception("Gagal menyimpan ulasan Anda.");
        }
        $stmt_insert->close();

        // 5. Hitung ulang dan update average_rating & rating_count di tabel product
        $sql_update_product_rating = "
            UPDATE product p
            JOIN (
                SELECT 
                    product_id, 
                    AVG(rating) as avg_r, 
                    COUNT(id) as count_r 
                FROM product_ratings 
                WHERE product_id = ?
                GROUP BY product_id
            ) AS pr ON p.id = pr.product_id
            SET p.average_rating = pr.avg_r, p.rating_count = pr.count_r
            WHERE p.id = ?
        ";
        $stmt_update = $conn->prepare($sql_update_product_rating);
        $stmt_update->bind_param("ii", $product_id, $product_id);
        if (!$stmt_update->execute()) {
            throw new Exception("Gagal memperbarui rating produk.");
        }
        $stmt_update->close();

        // Jika semua berhasil, commit transaksi
        $conn->commit();
        $_SESSION['pesan_notifikasi_produk'] = "Terima kasih! Ulasan Anda telah berhasil disimpan.";
        $_SESSION['tipe_notifikasi_produk'] = "success";
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['pesan_notifikasi_produk'] = "Terjadi kesalahan: " . $e->getMessage();
        $_SESSION['tipe_notifikasi_produk'] = "danger";
    }

    header('Location: detail_produk.php?id=' . $product_id);
    exit;
}
