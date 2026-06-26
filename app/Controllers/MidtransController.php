<?php

namespace App\Controllers;

use App\Core\Controller;

class MidtransController extends Controller {

    public function notification() {
        require_once dirname(dirname(__DIR__)) . '/vendor/autoload.php';
        
        \Midtrans\Config::$isProduction = false;
        \Midtrans\Config::$serverKey = 'SB-Mid-server-7rXZtaLcNc8M3I9VZYtj9eoE'; // TODO: Move to DB/env
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        try {
            $notif = new \Midtrans\Notification();
        } catch (\Exception $e) {
            http_response_code(400); // Bad request
            error_log('Midtrans Notification Error: ' . $e->getMessage());
            exit;
        }

        $transaction_status = $notif->transaction_status;
        $payment_type = $notif->payment_type;
        $order_id_from_midtrans = $notif->order_id;
        $fraud_status = $notif->fraud_status;

        $orderModel = $this->model('OrderModel');
        
        // Custom query to get order by midtrans_order_id
        $db = $orderModel->getDb();
        $sql_get_order = "SELECT id, status FROM orders WHERE midtrans_order_id = ?";
        $stmt_get_order = $db->prepare($sql_get_order);
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

        if ($transaction_status == 'capture') {
            if ($payment_type == 'credit_card') {
                if ($fraud_status == 'challenge') {
                    $new_status = 'Menunggu Verifikasi';
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

        if ($new_status && $new_status !== $current_status) {
            $db->begin_transaction();
            try {
                $sql_update = "UPDATE orders SET status = ?, tanggal_update_status = NOW() WHERE id = ?";
                $stmt_update = $db->prepare($sql_update);
                $stmt_update->bind_param("si", $new_status, $internal_order_id);
                $stmt_update->execute();
                
                if ($stmt_update->affected_rows == 0) {
                    throw new \Exception("Gagal update status pesanan.");
                }
                $stmt_update->close();

                $db->commit();
                http_response_code(200);
                error_log("Status untuk order ID " . $order_id_from_midtrans . " berhasil diupdate ke " . $new_status);

            } catch (\Exception $e) {
                $db->rollback();
                http_response_code(500);
                error_log("Gagal memproses notifikasi untuk order ID: " . $order_id_from_midtrans . ". Error: " . $e->getMessage());
            }
        } else {
            http_response_code(200);
            error_log("Notifikasi duplikat atau tidak memerlukan aksi untuk order ID: " . $order_id_from_midtrans);
        }
    }
}
