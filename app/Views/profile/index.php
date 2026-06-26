<?php require_once dirname(__DIR__) . '/layouts/header.php'; ?>

<link href="https://cdn.jsdelivr.net/npm/cropperjs@1.5.13/dist/cropper.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/cropperjs@1.5.13/dist/cropper.min.js"></script>

<style>
    .account-info-card {
        border: none;
        border-radius: 1rem;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        overflow: hidden;
    }
    .account-info-card .card-header {
        background-color: transparent;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        font-weight: 700;
        padding: 1.5rem;
    }
    .profile-picture-container {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        overflow: hidden;
        margin: 0 auto 1.5rem;
        border: 4px solid #fff;
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        position: relative;
    }
    .profile-picture-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .dashboard-menu .list-group-item {
        border: none;
        padding: 1rem 1.5rem;
        margin-bottom: 0.5rem;
        border-radius: 0.75rem !important;
        transition: all 0.3s ease;
        font-weight: 500;
        color: #6c757d;
    }
    .dashboard-menu .list-group-item:hover {
        background-color: #f8f9fa;
        transform: translateX(5px);
    }
    .dashboard-menu .list-group-item.active {
        background: linear-gradient(45deg, #0d6efd, #0dcaf0);
        color: white;
        box-shadow: 0 4px 15px rgba(13,110,253,0.2);
    }
</style>

<main class="container py-5" style="margin-top: 100px;">
    <div class="d-flex justify-content-between align-items-center mb-5 border-bottom pb-3" data-aos="fade-down">
        <h2 class="mb-0 fw-bold" style="font-family: 'Special Gothic Expanded One', system-ui;"><?= htmlspecialchars($page_title); ?></h2>
    </div>

    <?php if (isset($_SESSION['profil_message'])): ?>
        <div class="alert alert-<?= htmlspecialchars($_SESSION['profil_message_type']); ?> alert-dismissible fade show rounded-4 shadow-sm" role="alert" data-aos="fade-in">
            <i class="fas <?= $_SESSION['profil_message_type'] === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle' ?> me-2"></i> <?= $_SESSION['profil_message']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['profil_message'], $_SESSION['profil_message_type']); ?>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-lg-4" data-aos="fade-right">
            <div class="dashboard-menu list-group shadow-sm bg-white p-3 rounded-4">
                <a href="<?= BASE_URL ?>index.php?url=Profile/index" class="list-group-item list-group-item-action <?= !$edit_mode ? 'active' : ''; ?>">
                    <i class="fas fa-user-circle me-3"></i> Profil Saya
                </a>
                <a href="<?= BASE_URL ?>index.php?url=Order/history" class="list-group-item list-group-item-action">
                    <i class="fas fa-box-open me-3"></i> Riwayat Pesanan
                </a>
                <a href="<?= BASE_URL ?>index.php?url=Address/index" class="list-group-item list-group-item-action">
                    <i class="fas fa-map-marker-alt me-3"></i> Alamat Saya
                </a>
                <a href="<?= BASE_URL ?>index.php?url=Profile/changePassword" class="list-group-item list-group-item-action">
                    <i class="fas fa-lock me-3"></i> Ubah Password
                </a>
                <a href="<?= BASE_URL ?>index.php?url=Auth/logout" class="list-group-item list-group-item-action text-danger mt-3 bg-light">
                    <i class="fas fa-sign-out-alt me-3"></i> Logout
                </a>
            </div>
        </div>
        
        <div class="col-lg-8" data-aos="fade-left" data-aos-delay="100">
            <div class="card account-info-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="fs-5 text-dark"><i class="fas <?= $edit_mode ? 'fa-user-edit' : 'fa-id-card' ?> text-primary me-2"></i> <?= $edit_mode ? 'Form Ubah Profil' : 'Detail Profil'; ?></span>
                    <?php if (!$edit_mode): ?>
                        <a href="<?= BASE_URL ?>index.php?url=Profile/index&action=edit" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                            <i class="fas fa-pen me-1"></i> Edit
                        </a>
                    <?php endif; ?>
                </div>
                <div class="card-body p-4 p-md-5 position-relative">
                    <div style="position: absolute; top: -50px; right: -50px; width: 150px; height: 150px; background: rgba(13,202,240,0.1); border-radius: 50%; z-index: 0;"></div>
                    
                    <?php if ($user_data): ?>
                        <div class="profile-picture-container mb-4 position-relative z-1">
                            <?php
                            $profile_pic_name = $user_data['profile_picture_url'] ?? '';
                            $is_url = filter_var($profile_pic_name, FILTER_VALIDATE_URL);
                            $profile_pic_path = $is_url ? $profile_pic_name : BASE_URL . 'admin/' . $profile_pic_name;
                            
                            if (empty($profile_pic_name) || (!$is_url && !file_exists(dirname(__DIR__) . '/../../admin/' . $profile_pic_name))) {
                                $profile_pic_path = 'https://via.placeholder.com/150/808080/FFFFFF?text=' . strtoupper(substr($user_data['name'], 0, 1));
                            }
                            ?>
                            <img src="<?= htmlspecialchars($profile_pic_path); ?>" alt="Foto Profil" class="bg-light">
                        </div>

                        <div class="position-relative z-1">
                            <?php if ($edit_mode): ?>
                                <form action="<?= BASE_URL ?>index.php?url=Profile/update" method="POST" enctype="multipart/form-data">
                                    <div class="row">
                                        <div class="col-md-6 mb-4">
                                            <label class="form-label text-muted small fw-bold text-uppercase">Nama Lengkap <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control form-control-lg bg-light border-0" name="name" value="<?= htmlspecialchars($user_data['name']); ?>" required>
                                        </div>
                                        <div class="col-md-6 mb-4">
                                            <label class="form-label text-muted small fw-bold text-uppercase">Email</label>
                                            <input type="email" class="form-control form-control-lg bg-light border-0 text-muted" value="<?= htmlspecialchars($user_data['email']); ?>" disabled>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-4">
                                            <label class="form-label text-muted small fw-bold text-uppercase">Nomor Telepon</label>
                                            <input type="tel" class="form-control form-control-lg bg-light border-0" name="phone_number" value="<?= htmlspecialchars($user_data['phone_number'] ?? ''); ?>">
                                        </div>
                                        <div class="col-md-6 mb-4">
                                            <label class="form-label text-muted small fw-bold text-uppercase">Tanggal Lahir</label>
                                            <input type="date" class="form-control form-control-lg bg-light border-0" name="date_of_birth" value="<?= htmlspecialchars($user_data['date_of_birth'] ?? ''); ?>">
                                        </div>
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label text-muted small fw-bold text-uppercase">Ubah Foto Profil</label>
                                        <input class="form-control form-control-lg bg-light border-0" type="file" id="profile_picture" name="profile_picture" accept="image/jpeg, image/png, image/gif">
                                        <small class="form-text text-muted mt-2 d-block"><i class="fas fa-info-circle me-1"></i> Maksimal ukuran file 2MB.</small>
                                        
                                        <div class="modal fade" id="cropperModal" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content rounded-4 border-0 shadow">
                                                    <div class="modal-header border-bottom-0 pt-4 pb-0 px-4">
                                                        <h5 class="modal-title fw-bold">Sesuaikan Foto Profil</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body text-center p-4">
                                                        <div class="bg-light rounded-3 overflow-hidden p-2">
                                                            <img id="image-cropper-preview" style="max-width:100%; max-height:300px;">
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer border-top-0 pb-4 px-4">
                                                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                                        <button type="button" id="cropImageBtn" class="btn btn-primary rounded-pill px-4" style="background: linear-gradient(45deg, #0d6efd, #0dcaf0); border: none;">Gunakan Foto</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-5">
                                        <label class="form-label text-muted small fw-bold text-uppercase">Bio Singkat</label>
                                        <textarea class="form-control form-control-lg bg-light border-0" name="bio" rows="3" placeholder="Ceritakan sedikit tentang diri Anda..."><?= htmlspecialchars($user_data['bio'] ?? ''); ?></textarea>
                                    </div>
                                    
                                    <div class="d-flex gap-3">
                                        <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5 shadow-sm" style="background: linear-gradient(45deg, #0d6efd, #0dcaf0); border: none;">
                                            <i class="fas fa-save me-2"></i> Simpan Perubahan
                                        </button>
                                        <a href="<?= BASE_URL ?>index.php?url=Profile/index" class="btn btn-light btn-lg rounded-pill px-4">Batal</a>
                                    </div>
                                </form>
                            <?php else: ?>
                                <div class="text-center mb-5">
                                    <h3 class="fw-bold mb-1 text-dark"><?= htmlspecialchars($user_data['name']); ?></h3>
                                    <span class="badge bg-light text-secondary px-3 py-2 rounded-pill shadow-sm"><i class="fas fa-envelope text-primary me-1"></i> <?= htmlspecialchars($user_data['email']); ?></span>
                                </div>
                                
                                <div class="row g-4 mt-3">
                                    <div class="col-md-6">
                                        <div class="p-4 bg-light rounded-4 h-100">
                                            <h6 class="text-muted small text-uppercase fw-bold mb-3"><i class="fas fa-phone-alt me-2 text-primary"></i> Kontak</h6>
                                            <p class="fs-5 mb-0 text-dark fw-medium"><?= htmlspecialchars($user_data['phone_number']) ?: '<span class="text-muted fst-italic fs-6">Belum diatur</span>'; ?></p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-4 bg-light rounded-4 h-100">
                                            <h6 class="text-muted small text-uppercase fw-bold mb-3"><i class="fas fa-birthday-cake me-2 text-primary"></i> Tanggal Lahir</h6>
                                            <p class="fs-5 mb-0 text-dark fw-medium"><?= !empty($user_data['date_of_birth']) ? htmlspecialchars(date('d F Y', strtotime($user_data['date_of_birth']))) : '<span class="text-muted fst-italic fs-6">Belum diatur</span>'; ?></p>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="p-4 bg-light rounded-4">
                                            <h6 class="text-muted small text-uppercase fw-bold mb-3"><i class="fas fa-quote-left me-2 text-primary"></i> Bio</h6>
                                            <?php if (!empty($user_data['bio'])): ?>
                                                <p class="fs-6 mb-0 text-dark" style="line-height: 1.6;"><?= nl2br(htmlspecialchars($user_data['bio'])); ?></p>
                                            <?php else: ?>
                                                <p class="text-muted fst-italic mb-0">Belum ada deskripsi bio.</p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                            <h4>Data Tidak Ditemukan</h4>
                            <p class="text-muted">Tidak dapat memuat informasi akun Anda saat ini.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
let cropper;
const input = document.getElementById('profile_picture');
if (input) {
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
                aspectRatio: 1,
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
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(new File([blob], "profile.jpg", {type: "image/jpeg"}));
                input.files = dataTransfer.files;
                modal.hide();
            }, 'image/jpeg', 0.95);
        }
    });
}
</script>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
