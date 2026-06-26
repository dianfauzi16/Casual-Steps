<?php

namespace App\Controllers;

class AdminReportController extends AdminBaseController {
    
    public function index() {
        $reportModel = $this->model('ReportModel');
        
        // 1. Ambil Statistik Umum
        $stats = $reportModel->getGeneralStats();
        
        // 2. Ambil Produk Terlaris
        $topProducts = $reportModel->getTopProducts();
        
        // 3. Logika Filter Periode
        $salesPeriod = null;
        $activeTab = 'ringkasan';
        $filterStartDate = '';
        $filterEndDate = '';
        $error = '';
        
        if (isset($_GET['tab'])) {
            $activeTab = $_GET['tab'];
        }

        if (isset($_GET['tanggal_mulai']) && isset($_GET['tanggal_akhir'])) {
            $filterStartDate = trim($_GET['tanggal_mulai']);
            $filterEndDate = trim($_GET['tanggal_akhir']);
            
            // Validasi format tanggal Y-m-d
            $d_mulai = \DateTime::createFromFormat('Y-m-d', $filterStartDate);
            $d_akhir = \DateTime::createFromFormat('Y-m-d', $filterEndDate);
            
            if ($d_mulai && $d_mulai->format('Y-m-d') === $filterStartDate &&
                $d_akhir && $d_akhir->format('Y-m-d') === $filterEndDate &&
                $d_mulai <= $d_akhir) {
                
                $salesPeriod = $reportModel->getSalesByPeriod($filterStartDate, $filterEndDate);
                $activeTab = 'penjualan_periode';
            } else {
                $error = "Format tanggal tidak valid atau rentang tanggal salah.";
                $activeTab = 'penjualan_periode';
            }
        }
        
        $data = [
            'page_title' => 'Laporan Toko',
            'stats' => $stats,
            'topProducts' => $topProducts,
            'salesPeriod' => $salesPeriod,
            'activeTab' => $activeTab,
            'filterStartDate' => $filterStartDate,
            'filterEndDate' => $filterEndDate,
            'error' => $error
        ];
        
        $this->renderAdminView('admin/report/index', $data);
    }
}
