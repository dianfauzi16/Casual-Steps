<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 1. Periksa apakah pengguna sudah login, jika tidak, arahkan ke halaman login
if (!isset($_SESSION['user_loggedin']) || $_SESSION['user_loggedin'] !== true || !isset($_SESSION['user_id'])) {
    $_SESSION['login_error'] = "Anda harus login untuk mengakses halaman ini.";
    header('Location: login_pelanggan.php');
    exit;
}

// 2. Sertakan file koneksi database
require_once __DIR__ . '/../ADMIN MENU/db_connect.php';

$page_title = "Akun Saya";
$user_id = $_SESSION['user_id'];
$user_data = null;
$error_message = '';
$edit_mode = false; // Variabel untuk menandai mode edit

// Cek apakah mode edit aktif dari URL
if (isset($_GET['action']) && $_GET['action'] == 'edit') {
    $edit_mode = true;
    $page_title = "Ubah Profil Saya";
}

// 3. Ambil data pengguna dari database berdasarkan user_id di session
$sql_get_user_info = "SELECT u.id, u.name, u.email, u.phone_number, 
                             up.date_of_birth, up.profile_picture_url, up.bio
                      FROM users u
                      LEFT JOIN user_profiles up ON u.id = up.user_id
                      WHERE u.id = ?";
if ($stmt = $conn->prepare($sql_get_user_info)) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows == 1) {
        $user_data = $result->fetch_assoc();
    } else {
        $error_message = "Gagal mengambil informasi pengguna.";
    }
    $stmt->close();
} else {
    $error_message = "Terjadi kesalahan pada database: " . $conn->error;
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
    <link href="https://cdn.jsdelivr.net/npm/cropperjs@1.5.13/dist/cropper.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/cropperjs@1.5.13/dist/cropper.min.js"></script>
    <style>
        .account-info-card .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            font-weight: 600;
        }

        .profile-picture-container {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            overflow: hidden;
            margin: 0 auto 1rem;
            border: 3px solid #dee2e6;

        }

        .profile-picture-container img {
            width: 100%;
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
                    </ul>
                </div>
                <div class="d-flex align-items-center">
                    <a href="keranjang.php" class="position-relative me-3 text-dark">
                        <i class="fas fa-shopping-bag"></i>
                        <?php
                        $jumlah_item_di_keranjang = 0;
                        if (isset($_SESSION['keranjang']) && is_array($_SESSION['keranjang'])) {
                            foreach ($_SESSION['keranjang'] as $cart_key => $item_data) {
                                if (is_array($item_data) && isset($item_data['kuantitas'])) {
                                    $jumlah_item_di_keranjang += (int)$item_data['kuantitas'];
                                }
                            }
                        }
                        if ($jumlah_item_di_keranjang > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                <?php echo $jumlah_item_di_keranjang; ?>
                            </span>
                        <?php endif; ?>
                    </a>
                    <?php if (isset($_SESSION['user_loggedin']) && $_SESSION['user_loggedin'] === true): ?>
                        <div class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-dark" href="#" id="navbarDropdownUser" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user me-1"></i> <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Akun Saya'); ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownUser">
                                <li><a class="dropdown-item <?php echo (basename($_SERVER['PHP_SELF']) == 'akun_saya.php' ? 'active' : ''); ?>" href="akun_saya.php">Profil Saya</a></li>
                                <li><a class="dropdown-item" href="riwayat_pesanan.php">Riwayat Pesanan</a></li>
                                <li><a class="dropdown-item <?php echo (basename($_SERVER['PHP_SELF']) == 'alamat_saya.php' || basename($_SERVER['PHP_SELF']) == 'tambah_alamat.php' || basename($_SERVER['PHP_SELF']) == 'edit_alamat.php') ? 'active' : ''; ?>" href="alamat_saya.php">Alamat Saya</a></li>
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

    <main class="container py-5" style="margin-top: 100px;">
        <h1 class="mb-4"><?php echo htmlspecialchars($page_title); ?></h1>

        <?php
        // Menampilkan pesan notifikasi dari proses_edit_profil.php (jika ada)
        if (isset($_SESSION['profil_message']) && isset($_SESSION['profil_message_type'])) {
            echo '<div class="alert alert-' . htmlspecialchars($_SESSION['profil_message_type']) . ' alert-dismissible fade show" role="alert">';
            echo htmlspecialchars($_SESSION['profil_message']);
            echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
            echo '</div>';
            unset($_SESSION['profil_message']);
            unset($_SESSION['profil_message_type']);
        }
        ?>

        <div class="row">
            <div class="col-md-4">
                <div class="list-group mb-4">
                    <a href="akun_saya.php" class="list-group-item list-group-item-action <?php echo !$edit_mode ? 'active' : ''; ?>">
                        <i class="fas fa-user-circle me-2"></i>Profil Saya
                    </a>
                    <a href="riwayat_pesanan.php" class="list-group-item list-group-item-action">
                        <i class="fas fa-history me-2"></i>Riwayat Pesanan
                    </a>
                    <a href="alamat_saya.php" class="list-group-item list-group-item-action <?php echo (basename($_SERVER['PHP_SELF']) == 'alamat_saya.php' || basename($_SERVER['PHP_SELF']) == 'tambah_alamat.php' || basename($_SERVER['PHP_SELF']) == 'edit_alamat.php') ? 'active' : ''; ?>">
                        <i class="fas fa-map-marker-alt me-2"></i>Alamat Saya
                    </a>
                    <a href="ubah_password.php" class="list-group-item list-group-item-action <?php echo (basename($_SERVER['PHP_SELF']) == 'ubah_password.php' ? 'active' : ''); ?>">
                        <i class="fas fa-key me-2"></i>Ubah Password
                    </a>
                    <a href="logout_pelanggan.php" class="list-group-item list-group-item-action text-danger">
                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                    </a>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card account-info-card">
                    <div class="card-header">
                        <?php echo $edit_mode ? 'Form Ubah Profil' : 'Detail Profil'; ?>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($error_message)): ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo htmlspecialchars($error_message); ?>
                            </div>
                        <?php elseif ($user_data): ?>
                            <!-- Tampilan Profil Picture (selalu tampil) -->
                            <div class="profile-picture-container mb-4">
                                <?php
                                $profile_pic_path = '../ADMIN MENU/' . ($user_data['profile_picture_url'] ?? 'placeholder_profile.png');
                                if (empty($user_data['profile_picture_url']) || !file_exists($profile_pic_path)) {
                                    $profile_pic_path = 'https://via.placeholder.com/150/808080/FFFFFF?text=' . strtoupper(substr($user_data['name'], 0, 1));
                                }
                                ?>
                                <img src="<?php echo htmlspecialchars($profile_pic_path); ?>" alt="Foto Profil <?php echo htmlspecialchars($user_data['name']); ?>">
                            </div>
                            <?php if ($edit_mode): ?>
                                <form action="proses_edit_profil.php" method="POST" enctype="multipart/form-data">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($user_data['name']); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" readonly disabled>
                                        <small class="form-text text-muted">Email tidak dapat diubah melalui halaman ini.</small>
                                    </div>
                                    <div class="mb-3">
                                        <label for="phone_number" class="form-label">Nomor Telepon</label>
                                        <input type="tel" class="form-control" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($user_data['phone_number'] ?? ''); ?>" placeholder="Masukkan nomor telepon">
                                    </div>
                                    <div class="mb-3">
                                        <label for="profile_picture" class="form-label">Ubah Foto Profil</label>
                                        <input class="form-control" type="file" id="profile_picture" name="profile_picture" accept="image/jpeg, image/png, image/gif">
                                        <small class="form-text text-muted">Ukuran maks 2MB.</small>
                                        <!-- Modal Crop Foto -->
                                        <div class="modal fade" id="cropperModal" tabindex="-1" aria-labelledby="cropperModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Crop Foto Profil</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body text-center">
                                                        <img id="image-cropper-preview" style="max-width:100%; max-height:300px;">
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" id="cropImageBtn" class="btn btn-primary">Simpan</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="date_of_birth" class="form-label">Tanggal Lahir</label>
                                        <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" value="<?php echo htmlspecialchars($user_data['date_of_birth'] ?? ''); ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label for="bio" class="form-label">Bio Singkat</label>
                                        <textarea class="form-control" id="bio" name="bio" rows="3" placeholder="Ceritakan sedikit tentang diri Anda..."><?php echo htmlspecialchars($user_data['bio'] ?? ''); ?></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Simpan Perubahan</button>
                                    <a href="akun_saya.php" class="btn btn-secondary">Batal</a>
                                </form>
                            <?php else: ?>
                                <div class="text-center">
                                    <h4 class="mb-1"><?php echo htmlspecialchars($user_data['name']); ?></h4>
                                    <p class="text-muted mb-3"><?php echo htmlspecialchars($user_data['email']); ?></p>
                                </div>
                                <p><strong>Nomor Telepon:</strong> <?php echo htmlspecialchars($user_data['phone_number']) ?: '<em>- Belum diatur -</em>'; ?></p>
                                <p><strong>Tanggal Lahir:</strong> <?php echo !empty($user_data['date_of_birth']) ? htmlspecialchars(date('d F Y', strtotime($user_data['date_of_birth']))) : '<em>- Belum diatur -</em>'; ?></p>
                                <p><strong>Bio:</strong></p>
                                <p class="text-muted fst-italic"><?php echo !empty($user_data['bio']) ? nl2br(htmlspecialchars($user_data['bio'])) : '<em>- Belum ada bio -</em>'; ?></p>
                                <hr>
                                <a href="akun_saya.php?action=edit" class="btn btn-outline-primary btn-sm"><i class="fas fa-edit me-1"></i> Ubah Profil</a>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="alert alert-warning" role="alert">
                                Tidak dapat memuat informasi akun Anda.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
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
    <script>
let cropper;
const input = document.getElementById('profile_picture');
const modal = new bootstrap.Modal(document.getElementById('cropperModal'));
const imagePreview = document.getElementById('image-cropper-preview');
const cropBtn = document.getElementById('cropImageBtn');

input.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function(event) {
        imagePreview.src = event.target.result;
        modal.show();
        if (cropper) cropper.destroy();
        cropper = new Cropper(imagePreview, {
            aspectRatio: 1, // Square crop
            viewMode: 1,
            minContainerWidth: 300,
            minContainerHeight: 300,
        });
    };
    reader.readAsDataURL(file);
});

cropBtn.addEventListener('click', function() {
    if (cropper) {
        cropper.getCroppedCanvas({
            width: 300,
            height: 300,
            imageSmoothingQuality: 'high'
        }).toBlob(function(blob) {
            // Ganti file input dengan hasil crop
            const fileInput = document.getElementById('profile_picture');
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(new File([blob], "profile.jpg", {type: "image/jpeg"}));
            fileInput.files = dataTransfer.files;
            modal.hide();
        }, 'image/jpeg', 0.95);
    }
});
</script>
</body>

</html>
<?php
if (isset($conn)) {
    $conn->close();
}
?>