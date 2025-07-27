<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../ADMIN MENU/db_connect.php';

$page_title = "Keranjang Belanja Anda";
$keranjang_items_detail = [];
$total_harga_keranjang = 0;

if (isset($_SESSION['keranjang']) && !empty($_SESSION['keranjang'])) {
    // Kumpulkan semua id_produk unik dari keranjang untuk diambil detailnya
    $array_id_produk_unik = [];
    foreach ($_SESSION['keranjang'] as $cart_key => $item_data) {
        if (isset($item_data['id_produk'])) {
            $array_id_produk_unik[$item_data['id_produk']] = $item_data['id_produk']; // Gunakan ID produk sebagai kunci untuk keunikan
        }
    }
    $array_id_produk_unik = array_values($array_id_produk_unik); // Dapatkan nilai ID produknya saja

    if (!empty($array_id_produk_unik)) {
        $placeholders = implode(',', array_fill(0, count($array_id_produk_unik), '?'));
        $types = str_repeat('i', count($array_id_produk_unik));

        // Ambil detail produk dari database berdasarkan ID unik di keranjang
        $sql_keranjang = "SELECT id, name, price, image, stock FROM product WHERE id IN ($placeholders)";
        if ($stmt_keranjang = $conn->prepare($sql_keranjang)) {
            $stmt_keranjang->bind_param($types, ...$array_id_produk_unik);
            $stmt_keranjang->execute();
            $result_produk_db = $stmt_keranjang->get_result();

            $produk_details_map = [];
            while ($produk_row = $result_produk_db->fetch_assoc()) {
                $produk_details_map[$produk_row['id']] = $produk_row;
            }
            $stmt_keranjang->close();

            // Sekarang bangun array $keranjang_items_detail dengan data lengkap
            foreach ($_SESSION['keranjang'] as $cart_key => $item_data) {
                $id_produk_item = $item_data['id_produk'];
                if (isset($produk_details_map[$id_produk_item])) {
                    $produk_info = $produk_details_map[$id_produk_item];
                    $subtotal = $produk_info['price'] * $item_data['kuantitas'];

                    $keranjang_items_detail[$cart_key] = [ // Gunakan cart_key sebagai kunci di sini juga
                        'id' => $id_produk_item, // id produk
                        'cart_key' => $cart_key, // Simpan cart_key untuk form update/hapus
                        'name' => $produk_info['name'],
                        'price' => $produk_info['price'],
                        'image' => $produk_info['image'],
                        'stock' => $produk_info['stock'],
                        'kuantitas' => $item_data['kuantitas'],
                        'ukuran' => $item_data['ukuran'],
                        'subtotal' => $subtotal
                    ];
                    $total_harga_keranjang += $subtotal;
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> - Casual Steps</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
    <style>
        .cart-item-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
        }
    </style>
</head>

<body class="d-flex flex-column min-vh-100">
    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top shadow-sm">
            <div class="container">
                <a class="navbar-brand fw-bold" href="index.php">CASUAL STEPS</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"><span class="navbar-toggler-icon"></span></button>
                <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item"><a class="nav-link" href="index.php">HOME</a></li>
                        <li class="nav-item"><a class="nav-link" href="produk.php">PRODUCT</a></li>
                        <li class="nav-item"><a class="nav-link" href="about.php">ABOUT</a></li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($current_page_base == 'promo.php') ? 'active' : ''; ?>" href="promo.php">SALE</a>
                        </li>
                    </ul>
                </div>
                <div class="d-flex align-items-center">
                    <form action="produk.php" method="GET" class="d-flex me-3" role="search">
                        <input class="form-control form-control-sm me-2" type="search" name="keyword_pencarian" placeholder="Cari produk..." aria-label="Cari produk" value="<?php echo htmlspecialchars($_GET['keyword_pencarian'] ?? ''); ?>">
                        <button class="btn btn-outline-primary btn-sm" type="submit"><i class="fas fa-search"></i></button>
                    </form>
                    <a href="keranjang.php" class="position-relative me-3 text-dark nav-link <?php echo ($current_page_base == 'keranjang.php' || $current_page_base == 'checkout.php') ? 'active' : ''; ?>" title="Keranjang Belanja">
                        <i class="fas fa-shopping-bag"></i>
                        <?php
                        $jumlah_total_kuantitas_keranjang = 0;
                        if (isset($_SESSION['keranjang']) && is_array($_SESSION['keranjang'])) {
                            foreach ($_SESSION['keranjang'] as $cart_key_nav => $item_data_nav) {
                                if (is_array($item_data_nav) && isset($item_data_nav['kuantitas'])) {
                                    $jumlah_total_kuantitas_keranjang += (int)$item_data_nav['kuantitas'];
                                }
                            }
                        }
                        if ($jumlah_total_kuantitas_keranjang > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                <?php echo $jumlah_total_kuantitas_keranjang; ?>
                                <span class="visually-hidden">item di keranjang</span>
                            </span>
                        <?php endif; ?>
                    </a>

                    <?php if (isset($_SESSION['user_loggedin']) && $_SESSION['user_loggedin'] === true): ?>
                    <?php else: ?>
                    <?php endif; ?>
                </div>
                <?php if (isset($_SESSION['user_loggedin']) && $_SESSION['user_loggedin'] === true): ?>
                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-dark" href="#" id="navbarDropdownUser" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user me-1"></i> <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Akun Saya'); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownUser">
                            <li><a class="dropdown-item" href="akun_saya.php">Profil Saya</a></li>
                            <li><a class="dropdown-item" href="riwayat_pesanan.php">Riwayat Pesanan</a></li>
                            <li><a class="dropdown-item" href="alamat_saya.php">Alamat Saya</a></li>
                            <li><a class="dropdown-item" href="ubah_password.php">Ubah Password</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="logout_pelanggan.php">Logout</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="login_pelanggan.php" class="nav-link text-dark me-2">Login</a>
                    <a href="register.php" class="btn btn-primary btn-sm">Register</a>
                <?php endif; ?>
            </div>
            </div>
        </nav>
    </header>

    <main class="container py-5" style="margin-top: 120px;">
        <h1 class="mb-4"><?php echo htmlspecialchars($page_title); ?></h1>


        <?php if (!empty($keranjang_items_detail)): ?>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th scope="col" colspan="2">Produk</th>
                            <th scope="col">Ukuran</th>
                            <th scope="col">Harga Satuan</th>
                            <th scope="col" class="text-center">Kuantitas</th>
                            <th scope="col" class="text-end">Subtotal</th>
                            <th scope="col" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($keranjang_items_detail as $cart_key => $item): ?>
                            <tr>
                                <td style="width: 100px;">
                                    <?php
                                    $image_name_cart = htmlspecialchars($item['image'] ?? '');
                                    $image_path_cart = "../ADMIN MENU/uploads/produk/" . $image_name_cart;
                                    $placeholder_path_cart = "../ADMIN MENU/placeholder_image.png";

                                    if (!empty($image_name_cart) && file_exists($image_path_cart)):
                                    ?>
                                        <img src="<?php echo $image_path_cart; ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="img-thumbnail cart-item-img">
                                    <?php else: ?>
                                        <img src="<?php echo $placeholder_path_cart; ?>" alt="Gambar tidak tersedia" class="img-thumbnail cart-item-img">
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="detail_produk.php?id=<?php echo $item['id']; ?>" class="text-dark text-decoration-none">
                                        <?php echo htmlspecialchars($item['name']); ?>
                                    </a>
                                </td>
                                <td><?php echo !empty($item['ukuran']) ? htmlspecialchars($item['ukuran']) : '-'; ?></td>
                                <td>Rp <?php echo number_format($item['price'], 0, ',', '.'); ?></td>
                                <td class="text-center" style="min-width: 170px;">
                                    <form action="update_kuantitas_keranjang_proses.php" method="POST" class="d-inline-flex align-items-center justify-content-center">
                                        <input type="hidden" name="cart_item_key" value="<?php echo htmlspecialchars($cart_key); ?>">
                                        <input type="number"
                                            name="kuantitas"
                                            value="<?php echo htmlspecialchars($item['kuantitas']); ?>"
                                            min="0"
                                            max="<?php echo htmlspecialchars($item['stock']); ?>"
                                            class="form-control form-control-sm me-2"
                                            style="width: 70px;"
                                            required>
                                        <button type="submit" class="btn btn-sm btn-outline-primary" title="Update Kuantitas">
                                            <i class="fas fa-sync-alt"></i>
                                        </button>
                                    </form>
                                </td>
                                <td class="text-end">Rp <?php echo number_format($item['subtotal'], 0, ',', '.'); ?></td>
                                <td class="text-center">
                                    <a href="hapus_item_keranjang_proses.php?cart_item_key=<?php echo urlencode($cart_key); ?>" class="btn btn-sm btn-outline-danger" title="Hapus item" onclick="return confirm('Apakah Anda yakin ingin menghapus item ini dari keranjang?');">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5" class="text-end fw-bold border-top-0">Total Harga</td>
                            <td class="text-end fw-bold border-top-0">Rp <?php echo number_format($total_harga_keranjang, 0, ',', '.'); ?></td>
                            <td class="border-top-0"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-4">
                <a href="produk.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i> Lanjut Belanja</a>
                <a href="checkout.php?mode=cart" class="btn btn-primary btn-lg">Lanjut ke Checkout <i class="fas fa-arrow-right ms-2"></i></a>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center" role="alert">
                Keranjang belanja Anda masih kosong. <a href="produk.php" class="alert-link">Mulai belanja sekarang!</a>
            </div>
        <?php endif; ?>
    </main>

    <footer class="bg-dark text-white py-5 mt-auto">
        <div class="container">
            <hr class="my-4" />
            <div class="text-center">
                <p class="mb-0">© 2025 CasualSteps. All rights reserved.</p>
            </div>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
<?php
if (isset($conn) && $conn instanceof mysqli) {
    $conn->close();
}
?>