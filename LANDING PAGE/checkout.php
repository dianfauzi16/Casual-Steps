<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../ADMIN MENU/db_connect.php';

$page_title = "Checkout";
$keranjang_items_detail = [];
$total_harga_keranjang = 0;
$current_page_base = basename($_SERVER['PHP_SELF']); // Untuk active link
$is_direct_checkout = false;

// Tentukan mode checkout berdasarkan parameter URL. Default ke 'cart'.
$checkout_mode = $_GET['mode'] ?? 'cart';

// Jika mode adalah 'cart' (dari halaman keranjang), kita harus membersihkan sesi 'direct_checkout_item'
// untuk memastikan item dari keranjang yang diproses.
if ($checkout_mode === 'cart') {
    unset($_SESSION['direct_checkout_item']);
}

// Sekarang, proses item berdasarkan mode yang sudah ditentukan.
if ($checkout_mode === 'direct' && isset($_SESSION['direct_checkout_item']) && !empty($_SESSION['direct_checkout_item'])) {
    $is_direct_checkout = true;
    $direct_item = $_SESSION['direct_checkout_item'];

    $sql_direct_item_detail = "SELECT id, name, price, image, stock FROM product WHERE id = ?";
    if ($stmt_direct_item = $conn->prepare($sql_direct_item_detail)) {
        $stmt_direct_item->bind_param("i", $direct_item['id_produk']);
        $stmt_direct_item->execute();
        $result_direct_item = $stmt_direct_item->get_result();
        if ($product_data = $result_direct_item->fetch_assoc()) {
            // Validasi stok untuk direct checkout item
            if ($direct_item['kuantitas'] > $product_data['stock']) {
                $_SESSION['error_message_checkout'] = "Maaf, stok untuk produk '" . htmlspecialchars($product_data['name']) . "' tidak mencukupi (diminta: " . $direct_item['kuantitas'] . ", tersedia: " . $product_data['stock'] . ").";
                // Hapus item direct checkout agar tidak dicoba lagi tanpa intervensi
                unset($_SESSION['direct_checkout_item']);
                header('Location: detail_produk.php?id=' . $direct_item['id_produk']);
                exit;
            }
            $subtotal = $product_data['price'] * $direct_item['kuantitas'];
            $keranjang_items_detail['direct_checkout'] = [ // Gunakan kunci unik
                'id' => $product_data['id'],
                'name' => $product_data['name'],
                'price' => $product_data['price'],
                'image' => $product_data['image'],
                'kuantitas' => $direct_item['kuantitas'],
                'ukuran' => $direct_item['ukuran'],
                'subtotal' => $subtotal
            ];
            $total_harga_keranjang = $subtotal;
        } else {
            $_SESSION['error_message_checkout'] = "Produk untuk 'Beli Sekarang' tidak ditemukan.";
            unset($_SESSION['direct_checkout_item']); // Hapus item yang bermasalah
            header('Location: produk.php');
            exit;
        }
        $stmt_direct_item->close();
    } else {
        $_SESSION['error_message_checkout'] = "Terjadi kesalahan saat memuat detail produk 'Beli Sekarang'.";
        unset($_SESSION['direct_checkout_item']);
        header('Location: produk.php');
        exit;
    }
} else {
    // Ini adalah alur untuk keranjang belanja biasa (mode=cart)
    // atau sebagai fallback jika mode=direct tapi session-nya kosong.
    $is_direct_checkout = false;
    // Logika untuk keranjang reguler
    if (!isset($_SESSION['keranjang']) || empty($_SESSION['keranjang'])) {
        $_SESSION['pesan_notifikasi_keranjang'] = "Keranjang belanja Anda kosong. Silakan tambahkan produk terlebih dahulu.";
        $_SESSION['tipe_notifikasi_keranjang'] = "warning";
        header('Location: keranjang.php');
        exit;
    }

    $array_id_produk_unik = [];
    foreach ($_SESSION['keranjang'] as $cart_item_data) {
        if (isset($cart_item_data['id_produk'])) {
            $array_id_produk_unik[$cart_item_data['id_produk']] = $cart_item_data['id_produk'];
        }
    }
    $array_id_produk_unik = array_values($array_id_produk_unik);

    if (!empty($array_id_produk_unik)) {
        $placeholders = implode(',', array_fill(0, count($array_id_produk_unik), '?'));
        $types = str_repeat('i', count($array_id_produk_unik));

        if (!empty($placeholders)) {
            $sql_checkout_items = "SELECT id, name, price, image, stock FROM product WHERE id IN ($placeholders)"; // Tambah stock
            if ($stmt_checkout_items = $conn->prepare($sql_checkout_items)) {
                $stmt_checkout_items->bind_param($types, ...$array_id_produk_unik);
                $stmt_checkout_items->execute();
                $result_checkout_items = $stmt_checkout_items->get_result();

                $produk_details_map = [];
                while ($produk_row = $result_checkout_items->fetch_assoc()) {
                    $produk_details_map[$produk_row['id']] = $produk_row;
                }
                $stmt_checkout_items->close();
                foreach ($_SESSION['keranjang'] as $cart_key => $item_session_data) {
                    $id_p = $item_session_data['id_produk'];
                    if (isset($produk_details_map[$id_p])) {
                        $item_db_data = $produk_details_map[$id_p];

                        // Validasi stok untuk setiap item di keranjang
                        if ($item_session_data['kuantitas'] > $item_db_data['stock']) {
                            $_SESSION['error_message_checkout'] = "Stok untuk produk '" . htmlspecialchars($item_db_data['name']) . "' tidak mencukupi (diminta: " . $item_session_data['kuantitas'] . ", tersedia: " . $item_db_data['stock'] . "). Harap perbarui keranjang Anda.";
                            header('Location: keranjang.php'); // Arahkan ke keranjang untuk update
                            exit;
                        }

                        $kuantitas_di_keranjang = $item_session_data['kuantitas'];
                        $subtotal = $item_db_data['price'] * $kuantitas_di_keranjang;

                        $keranjang_items_detail[$cart_key] = [
                            'id' => $id_p,
                            'name' => $item_db_data['name'],
                            'price' => $item_db_data['price'],
                            'image' => $item_db_data['image'],
                            'kuantitas' => $kuantitas_di_keranjang,
                            'ukuran' => $item_session_data['ukuran'],
                            'subtotal' => $subtotal
                        ];
                        $total_harga_keranjang += $subtotal;
                    }
                }
            } else {
                $_SESSION['pesan_notifikasi_keranjang'] = "Keranjang Anda mengandung item yang tidak valid.";
                $_SESSION['tipe_notifikasi_keranjang'] = "warning";
                header('Location: keranjang.php');
                exit;
            }
        } else {
            $_SESSION['pesan_notifikasi_keranjang'] = "Keranjang Anda mengandung item yang tidak valid.";
            $_SESSION['tipe_notifikasi_keranjang'] = "warning";
            header('Location: keranjang.php');
            exit;
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
        .checkout-summary-img {
        width: 60px;  /* Lebar gambar */
        height: 60px; /* Tinggi gambar */
        object-fit: cover;
        border-radius: 0.375rem; /* Sedikit lengkungan di sudut */
        border: 1px solid #dee2e6; /* Border abu-abu tipis */
    }
    </style>
</head>

<body class="d-flex flex-column min-vh-100">
    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top shadow-sm">
            <div class="container">
                <a class="navbar-brand fw-bold" href="index.php">CASUAL STEPS</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavGlobal"><span class="navbar-toggler-icon"></span></button>
                <div class="collapse navbar-collapse justify-content-center" id="navbarNavGlobal">
                    <ul class="navbar-nav">
                        <li class="nav-item"><a class="nav-link" href="index.php">HOME</a></li>
                        <li class="nav-item"><a class="nav-link" href="about.php">ABOUT</a></li>
                        <li class="nav-item"><a class="nav-link" href="produk.php">PRODUCT</a></li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($current_page_base == 'promo.php') ? 'active' : ''; ?>" href="promo.php">SALE</a>
                        </li>
                    </ul>
                </div>
                <div class="d-flex align-items-center">
                    <form action="produk.php" method="GET" class="d-flex me-2" role="search">
                        <input class="form-control form-control-sm" type="search" name="keyword_pencarian" placeholder="Cari produk..." aria-label="Cari produk" value="<?php echo htmlspecialchars($_GET['keyword_pencarian'] ?? ''); ?>">
                        <button class="btn btn-outline-primary btn-sm ms-1" type="submit"><i class="fas fa-search"></i></button>
                    </form>
                    <a href="keranjang.php" class="position-relative me-3 text-dark nav-link <?php echo ($current_page_base == 'keranjang.php' || $current_page_base == 'checkout.php') ? 'active' : ''; ?>" title="Keranjang Belanja">
                        <i class="fas fa-shopping-bag fs-5"></i>
                        <?php
                        // INI BAGIAN YANG DIPERBAIKI
                        $jumlah_total_kuantitas_keranjang = 0;
                        if (isset($_SESSION['keranjang']) && is_array($_SESSION['keranjang'])) {
                            foreach ($_SESSION['keranjang'] as $cart_key_nav => $item_data_nav) {
                                if (is_array($item_data_nav) && isset($item_data_nav['kuantitas'])) {
                                    $jumlah_total_kuantitas_keranjang += (int)$item_data_nav['kuantitas'];
                                }
                            }
                        }
                        if ($jumlah_total_kuantitas_keranjang > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65em; padding: 0.3em 0.5em;">
                                <?php echo $jumlah_total_kuantitas_keranjang; ?>
                                <span class="visually-hidden">item di keranjang</span>
                            </span>
                        <?php endif; ?>
                    </a>
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

    <main class="container py-5" style="margin-top: 80px;">
        <h1 class="mb-4 text-center"><?php echo htmlspecialchars($page_title); ?></h1>

        <?php
        if (isset($_SESSION['checkout_errors']) && !empty($_SESSION['checkout_errors'])) {
            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Mohon perbaiki error berikut:</strong><ul class="mb-0">';
            foreach ($_SESSION['checkout_errors'] as $error) {
                echo '<li>' . htmlspecialchars($error) . '</li>';
            }
            echo '</ul><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
            unset($_SESSION['checkout_errors']);
        }
        if (isset($_SESSION['error_message_checkout'])) {
            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">' . htmlspecialchars($_SESSION['error_message_checkout']) . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
            unset($_SESSION['error_message_checkout']);
        }
        ?>

        <div class="row">
            <div class="col-lg-7 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Informasi Pengiriman</h4>
                    </div>
                    <div class="card-body">
                        <form action="proses_checkout.php" method="POST" id="formCheckout">
                            <?php if ($is_direct_checkout): ?>
                                <input type="hidden" name="checkout_type" value="direct_checkout">
                            <?php endif; ?>
                            <div class="mb-3">
                                <label for="nama_pelanggan" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nama_pelanggan" name="nama_pelanggan" required value="<?php echo htmlspecialchars($_SESSION['checkout_form_data']['nama_pelanggan'] ?? ($_SESSION['user_name'] ?? '')); ?>">
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="email_pelanggan" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email_pelanggan" name="email_pelanggan" value="<?php echo htmlspecialchars($_SESSION['checkout_form_data']['email_pelanggan'] ?? ($_SESSION['user_email'] ?? '')); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="telepon_pelanggan" class="form-label">Nomor Telepon <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control" id="telepon_pelanggan" name="telepon_pelanggan" required value="<?php echo htmlspecialchars($_SESSION['checkout_form_data']['telepon_pelanggan'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="alamat_pengiriman_lengkap" class="form-label">Alamat Lengkap Pengiriman <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="alamat_pengiriman_lengkap" name="alamat_pengiriman_lengkap" rows="3" required><?php echo htmlspecialchars($_SESSION['checkout_form_data']['alamat_pengiriman_lengkap'] ?? ''); ?></textarea>
                                <small class="form-text text-muted">Sertakan nama jalan, nomor rumah, RT/RW, kelurahan/desa, kecamatan.</small>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="kota_pengiriman" class="form-label">Kota/Kabupaten <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="kota_pengiriman" name="kota_pengiriman" required value="<?php echo htmlspecialchars($_SESSION['checkout_form_data']['kota_pengiriman'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="kode_pos_pengiriman" class="form-label">Kode Pos <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="kode_pos_pengiriman" name="kode_pos_pengiriman" required value="<?php echo htmlspecialchars($_SESSION['checkout_form_data']['kode_pos_pengiriman'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="catatan_pelanggan" class="form-label">Catatan Tambahan (Opsional)</label>
                                <textarea class="form-control" id="catatan_pelanggan" name="catatan_pelanggan" rows="2"><?php echo htmlspecialchars($_SESSION['checkout_form_data']['catatan_pelanggan'] ?? ''); ?></textarea>
                            </div>
                        </form>
                        <?php unset($_SESSION['checkout_form_data']); ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h4 class="mb-0">Ringkasan Pesanan Anda</h4>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($keranjang_items_detail)): ?>
                            <ul class="list-group list-group-flush mb-3">
                                <?php foreach ($keranjang_items_detail as $item_key => $item_value): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                        <div class="d-flex align-items-center">
                                            <?php
                                            // Persiapkan path gambar
                                            $image_name = htmlspecialchars($item_value['image'] ?? '');
                                            $image_path = "../ADMIN MENU/uploads/produk/" . $image_name;
                                            $placeholder_path = "../ADMIN MENU/placeholder_image.png"; // Fallback jika gambar tidak ada
                                            ?>
                                            <img src="<?php echo (!empty($image_name) && file_exists($image_path)) ? $image_path : $placeholder_path; ?>"
                                                alt="<?php echo htmlspecialchars($item_value['name']); ?>"
                                                class="checkout-summary-img me-3">
                                            <div>
                                                <h6 class="my-0 fw-bold"><?php echo htmlspecialchars($item_value['name']); ?></h6>
                                                <small class="text-muted">
                                                    Jumlah: <?php echo htmlspecialchars($item_value['kuantitas']); ?>
                                                    <?php if (!empty($item_value['ukuran'])): ?>
                                                        | Ukuran: <?php echo htmlspecialchars($item_value['ukuran']); ?>
                                                    <?php endif; ?>
                                                </small>
                                            </div>
                                        </div>
                                        <span class="text-muted">Rp <?php echo number_format($item_value['subtotal'], 0, ',', '.'); ?></span>
                                    </li>
                                <?php endforeach; ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0 fw-bold border-top pt-3">
                                    <span>Total (IDR)</span>
                                    <strong>Rp <?php echo number_format($total_harga_keranjang, 0, ',', '.'); ?></strong>
                                </li>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
                <!-- 
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h4 class="mb-0">Metode Pembayaran</h4>
                    </div>
                    <div class="card-body">
                        <p class="fw-bold">Transfer Bank Manual</p>
                        <p>Silakan lakukan pembayaran ke nomor rekening berikut:</p>
                        <ul>
                            <li>Bank ABC: 123-456-7890 a.n. Casual Steps</li>
                            <li>Bank XYZ: 987-654-3210 a.n. Casual Steps</li>
                        </ul>
                        <p><small class="text-muted">Pesanan Anda akan diproses setelah pembayaran dikonfirmasi.</small></p>
                    </div>
                </div> -->

                <div class="d-grid gap-2 mt-4">
                    <!-- Tombol ini tidak lagi submit form, tapi memicu JavaScript -->
                    <button type="button" id="pay-button" class="btn btn-primary btn-lg">
                        <i class="fas fa-shield-alt me-2"></i> Lanjut ke Pembayaran
                    </button>
                    <p class="text-center text-muted small mt-2">Pembayaran aman dan terenkripsi oleh Midtrans.</p>
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-dark text-white py-5 mt-auto">
        <div class="container">
            <hr class="my-4" />
            <div class="text-center">
                <p class="mb-0">© 2025 Casual Steps. All rights reserved.</p>
            </div>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- 1. Sertakan Library Snap.js dari Midtrans -->
    <!-- Ganti URL ke production saat live: https://app.midtrans.com/snap/snap.js -->
    <script type="text/javascript"
        src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="SB-Mid-client-60pVLv59Zh2pYo9C"></script>
    <!-- 2. Tambahkan JavaScript untuk memproses pembayaran -->
    <script type="text/javascript">
        // Ambil tombol pembayaran
        var payButton = document.getElementById('pay-button');
        payButton.addEventListener('click', function() {
            // Ambil data dari form checkout
            var checkoutForm = document.getElementById('formCheckout');
            var formData = new FormData(checkoutForm);

            // Kirim data form ke server Anda untuk mendapatkan token Midtrans
            fetch('proses_midtrans_token.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        // Tampilkan error jika ada
                        alert(data.error);
                    } else if (data.token) {
                        // Jika token diterima, buka pop-up pembayaran Midtrans
                        window.snap.pay(data.token, {
                            onSuccess: function(result) {
                                /* Anda bisa menangani hasil sukses di sini */
                                alert("Pembayaran berhasil!");
                                console.log(result);
                                window.location.href = 'payment_finish.php?order_id=' + result.order_id + '&status=success';
                            },
                            onPending: function(result) {
                                /* Anda bisa menangani hasil pending di sini */
                                alert("Menunggu pembayaran Anda!");
                                console.log(result);
                                window.location.href = 'payment_finish.php?order_id=' + result.order_id + '&status=pending';
                            },
                            onError: function(result) {
                                /* Anda bisa menangani error di sini */
                                alert("Pembayaran gagal!");
                                console.log(result);
                                window.location.href = 'payment_finish.php?order_id=' + result.order_id + '&status=error';
                            },
                            onClose: function() {
                                /* Anda bisa menangani jika pop-up ditutup sebelum pembayaran selesai */
                                alert('Anda menutup pop-up pembayaran sebelum menyelesaikan transaksi.');
                            }
                        });
                    }
                });
        });
    </script>
</body>

</html>
<?php
if (isset($conn) && $conn instanceof mysqli) {
    $conn->close();
}
?>