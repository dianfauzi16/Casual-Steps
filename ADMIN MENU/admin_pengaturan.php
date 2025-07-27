<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    header("location: admin_login.php");
    exit;
}

require_once 'db_connect.php'; // Path ke db_connect.php dari dalam ADMIN MENU
$page_title = "Pengaturan Situs";
$settings = [];
$error_message = ''; // Untuk pesan error umum (jika ada di luar proses POST)

// Fungsi untuk mengambil nilai pengaturan
function get_site_setting($conn_param, $key) {
    $setting_value = null;
    $sql = "SELECT setting_value FROM site_settings WHERE setting_key = ? LIMIT 1";
    if ($stmt = $conn_param->prepare($sql)) {
        $stmt->bind_param("s", $key);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $setting_value = $row['setting_value'];
            }
        } else {
            // error_log("Gagal execute get_site_setting untuk key ".$key.": ".$stmt->error);
        }
        $stmt->close();
    } else {
        // error_log("Gagal prepare get_site_setting untuk key ".$key.": ".$conn_param->error);
    }
    return $setting_value;
}

// Fungsi untuk menyimpan/update nilai pengaturan
function update_setting($conn_param, $key, $value) {
    $sql = "INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?)
            ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)";
    if ($stmt = $conn_param->prepare($sql)) {
        $stmt->bind_param("ss", $key, $value);
        if ($stmt->execute()) {
            if ($stmt->errno == 0) { 
                 return true;
            }
            // error_log("Gagal execute update_setting untuk key ".$key.": ".$stmt->error);
        }
        $stmt->close();
    }
    // error_log("Gagal prepare update_setting untuk key ".$key.": ".$conn_param->error);
    return false;
}

// Proses form jika ada data yang di-submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn->begin_transaction();
    $all_updates_successful = true;
    $transaction_error_message = null; // Pesan error spesifik dari transaksi

    // Daftar pengaturan yang akan diupdate dari form
    $settings_to_update_from_post = [
        'nama_toko'             => $_POST['nama_toko'] ?? '',
        'email_kontak'          => $_POST['email_kontak'] ?? '',
        'telepon_toko'          => $_POST['telepon_toko'] ?? '',
        'alamat_toko_lengkap'   => $_POST['alamat_toko_lengkap'] ?? '',
        'rekening_bank_1_nama'  => $_POST['rekening_bank_1_nama'] ?? '',
        'rekening_bank_1_nomor' => $_POST['rekening_bank_1_nomor'] ?? '',
        'rekening_bank_1_an'    => $_POST['rekening_bank_1_an'] ?? '',
        'rekening_bank_2_nama'  => $_POST['rekening_bank_2_nama'] ?? '',
        'rekening_bank_2_nomor' => $_POST['rekening_bank_2_nomor'] ?? '',
        'rekening_bank_2_an'    => $_POST['rekening_bank_2_an'] ?? '',
        'deskripsi_toko'        => $_POST['deskripsi_toko'] ?? ''
    ];

    foreach ($settings_to_update_from_post as $key => $value) {
        if (!update_setting($conn, $key, trim($value))) {
            $all_updates_successful = false;
            // Ambil error dari koneksi jika ada, karena statement mungkin sudah ditutup
            $transaction_error_message = "Gagal menyimpan pengaturan untuk '" . htmlspecialchars($key) . "'. Database error: " . $conn->error; 
            break; 
        }
    }

    if ($all_updates_successful) {
        $conn->commit();
        $_SESSION['settings_message'] = "Pengaturan berhasil disimpan!";
        $_SESSION['settings_message_type'] = "success";
    } else {
        $conn->rollback();
        $_SESSION['settings_message'] = $transaction_error_message ?: "Terjadi kesalahan umum saat menyimpan pengaturan.";
        $_SESSION['settings_message_type'] = "danger";
    }
    header("Location: admin_pengaturan.php"); // Refresh halaman untuk melihat perubahan atau pesan
    exit;
}


// Ambil semua pengaturan saat ini untuk ditampilkan di form
$keys_to_fetch = ['nama_toko', 'email_kontak', 'telepon_toko', 'alamat_toko_lengkap', 
                  'rekening_bank_1_nama', 'rekening_bank_1_nomor', 'rekening_bank_1_an',
                  'rekening_bank_2_nama', 'rekening_bank_2_nomor', 'rekening_bank_2_an',
                  'deskripsi_toko'];
foreach ($keys_to_fetch as $key) {
    $settings[$key] = get_site_setting($conn, $key);
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> - Admin Casual Steps</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="admin_style.css"> </head>
<body>
<div class="sidebar">
    <h3 class="text-center my-3 fw-bold">CASUAL STEPS</h3>
    <a href="admin_dashboard.php"><i class="fa fa-tachometer-alt me-2"></i>Dashboard</a>
    <a href="admin_produk.php"><i class="fa fa-box me-2"></i>Produk</a>
    <a href="admin_pesanan.php"><i class="fa fa-file-invoice me-2"></i>Pesanan</a>
    <a href="admin_pelanggan.php"><i class="fa fa-users me-2"></i>Pelanggan</a>
    <a href="admin_kategori.php"><i class="fa fa-tags me-2"></i>Kategori</a>
    <a href="admin_laporan.php"><i class="fa fa-chart-line me-2"></i>Laporan</a>
    <a href="admin_pengaturan.php" class="active"><i class="fa fa-cog me-2"></i>Pengaturan</a>
    <a href="admin-pesan-kontak.php"><i class="fa fa-envelope me-2"></i>Kontak Masuk</a>
    <a href="admin_promo.php"><i class="fa fa-gift me-2"></i>Promo</a>
    <hr class="text-secondary">
    <a href="logout.php"><i class="fa fa-sign-out-alt me-2"></i>Logout</a>
</div>

<div class="main-content">
    <div class="container-fluid">
        <h1 class="h3 mb-4"><?php echo htmlspecialchars($page_title); ?></h1>

        <?php
        // Menampilkan pesan notifikasi dari session
        if (isset($_SESSION['settings_message']) && isset($_SESSION['settings_message_type'])) {
            echo '<div class="alert alert-' . htmlspecialchars($_SESSION['settings_message_type']) . ' alert-dismissible fade show mb-3" role="alert">';
            echo htmlspecialchars($_SESSION['settings_message']);
            echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
            echo '</div>';
            unset($_SESSION['settings_message']);
            unset($_SESSION['settings_message_type']);
        }
        ?>

        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-cogs me-2"></i>Formulir Pengaturan Situs</h5>
            </div>
            <div class="card-body">
                <form action="admin_pengaturan.php" method="POST">
                    <fieldset class="mb-4 p-3 border rounded">
                        <legend class="w-auto px-2 h6">Informasi Toko Dasar</legend>
                        <div class="mb-3">
                            <label for="nama_toko" class="form-label">Nama Toko/Situs</label>
                            <input type="text" class="form-control" id="nama_toko" name="nama_toko" value="<?php echo htmlspecialchars($settings['nama_toko'] ?? ''); ?>">
                        </div>
                        <div class="mb-3">
                            <label for="email_kontak" class="form-label">Email Kontak Utama</label>
                            <input type="email" class="form-control" id="email_kontak" name="email_kontak" value="<?php echo htmlspecialchars($settings['email_kontak'] ?? ''); ?>">
                        </div>
                        <div class="mb-3">
                            <label for="telepon_toko" class="form-label">Nomor Telepon Toko</label>
                            <input type="text" class="form-control" id="telepon_toko" name="telepon_toko" value="<?php echo htmlspecialchars($settings['telepon_toko'] ?? ''); ?>">
                        </div>
                        <div class="mb-3">
                            <label for="alamat_toko_lengkap" class="form-label">Alamat Toko Lengkap</label>
                            <textarea class="form-control" id="alamat_toko_lengkap" name="alamat_toko_lengkap" rows="3"><?php echo htmlspecialchars($settings['alamat_toko_lengkap'] ?? ''); ?></textarea>
                        </div>
                         <div class="mb-3">
                            <label for="deskripsi_toko" class="form-label">Deskripsi Singkat Toko</label>
                            <textarea class="form-control" id="deskripsi_toko" name="deskripsi_toko" rows="2"><?php echo htmlspecialchars($settings['deskripsi_toko'] ?? ''); ?></textarea>
                        </div>
                    </fieldset>

                    <fieldset class="mb-4 p-3 border rounded">
                        <legend class="w-auto px-2 h6">Informasi Rekening Bank (untuk Transfer Manual)</legend>
                        <h6>Rekening Bank 1:</h6>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="rekening_bank_1_nama" class="form-label">Nama Bank</label>
                                <input type="text" class="form-control" id="rekening_bank_1_nama" name="rekening_bank_1_nama" value="<?php echo htmlspecialchars($settings['rekening_bank_1_nama'] ?? ''); ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="rekening_bank_1_nomor" class="form-label">Nomor Rekening</label>
                                <input type="text" class="form-control" id="rekening_bank_1_nomor" name="rekening_bank_1_nomor" value="<?php echo htmlspecialchars($settings['rekening_bank_1_nomor'] ?? ''); ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="rekening_bank_1_an" class="form-label">Atas Nama</label>
                                <input type="text" class="form-control" id="rekening_bank_1_an" name="rekening_bank_1_an" value="<?php echo htmlspecialchars($settings['rekening_bank_1_an'] ?? ''); ?>">
                            </div>
                        </div>
                        <hr>
                        <h6>Rekening Bank 2 (Opsional):</h6>
                         <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="rekening_bank_2_nama" class="form-label">Nama Bank</label>
                                <input type="text" class="form-control" id="rekening_bank_2_nama" name="rekening_bank_2_nama" value="<?php echo htmlspecialchars($settings['rekening_bank_2_nama'] ?? ''); ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="rekening_bank_2_nomor" class="form-label">Nomor Rekening</label>
                                <input type="text" class="form-control" id="rekening_bank_2_nomor" name="rekening_bank_2_nomor" value="<?php echo htmlspecialchars($settings['rekening_bank_2_nomor'] ?? ''); ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="rekening_bank_2_an" class="form-label">Atas Nama</label>
                                <input type="text" class="form-control" id="rekening_bank_2_an" name="rekening_bank_2_an" value="<?php echo htmlspecialchars($settings['rekening_bank_2_an'] ?? ''); ?>">
                            </div>
                        </div>
                    </fieldset>
                    
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Simpan Pengaturan</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
if (isset($conn)) {
    $conn->close();
}
?>