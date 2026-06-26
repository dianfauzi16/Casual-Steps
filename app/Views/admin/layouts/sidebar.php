<!-- Sidebar -->
<div class="sidebar p-3" style="width: 250px;">
    <div class="text-center mb-4 mt-2">
        <h4 class="fw-bold m-0"><i class="fas fa-shoe-prints me-2"></i>Admin</h4>
    </div>
    <hr class="border-secondary">
    <ul class="nav flex-column">
        <li class="nav-item">
            <a href="<?= BASE_URL ?>index.php?url=AdminDashboard/index" class="sidebar-link <?= (isset($_GET['url']) && strpos($_GET['url'], 'AdminDashboard') !== false) ? 'active' : ''; ?>">
                <i class="fas fa-home fa-fw me-2"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= BASE_URL ?>index.php?url=AdminProduct/index" class="sidebar-link <?= (isset($_GET['url']) && strpos($_GET['url'], 'AdminProduct') !== false) ? 'active' : ''; ?>">
                <i class="fas fa-box fa-fw me-2"></i> Produk
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= BASE_URL ?>index.php?url=AdminCategory/index" class="sidebar-link <?= (isset($_GET['url']) && strpos($_GET['url'], 'AdminCategory') !== false) ? 'active' : ''; ?>">
                <i class="fas fa-tags fa-fw me-2"></i> Kategori
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= BASE_URL ?>index.php?url=AdminOrder/index" class="sidebar-link <?= (isset($_GET['url']) && strpos($_GET['url'], 'AdminOrder') !== false) ? 'active' : ''; ?>">
                <i class="fas fa-shopping-cart fa-fw me-2"></i> Pesanan
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= BASE_URL ?>index.php?url=AdminCustomer/index" class="sidebar-link <?= (isset($_GET['url']) && strpos($_GET['url'], 'AdminCustomer') !== false) ? 'active' : ''; ?>">
                <i class="fas fa-users fa-fw me-2"></i> Pelanggan
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= BASE_URL ?>index.php?url=AdminReport/index" class="sidebar-link <?= (isset($_GET['url']) && strpos($_GET['url'], 'AdminReport') !== false) ? 'active' : ''; ?>">
                <i class="fas fa-chart-line fa-fw me-2"></i> Laporan
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= BASE_URL ?>index.php?url=AdminSetting/index" class="sidebar-link <?= (isset($_GET['url']) && strpos($_GET['url'], 'AdminSetting') !== false) ? 'active' : ''; ?>">
                <i class="fas fa-cog fa-fw me-2"></i> Pengaturan
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= BASE_URL ?>index.php?url=AdminContact/index" class="sidebar-link <?= (isset($_GET['url']) && strpos($_GET['url'], 'AdminContact') !== false) ? 'active' : ''; ?>">
                <i class="fas fa-envelope fa-fw me-2"></i> Kontak Masuk
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= BASE_URL ?>index.php?url=AdminPromo/index" class="sidebar-link <?= (isset($_GET['url']) && strpos($_GET['url'], 'AdminPromo') !== false) ? 'active' : ''; ?>">
                <i class="fas fa-gift fa-fw me-2"></i> Promo
            </a>
        </li>
        <li class="nav-item mt-4">
            <a href="<?= BASE_URL ?>index.php?url=AdminAuth/logout" class="sidebar-link text-danger">
                <i class="fas fa-sign-out-alt fa-fw me-2"></i> Keluar
            </a>
        </li>
    </ul>
</div>

<!-- Main Content Wrapper -->
<div class="main-content d-flex flex-column">
    <!-- Top Header -->
    <header class="top-header d-flex justify-content-between align-items-center">
        <h5 class="m-0 fw-semibold"><?= isset($page_title) ? htmlspecialchars($page_title) : 'Halaman Admin'; ?></h5>
        <div class="user-info d-flex align-items-center">
            <span class="me-3 text-muted">Halo, <?= htmlspecialchars($_SESSION['admin_username'] ?? 'Admin'); ?></span>
            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px;">
                <i class="fas fa-user"></i>
            </div>
        </div>
    </header>
    
    <!-- Content Area -->
    <main class="p-4 flex-grow-1">
