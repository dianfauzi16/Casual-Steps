<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Cek jika admin sudah login
if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    $_SESSION['login_error'] = "Anda harus login untuk mengakses halaman admin.";
    header("location: admin_login.php");
    exit;
}

require_once 'db_connect.php';

$page_title = "Balas Pesan Kontak";
$pesan_kontak = null;
$error_message = '';

if (isset($_GET['id_pesan']) && filter_var($_GET['id_pesan'], FILTER_VALIDATE_INT)) {
    $id_pesan = (int)$_GET['id_pesan'];

    $sql = "SELECT id, nama, email, subjek, pesan, tanggal_kirim, status_baca, admin_reply_message, admin_reply_timestamp FROM pesan_kontak WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $id_pesan);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows == 1) {
            $pesan_kontak = $result->fetch_assoc();
            $page_title .= " #" . htmlspecialchars($pesan_kontak['id']);
        } else {
            $error_message = "Pesan tidak ditemukan.";
        }
        $stmt->close();
    } else {
        $error_message = "Gagal mempersiapkan statement: " . $conn->error;
    }
} else {
    $error_message = "ID Pesan tidak valid atau tidak disediakan.";
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> - Admin Casual Steps</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="admin_style.css">
    <style>
        .message-card .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }
    </style>
</head>

<body>
    <div class="sidebar">
        <h3 class="text-center my-3 fw-bold">CASUAL STEPS</h3>
        <a href="admin_dashboard.php"><i class="fa fa-tachometer-alt me-2"></i>Dashboard</a>
        <a href="admin_produk.php"><i class="fa fa-box me-2"></i>Produk</a>
        <a href="admin_pesanan.php"><i class="fa fa-file-invoice me-2"></i>Pesanan</a>
        <a href="admin_pelanggan.php"><i class="fa fa-users me-2"></i>Pelanggan</a>
        <a href="admin_kategori.php"><i class="fa fa-tags me-2"></i>Kategori</a>
        <a href="admin_laporan.php"><i class="fa fa-chart-line me-2"></i>Laporan</a>
        <a href="admin_pengaturan.php"><i class="fa fa-cog me-2"></i>Pengaturan</a>
        <a href="admin-pesan-kontak.php" class="active"><i class="fa fa-envelope me-2"></i>Kontak Masuk</a>
        <a href="admin_promo.php"><i class="fa fa-gift me-2"></i>Promo</a>
        <hr class="text-secondary">
        <a href="logout.php"><i class="fa fa-sign-out-alt me-2"></i>Logout</a>
    </div>

    <div class="main-content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3"><?php echo htmlspecialchars($page_title); ?></h1>
                <a href="admin-pesan-kontak.php" class="btn btn-outline-secondary btn-sm">
                    <i class="fa fa-arrow-left me-1"></i> Kembali ke Daftar Pesan
                </a>
            </div>

            <?php
            if (!empty($error_message)) : ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
            <?php elseif ($pesan_kontak) : ?>
                <div class="card message-card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0 fw-bold">Pesan Asli dari <?php echo htmlspecialchars($pesan_kontak['nama']); ?></h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Dari:</strong> <?php echo htmlspecialchars($pesan_kontak['nama']); ?> &lt;<a href="mailto:<?php echo htmlspecialchars($pesan_kontak['email']); ?>"><?php echo htmlspecialchars($pesan_kontak['email']); ?></a>&gt;</p>
                        <p><strong>Subjek:</strong> <?php echo htmlspecialchars($pesan_kontak['subjek']); ?></p>
                        <p><strong>Tanggal Kirim:</strong> <?php echo htmlspecialchars(date('d M Y, H:i', strtotime($pesan_kontak['tanggal_kirim']))); ?></p>
                        <hr>
                        <p><strong>Isi Pesan:</strong></p>
                        <div class="alert alert-light border" style="white-space: pre-wrap;"><?php echo htmlspecialchars($pesan_kontak['pesan']); ?></div>
                    </div>
                </div>

                <?php if (!empty($pesan_kontak['admin_reply_message'])) : ?>
                    <div class="card message-card shadow-sm mb-4">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0 fw-bold">Balasan Sebelumnya</h5>
                        </div>
                        <div class="card-body">
                            <p><strong>Dibalas pada:</strong> <?php echo htmlspecialchars(date('d M Y, H:i', strtotime($pesan_kontak['admin_reply_timestamp']))); ?></p>
                            <hr>
                            <p><strong>Isi Balasan:</strong></p>
                            <div class="alert alert-light border" style="white-space: pre-wrap;"><?php echo htmlspecialchars($pesan_kontak['admin_reply_message']); ?></div>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0 fw-bold">Formulir Balasan</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        // Menampilkan pesan notifikasi dari session (setelah proses_balas_pesan.php)
                        if (isset($_SESSION['pesan_kontak_message']) && isset($_SESSION['pesan_kontak_message_type'])) {
                            echo '<div class="alert alert-' . htmlspecialchars($_SESSION['pesan_kontak_message_type']) . ' alert-dismissible fade show mb-3" role="alert">';
                            echo htmlspecialchars($_SESSION['pesan_kontak_message']);
                            echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
                            echo '</div>';
                            unset($_SESSION['pesan_kontak_message']);
                            unset($_SESSION['pesan_kontak_message_type']);
                        }
                        ?>
                        <form action="proses_balas_pesan.php" method="POST">
                            <input type="hidden" name="id_pesan" value="<?php echo htmlspecialchars($pesan_kontak['id']); ?>">
                            <input type="hidden" name="user_email" value="<?php echo htmlspecialchars($pesan_kontak['email']); ?>">
                            <input type="hidden" name="user_name" value="<?php echo htmlspecialchars($pesan_kontak['nama']); ?>">
                            <input type="hidden" name="original_subject" value="<?php echo htmlspecialchars($pesan_kontak['subjek']); ?>">
                            <input type="hidden" name="original_message" value="<?php echo htmlspecialchars($pesan_kontak['pesan']); ?>">

                            <div class="mb-3">
                                <label for="admin_reply" class="form-label">Isi Balasan Anda <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="admin_reply" name="admin_reply" rows="8" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane me-2"></i>Kirim Balasan</button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
<?php if (isset($conn)) $conn->close(); ?>