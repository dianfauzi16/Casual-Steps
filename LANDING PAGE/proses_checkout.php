<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../ADMIN MENU/db_connect.php';

// 1. Pastikan pengguna login
if (isset($_SESSION['user_loggedin']) && $_SESSION['user_loggedin'] === true && isset($_SESSION['user_id'])) {
    $user_id_pelanggan = $_SESSION['user_id'];
} else {
    $_SESSION['login_error'] = "Anda harus login untuk menyelesaikan pesanan.";
    // Simpan data form jika ada, agar bisa diisi ulang setelah login
    header('Location: login_pelanggan.php');
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Ambil dan validasi data dari form checkout
    $nama_pelanggan = trim($_POST['nama_pelanggan'] ?? '');
    // Tentukan tipe checkout berdasarkan input tersembunyi dari form checkout.php
    $checkout_type = $_POST['checkout_type'] ?? 'cart_checkout'; // Default ke cart_checkout jika tidak ada
    $email_pelanggan = trim($_POST['email_pelanggan'] ?? '');
    $telepon_pelanggan = trim($_POST['telepon_pelanggan'] ?? '');
    $alamat_pengiriman_lengkap = trim($_POST['alamat_pengiriman_lengkap'] ?? '');
    $kota_pengiriman = trim($_POST['kota_pengiriman'] ?? '');
    $kode_pos_pengiriman = trim($_POST['kode_pos_pengiriman'] ?? '');
    $catatan_pelanggan = trim($_POST['catatan_pelanggan'] ?? '');

    $metode_pembayaran = "Transfer Bank";
    $status_pesanan_awal = "Menunggu Pembayaran";

    $errors = [];
    // ... (Validasi form input tetap sama) ...
    if (empty($nama_pelanggan)) $errors[] = "Nama pelanggan wajib diisi.";
    if (empty($telepon_pelanggan)) $errors[] = "Nomor telepon wajib diisi.";
    if (empty($alamat_pengiriman_lengkap)) $errors[] = "Alamat pengiriman wajib diisi.";
    if (empty($kota_pengiriman)) $errors[] = "Kota pengiriman wajib diisi.";
    if (empty($kode_pos_pengiriman)) $errors[] = "Kode pos wajib diisi.";
    if (!empty($email_pelanggan) && !filter_var($email_pelanggan, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Format email tidak valid.";
    }

    if (!empty($errors)) {
        $_SESSION['checkout_errors'] = $errors;
        $_SESSION['checkout_form_data'] = $_POST;
        header('Location: checkout.php');
        exit;
    }

    // 2. Hitung ulang total harga & verifikasi stok berdasarkan struktur keranjang baru
    $total_harga_final = 0;
    $items_for_order_items_db = []; // Array untuk menyimpan data yang akan dimasukkan ke tabel order_items

    // Kumpulkan semua id_produk unik dari keranjang
    // Logika untuk mengambil item yang akan diproses berdasarkan tipe checkout
    if ($checkout_type === 'direct_checkout' && isset($_SESSION['direct_checkout_item']) && !empty($_SESSION['direct_checkout_item'])) {
        $direct_item_session = $_SESSION['direct_checkout_item'];
        $sql_item = "SELECT name, price, stock FROM product WHERE id = ?";
        if ($stmt_item_lookup = $conn->prepare($sql_item)) {
            $stmt_item_lookup->bind_param("i", $direct_item_session['id_produk']);
            $stmt_item_lookup->execute();
            $result_item_lookup = $stmt_item_lookup->get_result();
            if ($prod_data_db = $result_item_lookup->fetch_assoc()) {
                if ($direct_item_session['kuantitas'] > $prod_data_db['stock']) {
                    $_SESSION['error_message_checkout'] = "Maaf, stok untuk produk '" . htmlspecialchars($prod_data_db['name']) . "' baru saja habis atau berkurang. Silakan coba lagi.";
                    unset($_SESSION['direct_checkout_item']);
                    header('Location: detail_produk.php?id=' . $direct_item_session['id_produk']);
                    exit;
                }
                $items_for_order_items_db[] = [
                    'id_produk' => $direct_item_session['id_produk'],
                    'nama_produk_saat_pesan' => $prod_data_db['name'],
                    'kuantitas' => $direct_item_session['kuantitas'],
                    'size' => $direct_item_session['ukuran'],
                    'harga_satuan_saat_pesan' => $prod_data_db['price'],
                    'subtotal_item' => $prod_data_db['price'] * $direct_item_session['kuantitas']
                ];
                $total_harga_final = $prod_data_db['price'] * $direct_item_session['kuantitas'];
            } else {
                $_SESSION['error_message_checkout'] = "Produk yang ingin Anda beli tidak ditemukan.";
                unset($_SESSION['direct_checkout_item']);
                header('Location: produk.php');
                exit;
            }
            $stmt_item_lookup->close();
        } else {
            $_SESSION['error_message_checkout'] = "Kesalahan database saat verifikasi produk 'Beli Sekarang'.";
            header('Location: checkout.php');
            exit;
        }
    } elseif ($checkout_type === 'cart_checkout' && isset($_SESSION['keranjang']) && !empty($_SESSION['keranjang'])) {
            $array_id_produk_unik_checkout = [];
            foreach ($_SESSION['keranjang'] as $cart_item_data) {
                if (isset($cart_item_data['id_produk'])) {
                    $array_id_produk_unik_checkout[$cart_item_data['id_produk']] = $cart_item_data['id_produk'];
                }
            }
            $array_id_produk_unik_checkout = array_values($array_id_produk_unik_checkout);

            if (!empty($array_id_produk_unik_checkout)) {
                $placeholders_verify = implode(',', array_fill(0, count($array_id_produk_unik_checkout), '?'));
                $types_verify = str_repeat('i', count($array_id_produk_unik_checkout));

                $sql_verify_produk = "SELECT id, name, price, stock FROM product WHERE id IN ($placeholders_verify)";
                if ($stmt_verify = $conn->prepare($sql_verify_produk)) {
                    $stmt_verify->bind_param($types_verify, ...$array_id_produk_unik_checkout);
                    $stmt_verify->execute();
                    $result_produk_db = $stmt_verify->get_result();

                    $produk_details_map_checkout = [];
                    while ($produk_row_db = $result_produk_db->fetch_assoc()) {
                        $produk_details_map_checkout[$produk_row_db['id']] = $produk_row_db;
                    }
                    $stmt_verify->close();

                    foreach ($_SESSION['keranjang'] as $cart_key => $item_data_keranjang) {
                        $id_produk_current = $item_data_keranjang['id_produk'];
                        $kuantitas_pesan = $item_data_keranjang['kuantitas'];
                        $ukuran_pesan = $item_data_keranjang['ukuran']; 

                        if (!isset($produk_details_map_checkout[$id_produk_current])) {
                            $_SESSION['error_message_checkout'] = "Produk dengan ID " . $id_produk_current . " tidak ditemukan di database. Harap perbarui keranjang Anda.";
                            header('Location: keranjang.php');
                            exit;
                        }
                        $produk_db_info = $produk_details_map_checkout[$id_produk_current];

                        if ($kuantitas_pesan > $produk_db_info['stock']) {
                            $_SESSION['error_message_checkout'] = "Stok produk '" . htmlspecialchars($produk_db_info['name']) . ($ukuran_pesan ? " Ukuran " . htmlspecialchars($ukuran_pesan) : "") . "' tidak mencukupi (tersisa: " . $produk_db_info['stock'] . "). Harap perbarui keranjang Anda.";
                            header('Location: keranjang.php');
                            exit;
                        }

                        $subtotal = $produk_db_info['price'] * $kuantitas_pesan;
                        $total_harga_final += $subtotal;

                        $items_for_order_items_db[] = [
                            'id_produk' => $id_produk_current,
                            'nama_produk_saat_pesan' => $produk_db_info['name'],
                            'kuantitas' => $kuantitas_pesan,
                            'size' => $ukuran_pesan,
                            'harga_satuan_saat_pesan' => $produk_db_info['price'],
                            'subtotal_item' => $subtotal
                        ];
                    }
                } else {
                    $_SESSION['error_message_checkout'] = "Kesalahan database saat verifikasi item keranjang.";
                    header('Location: checkout.php');
                    exit;
                }
            } else { // Jika $array_id_produk_unik_checkout kosong (keranjang hanya berisi item tidak valid)
                $_SESSION['error_message_checkout'] = "Tidak ada item valid di keranjang Anda.";
                header('Location: keranjang.php');
                exit;
            }
    } else { // Jika tidak ada item sama sekali (direct checkout kosong dan keranjang kosong)
        $_SESSION['error_message_checkout'] = "Tidak ada item untuk di checkout.";
        header('Location: produk.php'); // Arahkan ke halaman produk
        exit;
    }

    // Pengecekan akhir jika $items_for_order_items_db masih kosong setelah semua logika
    if (empty($items_for_order_items_db)) {
        $_SESSION['error_message_checkout'] = "Tidak ada item untuk diproses. Keranjang atau item 'Beli Sekarang' Anda mungkin kosong atau bermasalah.";
        header('Location: keranjang.php'); // Arahkan ke keranjang jika tidak ada item
        exit;
    }

    $conn->begin_transaction();
    try {
        // 3. Simpan data ke tabel 'orders'
        $sql_insert_order = "INSERT INTO orders (user_id, nama_pelanggan, email_pelanggan, telepon_pelanggan, alamat_pengiriman_lengkap, kota_pengiriman, kode_pos_pengiriman, total_price, metode_pembayaran, status, catatan_pelanggan, tanggal_pesanan) 
                             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

        $stmt_order = $conn->prepare($sql_insert_order);
        if (!$stmt_order) {
            throw new Exception("Gagal mempersiapkan statement order: " . $conn->error);
        }
        $stmt_order->bind_param(
            "issssssdsss",
            $user_id_pelanggan,
            $nama_pelanggan,
            $email_pelanggan,
            $telepon_pelanggan,
            $alamat_pengiriman_lengkap,
            $kota_pengiriman,
            $kode_pos_pengiriman,
            $total_harga_final,
            $metode_pembayaran,
            $status_pesanan_awal,
            $catatan_pelanggan
        );
        $stmt_order->execute();
        $id_order_baru = $stmt_order->insert_id;
        
        if (!$id_order_baru) {
            $stmt_order->close(); // Tutup statement sebelum throw exception
            throw new Exception("Gagal menyimpan data pesanan utama (orders): " . $conn->error);
        }
        $stmt_order->close();

        // 4. Simpan setiap item produk ke tabel 'order_items' dan update stok
        $sql_insert_order_item = "INSERT INTO order_items (id_order, id_produk, nama_produk_saat_pesan, quantity, size, harga_satuan_saat_pesan, subtotal_item) 
                                  VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt_order_item = $conn->prepare($sql_insert_order_item);
        if (!$stmt_order_item) {
            throw new Exception("Gagal mempersiapkan statement order item: " . $conn->error);
        }

        $sql_update_stock = "UPDATE product SET stock = stock - ? WHERE id = ? AND stock >= ?"; // Tambahkan pengecekan stok >= kuantitas
        $stmt_update_stock = $conn->prepare($sql_update_stock);
        if (!$stmt_update_stock) {
            $stmt_order_item->close();
            throw new Exception("Gagal mempersiapkan statement update stok: " . $conn->error);
        }

        foreach ($items_for_order_items_db as $item_pesanan) {
            $stmt_order_item->bind_param(
                "iisisdd",
                $id_order_baru,
                $item_pesanan['id_produk'],
                $item_pesanan['nama_produk_saat_pesan'],
                $item_pesanan['kuantitas'],
                $item_pesanan['size'],
                $item_pesanan['harga_satuan_saat_pesan'],
                $item_pesanan['subtotal_item']
            );
            if (!$stmt_order_item->execute()) {
                throw new Exception("Gagal menyimpan item pesanan: " . $item_pesanan['nama_produk_saat_pesan'] . " - " . $stmt_order_item->error);
            }

            $stmt_update_stock->bind_param("iii", $item_pesanan['kuantitas'], $item_pesanan['id_produk'], $item_pesanan['kuantitas']);
            if (!$stmt_update_stock->execute()) {
                 throw new Exception("Gagal mengurangi stok (execute) untuk produk: " . $item_pesanan['nama_produk_saat_pesan'] . ". Error: " . $stmt_update_stock->error);
            }
            if ($stmt_update_stock->affected_rows <= 0) {
                throw new Exception("Gagal mengurangi stok (affected_rows) untuk produk: " . $item_pesanan['nama_produk_saat_pesan'] . ". Stok mungkin tidak cukup atau berubah.");
            }
        }
        $stmt_order_item->close();
        $stmt_update_stock->close();

        $conn->commit();
        // Bersihkan session yang relevan
        if ($checkout_type === 'direct_checkout') {
            unset($_SESSION['direct_checkout_item']);
        } else {
            $_SESSION['keranjang_sebelum_checkout'] = $_SESSION['keranjang']; 
            unset($_SESSION['keranjang']);
        }
        unset($_SESSION['checkout_form_data']);
        $_SESSION['id_order_sukses'] = $id_order_baru;
        header('Location: terima_kasih.php');
        exit;
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error_message_checkout'] = "Terjadi kesalahan saat memproses pesanan Anda: " . $e->getMessage();
        error_log("Checkout error (user: " . $user_id_pelanggan . "): " . $e->getMessage() . " | SQL Error: " . $conn->error);
        header('Location: checkout.php');
        exit;
    }
} else { // Jika bukan POST
    header('Location: index.php');
    exit;
}

// Selalu tutup koneksi di akhir skrip
if (isset($conn) && $conn instanceof mysqli) {
    $conn->close();
}