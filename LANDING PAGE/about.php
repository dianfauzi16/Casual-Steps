<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Jika di masa depan Anda membutuhkan data dari database untuk halaman ini,
// sertakan koneksi di sini. Contoh:
// require_once 'db_connect.php';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Pelajari lebih lanjut tentang Casual Steps, komitmen kami terhadap kualitas, desain trendy, dan kepuasan pelanggan dalam menyediakan sepatu kasual terbaik di Yogyakarta dan seluruh Indonesia." />
    <meta name="keywords" content="tentang kami, casual steps, toko sepatu, sepatu kasual, yogyakarta, sepatu berkualitas" />
    <title>Tentang Kami - Casual Steps</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="styles.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Special+Gothic+Expanded+One&display=swap" rel="stylesheet">
</head>

</head>

<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top shadow-sm">
            <div class="container">
                <a class="navbar-brand fw-bold" href="index.php">Casual Steps</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item"><a class="nav-link" href="index.php">HOME</a></li>
                        <li class="nav-item"><a class="nav-link active" aria-current="page" href="about.php">ABOUT</a></li>
                        <li class="nav-item"><a class="nav-link" href="produk.php">PRODUCT</a></li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($current_page_base == 'promo.php') ? 'active' : ''; ?>" href="promo.php">SALE</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <section class="about-hero">
        <div class="container" class="text-center py-5" style="margin-top: 100px;">
            <h1 class="display-3 fw-bold" style="font-family: 'Special Gothic Expanded One', system-ui; ">Tentang Casual Steps</h1>
            <p class="lead col-lg-8 mx-auto">Menghadirkan langkah nyaman dan penuh gaya untuk menemani setiap aktivitas Anda.</p>
        </div>
    </section>

    <section class="our-story-section py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 mb-4 mb-md-0">
                    <img src="../ADMIN MENU/Assets/brand-logo.png" alt="Kualitas Casual Steps" class="img-fluid rounded shadow-lg" />
                </div>
                <div class="col-md-6">
                    <h2 class="display-5 fw-bold mb-3" style="font-family: 'Special Gothic Expanded One', system-ui;">Cerita Kami</h2>
                    <p class="text-muted mb-4">
                        <b>Casual Steps</b> lahir dari kecintaan pada kenyamanan dan gaya. Berdiri sejak tahun 2020 di jantung kota Yogyakarta, kami memulai perjalanan dengan satu misi: menyediakan sepatu kasual berkualitas tinggi yang tidak hanya mengikuti tren terkini, tetapi juga ramah di kantong bagi pria dan wanita Indonesia.
                    </p>
                    <p class="text-muted">
                        Kami percaya bahwa sepatu adalah fondasi dari setiap penampilan dan cerminan gaya hidup. Oleh karena itu, setiap pasang sepatu yang kami tawarkan telah melalui proses kurasi dan kontrol kualitas yang ketat, memastikan pelanggan kami mendapatkan pengalaman melangkah yang tak tertandingi dalam kenyamanan dan kepuasan.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="vision-mission-section py-5 bg-light">
        <div class="container">
            <div class="row text-center">
                <div class="col-md-6 mb-4 mb-md-0">
                    <i class="fas fa-bullseye icon-feature"></i>
                    <h3 class="fw-semibold mb-3">Visi Kami</h3>
                    <p class="text-muted">Menjadi destinasi utama bagi para pencari sepatu kasual yang mengutamakan kualitas, gaya, dan nilai terbaik di Indonesia.</p>
                </div>
                <div class="col-md-6">
                    <i class="fas fa-rocket icon-feature"></i>
                    <h3 class="fw-semibold mb-3">Misi Kami</h3>
                    <ul class="list-unstyled text-muted">
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Menyediakan koleksi sepatu kasual yang beragam dan selalu up-to-date.</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Menjamin kualitas produk melalui standar quality control yang tinggi.</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Memberikan pengalaman belanja yang mudah, aman, dan menyenangkan.</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Membangun hubungan jangka panjang dengan pelanggan melalui pelayanan prima.</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <section class="why-choose-us-section py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold" style="font-family: 'Special Gothic Expanded One', system-ui;">Mengapa Memilih Casual Steps?</h2>
                <p class="lead text-muted col-lg-8 mx-auto">Kami bukan hanya sekadar toko sepatu, kami adalah partner gaya Anda.</p>
            </div>
            <div class="row g-4">
                <div class="col-md-6 col-lg-3 d-flex">
                    <div class="card shadow-sm text-center p-4 flex-fill">
                        <i class="fas fa-medal icon-feature"></i>
                        <h5 class="card-title fw-semibold">Produk Original</h5>
                        <p class="card-text text-muted small">Jaminan 100% produk asli dengan garansi resmi.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3 d-flex">
                    <div class="card shadow-sm text-center p-4 flex-fill">
                        <i class="fas fa-palette icon-feature"></i>
                        <h5 class="card-title fw-semibold">Desain Kekinian</h5>
                        <p class="card-text text-muted small">Selalu update dengan tren fashion sepatu terbaru.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3 d-flex">
                    <div class="card shadow-sm text-center p-4 flex-fill">
                        <i class="fas fa-comments icon-feature"></i>
                        <h5 class="card-title fw-semibold">Pelayanan Ramah</h5>
                        <p class="card-text text-muted small">Tim kami siap membantu Anda dengan responsif.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3 d-flex">
                    <div class="card shadow-sm text-center p-4 flex-fill">
                        <i class="fas fa-shipping-fast icon-feature"></i>
                        <h5 class="card-title fw-semibold">Pengiriman Cepat</h5>
                        <p class="card-text text-muted small">Pesanan Anda kami proses dan kirim secepatnya.</p>
                    </div>
                </div>
            </div>
            <div class="text-center mt-5">
                <a href="produk.php" class="btn btn-primary btn-lg px-5 py-3">Jelajahi Koleksi Kami</a>
            </div>
        </div>
    </section>

    <footer class="bg-dark text-white py-5 mt-auto">
        <div class="container">
            <hr class="my-4" />
            <div class="text-center">
                <p class="mb-0">© 2025 Casual Steps. All rights reserved.</p>
            </div>
        </div>
    </footer>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>

</html>