<?php require_once dirname(__DIR__) . '/layouts/header.php'; ?>

<section class="about-hero position-relative d-flex align-items-center justify-content-center" style="margin-top: 70px; min-height: 60vh; background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.7)), url('<?= BASE_URL ?>admin/Assets/ok.png') no-repeat center center; background-size: cover; background-attachment: fixed; overflow: hidden;" data-aos="fade-in">
    <div class="container text-center py-5 z-1">
        <h1 class="display-2 fw-bold mb-4 text-white shadow-sm" style="font-family: 'Special Gothic Expanded One', system-ui;" data-aos="zoom-in" data-aos-delay="200">TENTANG KAMI</h1>
        <p class="lead col-lg-8 mx-auto text-light fw-light text-shadow" style="font-size: 1.25rem;" data-aos="fade-up" data-aos-delay="400">Menghadirkan langkah nyaman dan penuh gaya untuk menemani setiap aktivitas Anda.</p>
    </div>
</section>

<section class="our-story-section py-5 my-5">
    <div class="container">
        <div class="row align-items-center bg-white p-4 p-md-5 rounded-5 shadow-sm border-0">
            <div class="col-md-6 mb-5 mb-md-0 position-relative" data-aos="fade-right">
                <div style="position: absolute; top: 10px; left: -10px; width: 100%; height: 100%;"></div>
                <img src="<?= BASE_URL ?>admin/Assets/brand-logo.png" alt="Kualitas Casual Steps" class="img-fluid rounded-4 shadow-lg position-relative z-1" style="transition: transform 0.5s;" onmouseover="this.style.transform='scale(1.02)'" onmouseout="this.style.transform='scale(1)'"/>
            </div>
            <div class="col-md-6 ps-md-5" data-aos="fade-left" data-aos-delay="200">
                <h2 class="display-5 fw-bold mb-4" style="font-family: 'Special Gothic Expanded One', system-ui;">Cerita Kami</h2>
                <div class="pe-md-4">
                    <p class="text-muted mb-4" style="line-height: 1.8; font-size: 1.1rem;">
                        <b class="text-dark">Casual Steps</b> lahir dari kecintaan pada kenyamanan dan gaya. Berdiri sejak tahun 2020 di jantung kota Yogyakarta, kami memulai perjalanan dengan satu misi: menyediakan sepatu kasual berkualitas tinggi yang tidak hanya mengikuti tren terkini, tetapi juga ramah di kantong bagi pria dan wanita Indonesia.
                    </p>
                    <p class="text-muted" style="line-height: 1.8; font-size: 1.1rem;">
                        Kami percaya bahwa sepatu adalah fondasi dari setiap penampilan dan cerminan gaya hidup. Oleh karena itu, setiap pasang sepatu yang kami tawarkan telah melalui proses kurasi dan kontrol kualitas yang ketat, memastikan pelanggan kami mendapatkan pengalaman melangkah yang tak tertandingi dalam kenyamanan dan kepuasan.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="vision-mission-section py-5 bg-dark text-white rounded-5 mx-2 mx-md-4 mb-5" data-aos="fade-up">
    <div class="container py-4">
        <div class="row justify-content-center text-center">
            <div class="col-md-5 mb-5 mb-md-0 p-4" data-aos="zoom-in" data-aos-delay="200">
                <div class="bg-white text-primary rounded-circle d-inline-flex justify-content-center align-items-center mb-4 shadow" style="width: 80px; height: 80px;">
                    <i class="fas fa-bullseye fa-2x"></i>
                </div>
                <h3 class="fw-bold mb-3 font-special">Visi Kami</h3>
                <p class="text-white-50" style="line-height: 1.8;">Menjadi destinasi utama bagi para pencari sepatu kasual yang mengutamakan kualitas, gaya, dan nilai terbaik di Indonesia.</p>
            </div>
            <div class="col-md-2 d-none d-md-flex justify-content-center align-items-center">
                <div style="height: 100%; width: 1px; background: rgba(255,255,255,0.1);"></div>
            </div>
            <div class="col-md-5 p-4" data-aos="zoom-in" data-aos-delay="400">
                <div class="bg-white text-primary rounded-circle d-inline-flex justify-content-center align-items-center mb-4 shadow" style="width: 80px; height: 80px;">
                    <i class="fas fa-rocket fa-2x"></i>
                </div>
                <h3 class="fw-bold mb-3 font-special">Misi Kami</h3>
                <ul class="list-unstyled text-start d-inline-block text-white-50">
                    <li class="mb-3 d-flex align-items-center"><i class="fas fa-check-circle text-info me-3 fs-5"></i>Menyediakan koleksi sepatu kasual yang beragam.</li>
                    <li class="mb-3 d-flex align-items-center"><i class="fas fa-check-circle text-info me-3 fs-5"></i>Menjamin kualitas produk dengan standar tinggi.</li>
                    <li class="mb-3 d-flex align-items-center"><i class="fas fa-check-circle text-info me-3 fs-5"></i>Memberikan pengalaman belanja yang mudah.</li>
                    <li class="mb-3 d-flex align-items-center"><i class="fas fa-check-circle text-info me-3 fs-5"></i>Membangun hubungan jangka panjang dengan pelanggan.</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<section class="why-choose-us-section py-5 mb-5">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="display-5 fw-bold mb-3" style="font-family: 'Special Gothic Expanded One', system-ui;">Mengapa Memilih Kami?</h2>
            <p class="lead text-muted col-lg-8 mx-auto">Kami bukan hanya sekadar toko sepatu, kami adalah partner gaya Anda.</p>
        </div>
        <div class="row g-4">
            <div class="col-md-6 col-lg-3 d-flex" data-aos="fade-up" data-aos-delay="100">
                <div class="card bg-white border-0 shadow-sm text-center p-4 flex-fill rounded-4" style="transition: all 0.3s;" onmouseover="this.style.transform='translateY(-10px)'; this.style.boxShadow='0 15px 30px rgba(0,0,0,0.1)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 0.125rem 0.25rem rgba(0,0,0,0.075)'">
                    <div class="mx-auto mb-4 bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                        <i class="fas fa-medal text-primary fs-3"></i>
                    </div>
                    <h5 class="card-title fw-bold mb-3">Produk Original</h5>
                    <p class="card-text text-muted small">Jaminan 100% produk asli dengan garansi resmi.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 d-flex" data-aos="fade-up" data-aos-delay="200">
                <div class="card bg-white border-0 shadow-sm text-center p-4 flex-fill rounded-4" style="transition: all 0.3s;" onmouseover="this.style.transform='translateY(-10px)'; this.style.boxShadow='0 15px 30px rgba(0,0,0,0.1)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 0.125rem 0.25rem rgba(0,0,0,0.075)'">
                    <div class="mx-auto mb-4 bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                        <i class="fas fa-palette text-primary fs-3"></i>
                    </div>
                    <h5 class="card-title fw-bold mb-3">Desain Kekinian</h5>
                    <p class="card-text text-muted small">Selalu update dengan tren fashion sepatu terbaru.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 d-flex" data-aos="fade-up" data-aos-delay="300">
                <div class="card bg-white border-0 shadow-sm text-center p-4 flex-fill rounded-4" style="transition: all 0.3s;" onmouseover="this.style.transform='translateY(-10px)'; this.style.boxShadow='0 15px 30px rgba(0,0,0,0.1)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 0.125rem 0.25rem rgba(0,0,0,0.075)'">
                    <div class="mx-auto mb-4 bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                        <i class="fas fa-comments text-primary fs-3"></i>
                    </div>
                    <h5 class="card-title fw-bold mb-3">Pelayanan Ramah</h5>
                    <p class="card-text text-muted small">Tim kami siap membantu Anda dengan responsif.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 d-flex" data-aos="fade-up" data-aos-delay="400">
                <div class="card bg-white border-0 shadow-sm text-center p-4 flex-fill rounded-4" style="transition: all 0.3s;" onmouseover="this.style.transform='translateY(-10px)'; this.style.boxShadow='0 15px 30px rgba(0,0,0,0.1)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 0.125rem 0.25rem rgba(0,0,0,0.075)'">
                    <div class="mx-auto mb-4 bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                        <i class="fas fa-shipping-fast text-primary fs-3"></i>
                    </div>
                    <h5 class="card-title fw-bold mb-3">Pengiriman Cepat</h5>
                    <p class="card-text text-muted small">Pesanan Anda kami proses dan kirim secepatnya.</p>
                </div>
            </div>
        </div>
        <div class="text-center mt-5" data-aos="zoom-in" data-aos-delay="500">
            <a href="<?= BASE_URL ?>index.php?url=Product/index" class="btn btn-primary btn-lg px-5 py-3 rounded-pill shadow" style="background: linear-gradient(45deg, #0d6efd, #0dcaf0); border: none;">Jelajahi Koleksi Kami <i class="fas fa-arrow-right ms-2"></i></a>
        </div>
    </div>
</section>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
