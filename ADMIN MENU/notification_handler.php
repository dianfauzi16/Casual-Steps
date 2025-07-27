<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/db_connect.php';

\Midtrans\Config::$isProduction = false;
\Midtrans\Config::$serverKey = 'SB-Mid-server-7rXZtaLcNc8M3I9VZYtj9eoE';
\Midtrans\Config::$isSanitized = true;
\Midtrans\Config::$is3ds = true;

try {
    $notif = new \Midtrans\Notification();
} catch (Exception $e) {
    http_response_code(400); // Bad request, notifikasi tidak valid
    error_log('Midtrans Notification Error: ' . $e->getMessage());
    exit;
}

$transaction_status = $notif->transaction_status;
$payment_type = $notif->payment_type;
$order_id_from_midtrans = $notif->order_id;
$fraud_status = $notif->fraud_status;

// Cari pesanan di database menggunakan midtrans_order_id
$sql_get_order = "SELECT id, status FROM orders WHERE midtrans_order_id = ?";
$stmt_get_order = $conn->prepare($sql_get_order);
$stmt_get_order->bind_param("s", $order_id_from_midtrans);
$stmt_get_order->execute();
$result_order = $stmt_get_order->get_result();
$order = $result_order->fetch_assoc();
$stmt_get_order->close();

if (!$order) {
    http_response_code(404); // Not Found
    error_log("Midtrans notification: Order not found for midtrans_order_id: " . $order_id_from_midtrans);
    exit;
}

$internal_order_id = $order['id'];
$current_status = $order['status'];
$new_status = null;

// Tentukan status baru berdasarkan notifikasi
if ($transaction_status == 'capture') {
    if ($payment_type == 'credit_card') {
        if ($fraud_status == 'challenge') {
            $new_status = 'Menunggu Verifikasi'; // Status custom jika perlu
        } else {
            $new_status = 'Diproses';
        }
    }
} else if ($transaction_status == 'settlement') {
    $new_status = 'Diproses';
} else if ($transaction_status == 'pending') {
    $new_status = 'Menunggu Pembayaran';
} else if ($transaction_status == 'deny' || $transaction_status == 'expire' || $transaction_status == 'cancel') {
    $new_status = 'Dibatalkan';
}

// Lakukan update HANYA jika status baru valid dan berbeda dari status saat ini
if ($new_status && $new_status !== $current_status) {
    $conn->begin_transaction();
    try {
        // 1. Update status pesanan
        $sql_update = "UPDATE orders SET status = ?, tanggal_update_status = NOW() WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("si", $new_status, $internal_order_id);
        $stmt_update->execute();
        if ($stmt_update->affected_rows == 0) {
            throw new Exception("Gagal update status pesanan (affected_rows = 0).");
        }
        $stmt_update->close();

        // LOGIKA PENGURANGAN STOK DIHAPUS DARI SINI
        // Karena sudah ditangani di proses_midtrans_token.php

        // Jika semua berhasil, commit transaksi
        $conn->commit();
        http_response_code(200); // OK
        error_log("Status untuk order ID " . $order_id_from_midtrans . " berhasil diupdate ke " . $new_status);

    } catch (Exception $e) {
        $conn->rollback();
        http_response_code(500); // Internal Server Error
        error_log("Gagal memproses notifikasi untuk order ID: " . $order_id_from_midtrans . ". Error: " . $e->getMessage());
    }
} else {
    // Tidak ada perubahan status yang perlu dilakukan (misal: notifikasi duplikat)
    http_response_code(200); // Beri tahu Midtrans bahwa notifikasi sudah diterima
    error_log("Notifikasi duplikat atau tidak memerlukan aksi untuk order ID: " . $order_id_from_midtrans . ". Status saat ini: " . $current_status . ", Notifikasi: " . $transaction_status);
}

if (isset($conn)) $conn->close();
