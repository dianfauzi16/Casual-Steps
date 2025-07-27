<?php
$page_title = "Password Berhasil Diubah";
$nama_toko = "Casual Steps";
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Meta tag ini akan mengarahkan pengguna setelah 5 detik -->
    <meta http-equiv="refresh" content="5;url=login_pelanggan.php">
    <title><?php echo htmlspecialchars($page_title); ?> - <?php echo htmlspecialchars($nama_toko); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
    <style>
        .success-animation-icon {
            font-size: 5rem;
            color: #198754; /* Warna hijau sukses Bootstrap */
            animation: pop-in 0.6s cubic-bezier(0.68, -0.55, 0.27, 1.55);
        }

        @keyframes pop-in {
            0% {
                transform: scale(0);
                opacity: 0;
            }
            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        .progress-bar-container {
            width: 100%;
            background-color: #e9ecef;
            border-radius: .25rem;
            overflow: hidden;
            margin-top: 1rem;
        }

        .progress-bar-inner {
            height: 8px;
            width: 100%;
            background-color: #0d6efd; /* Warna biru primary Bootstrap */
            animation: countdown 5s linear forwards;
        }

        @keyframes countdown {
            from { width: 100%; }
            to { width: 0%; }
        }
    </style>
</head>

<body class="form-page-bg d-flex flex-column min-vh-100">
    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top shadow-sm">
            <div class="container">
                <a class="navbar-brand fw-bold" href="index.php"><?php echo htmlspecialchars($nama_toko); ?></a>
            </div>
        </nav>
    </header>

    <main class="container flex-grow-1 d-flex align-items-center justify-content-center">
        <div class="col-md-6 col-lg-5 col-xl-4">
            <div class="form-card text-center">
                <i class="fas fa-check-circle success-animation-icon mb-3"></i>
                <h2 class="form-card-title mb-3">Berhasil!</h2>
                <p class="text-muted">Password Anda telah berhasil diubah.</p>
                <p class="text-muted">Anda akan diarahkan ke halaman login dalam <span id="countdown-timer">5</span> detik...</p>
                <div class="progress-bar-container">
                    <div class="progress-bar-inner"></div>
                </div>
                <a href="login_pelanggan.php" class="btn btn-link mt-3">Login Sekarang</a>
            </div>
        </div>
    </main>

    <footer class="bg-dark text-white py-4 mt-auto">
        <div class="container text-center">
            <p class="mb-0">© <?php echo date("Y"); ?> <?php echo htmlspecialchars($nama_toko); ?>. All rights reserved.</p>
        </div>
    </footer>

    <script>
        let seconds = 5;
        const countdownElement = document.getElementById('countdown-timer');
        const interval = setInterval(() => {
            seconds--;
            if (countdownElement) {
                countdownElement.textContent = seconds;
            }
            if (seconds <= 0) {
                clearInterval(interval);
            }
        }, 1000);
    </script>
</body>

</html>
