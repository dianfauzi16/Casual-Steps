<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'db_connect.php';
// Cek jika admin sudah login, tambahkan validasi sesuai sistem Anda

$result = $conn->query("SELECT * FROM pesan_kontak ORDER BY tanggal_kirim DESC");
?>
<!DOCTYPE html>
<html>

<head>
    <title>Pesan Kontak Masuk</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="admin_style.css">
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
                <h1 class="h3">Pesan Kontak Masuk</h1>
            </div>

            <?php
            // Menampilkan pesan notifikasi dari session (misalnya setelah menandai dibaca)
            if (isset($_SESSION['pesan_kontak_message']) && isset($_SESSION['pesan_kontak_message_type'])) {
                echo '<div class="alert alert-' . htmlspecialchars($_SESSION['pesan_kontak_message_type']) . ' alert-dismissible fade show" role="alert">';
                echo htmlspecialchars($_SESSION['pesan_kontak_message']);
                echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
                echo '</div>';
                unset($_SESSION['pesan_kontak_message']);
                unset($_SESSION['pesan_kontak_message_type']);
            }
            ?>

            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-envelope-open-text me-2"></i>Daftar Pesan</h5>
                </div>
                <div class="card-body">
                    <?php if ($result && $result->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Tanggal</th>
                                        <th>Nama</th>
                                        <th>Email</th>
                                        <th>Subjek</th>
                                        <th>Pesan (Singkat)</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td>#<?php echo htmlspecialchars($row['id']); ?></td>
                                            <td><?php echo htmlspecialchars(date('d M Y, H:i', strtotime($row['tanggal_kirim']))); ?></td>
                                            <td><?php echo htmlspecialchars($row['nama']); ?></td>
                                            <td><a href="mailto:<?php echo htmlspecialchars($row['email']); ?>"><?php echo htmlspecialchars($row['email']); ?></a></td>
                                            <td><?php echo htmlspecialchars($row['subjek']); ?></td>
                                            <td><?php echo htmlspecialchars(mb_strimwidth($row['pesan'], 0, 50, "...")); ?></td>
                                            <td class="text-center">
                                                <?php if ($row['status_baca'] == 'sudah dibaca'): ?>
                                                    <span class="badge bg-success">Sudah Dibaca</span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning text-dark">Belum Dibaca</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-info btn-sm mb-1" data-bs-toggle="modal" data-bs-target="#detailPesanModal" 
                                                        data-id="<?php echo $row['id']; ?>"
                                                        data-nama="<?php echo htmlspecialchars($row['nama']); ?>"
                                                        data-email="<?php echo htmlspecialchars($row['email']); ?>"
                                                        data-subjek="<?php echo htmlspecialchars($row['subjek']); ?>"
                                                        data-pesan="<?php echo htmlspecialchars($row['pesan']); ?>"
                                                        data-tanggal="<?php echo htmlspecialchars(date('d M Y, H:i', strtotime($row['tanggal_kirim']))); ?>"
                                                        title="Lihat Detail Pesan">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <?php if ($row['status_baca'] == 'belum dibaca'): ?>
                                                    <a href="proses_tandai_dibaca.php?id_pesan=<?php echo $row['id']; ?>" class="btn btn-success btn-sm mb-1" title="Tandai Sudah Dibaca" onclick="return confirm('Tandai pesan ini sebagai sudah dibaca?');">
                                                        <i class="fas fa-check-circle"></i>
                                                    </a>
                                                    <?php else: ?>
                                                    <a href="admin_balas_pesan.php?id_pesan=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm mb-1" title="Balas Pesan">
                                                        <i class="fas fa-reply"></i> Balas
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info text-center">Belum ada pesan kontak yang masuk.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detail Pesan -->
    <div class="modal fade" id="detailPesanModal" tabindex="-1" aria-labelledby="detailPesanModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailPesanModalLabel">Detail Pesan Kontak</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>ID Pesan:</strong> <span id="modalPesanId"></span></p>
                    <p><strong>Tanggal Kirim:</strong> <span id="modalPesanTanggal"></span></p>
                    <p><strong>Nama Pengirim:</strong> <span id="modalPesanNama"></span></p>
                    <p><strong>Email Pengirim:</strong> <span id="modalPesanEmail"></span></p>
                    <p><strong>Subjek:</strong> <span id="modalPesanSubjek"></span></p>
                    <hr>
                    <p><strong>Isi Pesan:</strong></p>
                    <div id="modalPesanIsi" style="white-space: pre-wrap; max-height: 300px; overflow-y: auto;"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        var detailPesanModal = document.getElementById('detailPesanModal');
        detailPesanModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var id = button.getAttribute('data-id');
            var nama = button.getAttribute('data-nama');
            var email = button.getAttribute('data-email');
            var subjek = button.getAttribute('data-subjek');
            var pesan = button.getAttribute('data-pesan');
            var tanggal = button.getAttribute('data-tanggal');

            var modalTitle = detailPesanModal.querySelector('.modal-title');
            var modalPesanId = detailPesanModal.querySelector('#modalPesanId');
            var modalPesanTanggal = detailPesanModal.querySelector('#modalPesanTanggal');
            var modalPesanNama = detailPesanModal.querySelector('#modalPesanNama');
            var modalPesanEmail = detailPesanModal.querySelector('#modalPesanEmail');
            var modalPesanSubjek = detailPesanModal.querySelector('#modalPesanSubjek');
            var modalPesanIsi = detailPesanModal.querySelector('#modalPesanIsi');

            modalTitle.textContent = 'Detail Pesan dari ' + nama;
            modalPesanId.textContent = '#' + id;
            modalPesanTanggal.textContent = tanggal;
            modalPesanNama.textContent = nama;
            modalPesanEmail.innerHTML = '<a href="mailto:' + email + '">' + email + '</a>';
            modalPesanSubjek.textContent = subjek;
            modalPesanIsi.textContent = pesan; // Menggunakan textContent agar nl2br dari PHP tidak diinterpretasi sebagai HTML
        });
    </script>
</body>

</html>
<?php if (isset($conn)) $conn->close(); ?>