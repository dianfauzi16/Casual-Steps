<?php

namespace App\Controllers;

/**
 * Controller untuk mengelola Dashboard Admin
 * Berfungsi untuk mengumpulkan data statistik penting dari database dan menampilkan ringkasan performa toko.
 */
class AdminDashboardController extends AdminBaseController {
    
    /**
     * Menampilkan halaman dashboard utama admin
     * Memuat ringkasan jumlah pelanggan, pesanan, pendapatan, produk, pesanan terbaru, dan produk stok tipis.
     */
    public function index() {
        // Menginisialisasi AdminModel untuk memanggil fungsi-fungsi statistik
        $adminModel = $this->model('AdminModel');
        
        // Menyiapkan data yang dibutuhkan untuk view Dashboard
        $data = [
            'page_title' => 'Dashboard',
            'total_customers' => $adminModel->getTotalCustomers(),       // Total pelanggan terdaftar
            'total_orders' => $adminModel->getTotalOrders(),             // Total seluruh pesanan masuk
            'total_revenue' => $adminModel->getTotalRevenue(),           // Total pendapatan dari pesanan yang sukses/selesai
            'total_products' => $adminModel->getTotalProducts(),         // Total jenis produk terdaftar
            'recent_orders' => $adminModel->getRecentOrders(5),          // Mengambil 5 pesanan terbaru
            'low_stock_products' => $adminModel->getLowStockProducts(1)   // Mengambil produk yang stoknya menipis (<= 1)
        ];
        
        // Me-render view dashboard admin menggunakan framework helper renderAdminView
        $this->renderAdminView('admin/dashboard/index', $data);
    }
}

