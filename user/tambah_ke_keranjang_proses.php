<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Inisialisasi keranjang jika belum ada di session
if (!isset($_SESSION['keranjang'])) {
    $_SESSION['keranjang'] = []; // Array asosiatif
}

$pesan_notifikasi = ""; 
$tipe_notifikasi = "";  

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_produk'])) {
    $id_produk = filter_var($_POST['id_produk'], FILTER_VALIDATE_INT);
    $kuantitas = isset($_POST['kuantitas']) && filter_var($_POST['kuantitas'], FILTER_VALIDATE_INT) && $_POST['kuantitas'] > 0 ? (int)$_POST['kuantitas'] : 1;
    
    // MODIFIKASI: Ambil ukuran terpilih. Bisa jadi string kosong jika tidak ada ukuran.
    $ukuran_terpilih = trim($_POST['ukuran_terpilih'] ?? '');

    if ($id_produk) {
        // Buat kunci unik untuk item keranjang berdasarkan ID produk dan ukuran
        // Jika tidak ada ukuran, kuncinya hanya berdasarkan ID produk (atau Anda bisa memutuskan untuk mewajibkan ukuran jika produk memilikinya)
        $cart_item_key = $id_produk;
        if (!empty($ukuran_terpilih)) {
            // Ganti karakter non-alfanumerik dari ukuran untuk kunci yang lebih aman, atau pastikan ukuran selalu aman
            $safe_size_key = preg_replace('/[^a-zA-Z0-9_-]/', '', $ukuran_terpilih);
            $cart_item_key .= "_" . $safe_size_key;
        }

        // Idealnya, lakukan pengecekan stok ke database di sini, terutama jika stok bergantung pada ukuran.
        // Untuk sekarang, kita sederhanakan.

        // Jika item (produk dengan ukuran spesifik) sudah ada di keranjang, tambahkan kuantitasnya
        if (isset($_SESSION['keranjang'][$cart_item_key])) {
            $_SESSION['keranjang'][$cart_item_key]['kuantitas'] += $kuantitas;
            // $pesan_notifikasi = "Kuantitas produk berhasil diperbarui di keranjang!";
        } else {
            // Jika item belum ada, tambahkan sebagai item baru
            $_SESSION['keranjang'][$cart_item_key] = [
                'id_produk' => $id_produk,
                'ukuran' => $ukuran_terpilih, // Simpan ukuran yang dipilih
                'kuantitas' => $kuantitas
                // Anda bisa juga menyimpan nama produk, harga, gambar di sini saat mengambil dari DB
                // untuk mempermudah tampilan di halaman keranjang nanti dan mengurangi query DB.
                // Namun, untuk sekarang, kita simpan ID, ukuran, dan kuantitas saja.
            ];
            // $pesan_notifikasi = "Produk berhasil ditambahkan ke keranjang!";
        }
        // $tipe_notifikasi = "success";
        
        // (Opsional) Pesan notifikasi bisa disimpan di session untuk ditampilkan di halaman sebelumnya
        // if(isset($_POST['nama_produk'])) { // Jika Anda mengirim nama produk juga dari form
        //     $_SESSION['pesan_notifikasi'] = htmlspecialchars($_POST['nama_produk']) . ($ukuran_terpilih ? " (Ukuran: " . htmlspecialchars($ukuran_terpilih) . ")" : "") . " telah ditambahkan ke keranjang.";
        //     $_SESSION['tipe_notifikasi'] = "success";
        // }


    } else {
        // $_SESSION['pesan_notifikasi'] = "ID Produk tidak valid.";
        // $_SESSION['tipe_notifikasi'] = "danger";
    }
} else {
    // $_SESSION['pesan_notifikasi'] = "Aksi tidak valid.";
    // $_SESSION['tipe_notifikasi'] = "danger";
}

// Arahkan pengguna kembali ke halaman tempat mereka mengklik tombol
$url_sebelumnya = $_SERVER['HTTP_REFERER'] ?? 'index.php';
header("Location: " . $url_sebelumnya);
exit;
?>