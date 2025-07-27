<?php
// Memulai sesi jika belum ada sesi yang aktif.
// Sesi digunakan untuk menyimpan pesan error dari halaman lain (login_process.php).
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Casual Steps</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="admin_style.css">
</head>
<body class="login-page">
    <div class="card login-card">
        <div class="login-header">
            <i class="fa fa-user-shield"></i>
            <h2>Admin Login</h2>
            <p>Casual Steps Admin Panel</p>
        </div>
        <div class="card-body login-body">
            <?php
            // Memeriksa apakah ada pesan error login yang disimpan di sesi.
            if (isset($_SESSION['login_error']) && !empty($_SESSION['login_error'])) {
                // Jika ada, tampilkan pesan error dalam sebuah div dengan kelas alert Bootstrap.
                echo '<div class="alert alert-danger" role="alert">' . htmlspecialchars($_SESSION['login_error']) . '</div>';
                // Setelah pesan error ditampilkan, hapus dari sesi agar tidak muncul lagi saat halaman di-refresh.
                unset($_SESSION['login_error']); 
            }
            ?>
            <form action="login_process.php" method="POST"> 
                <div class="mb-3">
                    <label for="username" class="form-label fw-bold">Username</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa fa-user"></i></span>
                        <input type="text" class="form-control" id="username" name="username" placeholder="Masukkan username admin">
                    </div>
                </div>
                <div class="mb-4">
                    <label for="password" class="form-label fw-bold">Password</label>
                    <div class="input-group">
                         <span class="input-group-text"><i class="fa fa-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan password admin">
                    </div>
                </div>
                <button type="submit" class="btn btn-login w-100">Login Admin</button>
            </form>
        </div>
    </div>
</body>
</html>