<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Ambil pesan dari session
$status = $_SESSION['pesan_kontak_status'] ?? 'info';
$message = $_SESSION['pesan_kontak_message'] ?? 'Terima kasih atas pesan Anda.';

// Hapus pesan dari session setelah diambil agar tidak muncul lagi saat di-refresh
unset($_SESSION['pesan_kontak_status']);
unset($_SESSION['pesan_kontak_message']);

$page_title = "Konfirmasi Pesan";
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
    <style>
        body {
            background-color: #f8f9fa;
        }

        .confirmation-container {
            max-width: 600px;
            margin-top: 100px;
            margin-bottom: 50px;
            background-color: #fff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .confirmation-icon {
            font-size: 4rem;
            margin-bottom: 20px;
        }
    </style>
</head>

<body class="d-flex flex-column min-vh-100">
    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top shadow-sm">
            <div class="container">
                <a class="navbar-brand fw-bold" href="index.php">CASUAL STEPS</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"><span class="navbar-toggler-icon"></span></button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="produk.php">Produk</a></li>
                        <li class="nav-item"><a class="nav-link" href="index.php#contact1">Kontak</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <main class="container">
        <div class="confirmation-container mx-auto">
            <?php if ($status === 'success'): ?>
                <i class="fas fa-check-circle confirmation-icon text-success"></i>
            <?php elseif ($status === 'danger'): ?>
                <i class="fas fa-times-circle confirmation-icon text-danger"></i>
            <?php else: ?>
                <i class="fas fa-info-circle confirmation-icon text-info"></i>
            <?php endif; ?>
            <h2 class="mb-3"><?php echo htmlspecialchars($page_title); ?></h2>
            <p class="lead"><?php echo htmlspecialchars($message); ?></p>
            <a href="index.php" class="btn btn-primary mt-4">Kembali ke Halaman Utama</a>
        </div>
    </main>

    <footer class="bg-dark text-white py-5 mt-auto">
        <div class="container">
            <hr class="my-4" />
            <div class="text-center">
                <p class="mb-0">© <?php echo date("Y"); ?> CasualSteps. All rights reserved.</p>
            </div>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>