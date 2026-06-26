<?php

namespace App\Controllers;

class AdminDashboardController extends AdminBaseController {
    
    public function index() {
        $adminModel = $this->model('AdminModel');
        
        $data = [
            'page_title' => 'Dashboard',
            'total_customers' => $adminModel->getTotalCustomers(),
            'total_orders' => $adminModel->getTotalOrders(),
            'total_revenue' => $adminModel->getTotalRevenue(),
            'total_products' => $adminModel->getTotalProducts(),
            'recent_orders' => $adminModel->getRecentOrders(5),
            'low_stock_products' => $adminModel->getLowStockProducts(1)
        ];
        
        $this->renderAdminView('admin/dashboard/index', $data);
    }
}
