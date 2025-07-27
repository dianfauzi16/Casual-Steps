<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Terhubung dengan Casual Steps di media sosial. Ikuti kami di Instagram, Facebook, TikTok, dan Twitter." />
    <meta name="keywords" content="media sosial, casual steps, instagram, facebook, twitter, tiktok, social links" />
    <title>Ikuti Kami - Casual Steps</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="styles.css" />
    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
            background-color: #ffffff;
        }

        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url("../ADMIN MENU/Assets/logo.png"); /* GANTI BARIS INI */
            /* GANTI BARIS INI */
            background-size: 70% auto;
            /* <--- Contoh: lebar 70%, tinggi otomatis */
            background-position: center center;
            background-repeat: no-repeat;
            z-index: -1;
            transition: transform 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94),
                filter 0.6s ease;
        }

        body:hover::before {
            transform: scale(1.05);
            filter: brightness(1.1) saturate(1.1);
        }
        
    </style>
</head>

<body>
    <header>
        <nav class="navbar navbar-light fixed-top shadow-sm">
            <div class="container">
                <a class="navbar-brand fw-bold" href="index.php" style="color: #333; text-shadow: 1px 1px 2px rgba(255,255,255,0.5);">CASUAL STEPS</a>
            </div>
        </nav>
    </header>

    <main class="social-connect-section">
        <div class="container">
            <h1>Tetap Terhubung</h1>
            <p class="subtitle">Temukan kami di platform media sosial favorit Anda dan jadilah bagian dari komunitas Casual Steps!</p>

            <div class="social-links-container">
                <a href="https://www.instagram.com/NAMA_AKUN_ANDA" target="_blank" rel="noopener noreferrer" class="social-icon-link instagram" aria-label="Instagram Casual Steps" title="Instagram">
                    <i class="fab fa-instagram"></i>
                </a>
                <a href="https://www.facebook.com/NAMA_HALAMAN_ANDA" target="_blank" rel="noopener noreferrer" class="social-icon-link facebook" aria-label="Facebook Casual Steps" title="Facebook">
                    <i class="fab fa-facebook-f"></i>
                </a>
                <a href="https://www.tiktok.com/@NAMA_AKUN_ANDA" target="_blank" rel="noopener noreferrer" class="social-icon-link tiktok" aria-label="TikTok Casual Steps" title="TikTok">
                    <i class="fab fa-tiktok"></i>
                </a>
                <a href="https://twitter.com/NAMA_AKUN_ANDA" target="_blank" rel="noopener noreferrer" class="social-icon-link twitter" aria-label="Twitter Casual Steps" title="Twitter">
                    <i class="fab fa-twitter"></i>
                </a>
            </div>
        </div>
    </main>

    <footer class="bg-dark text-white py-5">
        <div class="container">
            <hr class="my-4 bg-secondary">
            <div class="text-center">
                <p class="mb-0 small"> CasualSteps. All rights reserved. </p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>

</html>