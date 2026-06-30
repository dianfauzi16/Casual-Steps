    <!-- Akhir Konten Utama -->
    <footer class="bg-dark text-white py-5 mt-auto">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-5 col-md-6 mb-4 mb-md-0">
                    <h5 class="text-uppercase fw-bold mb-4 font-special" style="letter-spacing: 2px; color: #0d6efd;"><?= htmlspecialchars($global_settings['nama_toko'] ?? 'CASUAL STEPS') ?></h5>
                    <p class="text-muted pe-lg-5 mb-4" style="line-height: 1.8;">
                        <?= htmlspecialchars($global_settings['deskripsi_toko'] ?? 'Menyediakan koleksi sepatu premium dan kasual dengan harga terbaik untuk menemani setiap langkah Anda.') ?>
                    </p>
                    <div class="d-flex gap-3">
                        <?php if (!empty($global_settings['social_instagram'])): ?>
                            <a href="<?= htmlspecialchars($global_settings['social_instagram']) ?>" target="_blank" class="text-white-50 bg-secondary bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; transition: all 0.3s;" onmouseover="this.className='text-white bg-danger rounded-circle d-flex align-items-center justify-content-center'" onmouseout="this.className='text-white-50 bg-secondary bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center'"><i class="fab fa-instagram"></i></a>
                        <?php endif; ?>
                        
                        <?php if (!empty($global_settings['social_facebook'])): ?>
                            <a href="<?= htmlspecialchars($global_settings['social_facebook']) ?>" target="_blank" class="text-white-50 bg-secondary bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; transition: all 0.3s;" onmouseover="this.className='text-white bg-primary rounded-circle d-flex align-items-center justify-content-center'" onmouseout="this.className='text-white-50 bg-secondary bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center'"><i class="fab fa-facebook-f"></i></a>
                        <?php endif; ?>
                        
                        <?php if (!empty($global_settings['social_tiktok'])): ?>
                            <a href="<?= htmlspecialchars($global_settings['social_tiktok']) ?>" target="_blank" class="text-white-50 bg-secondary bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; transition: all 0.3s;" onmouseover="this.className='text-white bg-dark rounded-circle d-flex align-items-center justify-content-center'" onmouseout="this.className='text-white-50 bg-secondary bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center'"><i class="fab fa-tiktok"></i></a>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
                    <h6 class="text-uppercase fw-bold mb-4">Navigasi</h6>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><a href="<?= BASE_URL ?>" class="text-white-50 text-decoration-none">Beranda</a></li>
                        <li class="mb-2"><a href="<?= BASE_URL ?>index.php?url=Product/index" class="text-white-50 text-decoration-none">Produk</a></li>
                        <li class="mb-2"><a href="<?= BASE_URL ?>index.php?url=Home/promo" class="text-white-50 text-decoration-none">Promo</a></li>
                        <li class="mb-2"><a href="<?= BASE_URL ?>index.php?url=Home/about" class="text-white-50 text-decoration-none">Tentang Kami</a></li>
                    </ul>
                </div>

                <div class="col-lg-4 col-md-12">
                    <h6 class="text-uppercase fw-bold mb-4">Hubungi Kami</h6>
                    <ul class="list-unstyled text-muted">
                        <li class="mb-3 d-flex">
                            <i class="fas fa-map-marker-alt mt-1 me-3 text-white-50"></i>
                            <span><?= nl2br(htmlspecialchars($global_settings['alamat_toko_lengkap'] ?? 'Jl. Raya No. 123, Kota Anda')) ?></span>
                        </li>
                        <li class="mb-3 d-flex align-items-center">
                            <i class="fas fa-phone-alt me-3 text-white-50"></i>
                            <span><?= htmlspecialchars($global_settings['telepon_toko'] ?? '+62 812-3456-7890') ?></span>
                        </li>
                        <li class="mb-3 d-flex align-items-center">
                            <i class="fas fa-envelope me-3 text-white-50"></i>
                            <span><?= htmlspecialchars($global_settings['email_kontak'] ?? 'cs@casualsteps.com') ?></span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <hr class="my-4 border-secondary" />
            <div class="text-center">
                <p class="mb-0 text-white-50">© <?= date('Y') ?> <?= htmlspecialchars($global_settings['nama_toko'] ?? 'CasualSteps') ?>. Personal Portfolio.</p>
            </div>
        </div>
    </footer>
    
    <script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-auth.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="<?= BASE_URL ?>public/js/firebase_config.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            AOS.init({
                duration: 800,
                once: true,
                offset: 100,
            });
        });
    </script>
</body>
</html>
