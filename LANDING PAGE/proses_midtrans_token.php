<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Sertakan autoloader Composer
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../ADMIN MENU/db_connect.php';

header('Content-Type: application/json');

// --- Konfigurasi Midtrans ---
// Ganti dengan kunci API Anda
\Midtrans\Config::$serverKey = 'SB-Mid-server-7rXZtaLcNc8M3I9VZYtj9eoE';
\Midtrans\Config::$isProduction = false; // Set ke true jika sudah live
\Midtrans\Config::$isSanitized = true;
\Midtrans\Config::$is3ds = true;

// --- Validasi dan Ambil Data ---
if (!isset($_SESSION['user_loggedin']) || $_SESSION['user_loggedin'] !== true) {
    echo json_encode(['error' => 'Anda harus login untuk melanjutkan.']);
    exit;
}



$user_id_pelanggan = $_SESSION['user_id'];
$nama_pelanggan = trim($_POST['nama_pelanggan'] ?? '');
$email_pelanggan = trim($_POST['email_pelanggan'] ?? '');
$telepon_pelanggan = trim($_POST['telepon_pelanggan'] ?? '');
$alamat_pengiriman_lengkap = trim($_POST['alamat_pengiriman_lengkap'] ?? '');
$kota_pengiriman = trim($_POST['kota_pengiriman'] ?? '');
$kode_pos_pengiriman = trim($_POST['kode_pos_pengiriman'] ?? '');
$catatan_pelanggan = trim($_POST['catatan_pelanggan'] ?? '');

$checkout_type = $_POST['checkout_type'] ?? 'cart_checkout';

// --- Validasi Form (Sederhana, bisa diperkuat) ---
if (empty($nama_pelanggan) || empty($telepon_pelanggan) || empty($alamat_pengiriman_lengkap)) {
    echo json_encode(['error' => 'Mohon lengkapi semua field yang wajib diisi.']);
    exit;
}

// --- Ambil Item dan Hitung Total (Logika dari proses_checkout.php) ---
$total_harga_final = 0;
$item_details_midtrans = [];
$items_to_save_in_db = []; // Array baru untuk menyimpan item ke DB
$items_for_stock_check = []; // Array untuk validasi stok

try {
    // Mulai transaksi database
    $conn->begin_transaction();

    // Kumpulkan item dari session ke array untuk diproses
    if ($checkout_type === 'direct_checkout' && isset($_SESSION['direct_checkout_item'])) {
        $items_for_stock_check[] = $_SESSION['direct_checkout_item'];
    } elseif ($checkout_type === 'cart_checkout' && isset($_SESSION['keranjang'])) {
        $items_for_stock_check = array_values($_SESSION['keranjang']);
    }

    if (empty($items_for_stock_check)) {
        throw new Exception('Tidak ada item untuk di-checkout.');
    }

    // Validasi stok dan kumpulkan detail produk dalam satu query
    $product_ids = array_map(function ($item) {
        return $item['id_produk'];
    }, $items_for_stock_check);
    $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
    $types = str_repeat('i', count($product_ids));
    $sql_products = "SELECT id, name, price, stock FROM product WHERE id IN ($placeholders) FOR UPDATE"; // FOR UPDATE untuk mengunci baris
    $stmt_products = $conn->prepare($sql_products);
    $stmt_products->bind_param($types, ...$product_ids);
    $stmt_products->execute();
    $result_products = $stmt_products->get_result();
    $products_from_db = [];
    while ($row = $result_products->fetch_assoc()) {
        $products_from_db[$row['id']] = $row;
    }
    $stmt_products->close();

    // Proses setiap item, validasi stok, dan siapkan data
    foreach ($items_for_stock_check as $item) {
        $product_id = $item['id_produk'];
        $quantity = $item['kuantitas'];

        if (!isset($products_from_db[$product_id])) {
            throw new Exception("Produk dengan ID {$product_id} tidak ditemukan.");
        }

        $prod_data = $products_from_db[$product_id];

        if ($quantity > $prod_data['stock']) {
            throw new Exception("Stok untuk produk '{$prod_data['name']}' tidak mencukupi. Tersisa: {$prod_data['stock']}.");
        }

        $subtotal = $prod_data['price'] * $quantity;
        $total_harga_final += $subtotal;

        $item_details_midtrans[] = [
            'id'       => $product_id,
            'price'    => $prod_data['price'],
            'quantity' => $quantity,
            'name'     => $prod_data['name'] . (!empty($item['ukuran']) ? ' (Ukuran: ' . $item['ukuran'] . ')' : '')
        ];

        $items_to_save_in_db[] = [
            'id_produk' => $product_id,
            'nama_produk' => $prod_data['name'],
            'kuantitas' => $quantity,
            'ukuran' => $item['ukuran'],
            'harga_satuan' => $prod_data['price']
        ];
    }

    // --- BUAT PESANAN DI DATABASE DENGAN STATUS 'pending' ---
    $status_pesanan_awal = 'pending';
    $metode_pembayaran = 'Payment Gateway';
    $sql_insert_order = "INSERT INTO orders (user_id, nama_pelanggan, email_pelanggan, telepon_pelanggan, alamat_pengiriman_lengkap, kota_pengiriman, kode_pos_pengiriman, total_price, metode_pembayaran, status, catatan_pelanggan, tanggal_pesanan) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    $stmt_order = $conn->prepare($sql_insert_order);
    $stmt_order->bind_param("issssssdsss", $user_id_pelanggan, $nama_pelanggan, $email_pelanggan, $telepon_pelanggan, $alamat_pengiriman_lengkap, $kota_pengiriman, $kode_pos_pengiriman, $total_harga_final, $metode_pembayaran, $status_pesanan_awal, $catatan_pelanggan);
    $stmt_order->execute();
    $new_order_id_int = $conn->insert_id;
    if (!$new_order_id_int) {
        throw new Exception("Gagal membuat pesanan di database.");
    }
    $stmt_order->close();

    $midtrans_order_id_string = 'CS-' . $new_order_id_int . '-' . time();
    $sql_update_midtrans_id = "UPDATE orders SET midtrans_order_id = ? WHERE id = ?";
    $stmt_update_midtrans_id = $conn->prepare($sql_update_midtrans_id);
    $stmt_update_midtrans_id->bind_param("si", $midtrans_order_id_string, $new_order_id_int);
    $stmt_update_midtrans_id->execute();
    $stmt_update_midtrans_id->close();

    // --- SIMPAN SETIAP ITEM KE TABEL order_items ---
    $sql_insert_item = "INSERT INTO order_items (id_order, id_produk, nama_produk_saat_pesan, quantity, size, harga_satuan_saat_pesan, subtotal_item) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt_item = $conn->prepare($sql_insert_item);
    foreach ($items_to_save_in_db as $item_db) {
        $subtotal_item = $item_db['harga_satuan'] * $item_db['kuantitas'];
        $stmt_item->bind_param("iisisdd", $new_order_id_int, $item_db['id_produk'], $item_db['nama_produk'], $item_db['kuantitas'], $item_db['ukuran'], $item_db['harga_satuan'], $subtotal_item);
        $stmt_item->execute();
    }
    $stmt_item->close();

    // --- LANGSUNG KURANGI STOK PRODUK ---
    $sql_update_stock = "UPDATE product SET stock = stock - ? WHERE id = ? AND stock >= ?";
    $stmt_update_stock = $conn->prepare($sql_update_stock);
    if (!$stmt_update_stock) {
        throw new Exception("Gagal mempersiapkan statement update stok: " . $conn->error);
    }
    foreach ($items_to_save_in_db as $item_to_reduce) {
        $stmt_update_stock->bind_param("iii", $item_to_reduce['kuantitas'], $item_to_reduce['id_produk'], $item_to_reduce['kuantitas']);
        if (!$stmt_update_stock->execute()) {
            throw new Exception("Gagal mengurangi stok untuk produk: " . $item_to_reduce['nama_produk'] . ". Error: " . $stmt_update_stock->error);
        }
        if ($stmt_update_stock->affected_rows == 0) {
            throw new Exception("Gagal mengurangi stok (stok tidak cukup saat update) untuk produk: " . $item_to_reduce['nama_produk']);
        }
    }
    $stmt_update_stock->close();

    // --- Siapkan Parameter untuk Midtrans ---
    $params = [
        'transaction_details' => [
            'order_id' => $midtrans_order_id_string,
            'gross_amount' => $total_harga_final,
        ],
        'customer_details'    => [
            'first_name' => $nama_pelanggan,
            'email'      => $email_pelanggan,
            'phone'      => $telepon_pelanggan,
        ],
        'item_details'        => $item_details_midtrans,
    ];


    // Dapatkan Snap Token
    $snapToken = \Midtrans\Snap::getSnapToken($params);

    // Bersihkan session keranjang/direct checkout setelah berhasil membuat order
    if ($checkout_type === 'direct_checkout') {
        unset($_SESSION['direct_checkout_item']);
    } else {
        unset($_SESSION['keranjang']);
    }

    echo json_encode(['token' => $snapToken]);
    $conn->commit();
} catch (Exception $e) {
    // Jika terjadi error di mana pun, rollback transaksi
    $conn->rollback();
    echo json_encode(['error' => $e->getMessage()]);
} finally {
    // Selalu tutup koneksi
    if (isset($conn)) {
        $conn->close();
    }
}
