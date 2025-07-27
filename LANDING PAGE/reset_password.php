<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../ADMIN MENU/db_connect.php';

$page_title = "Atur Ulang Password";
$token = $_GET['token'] ?? '';
$is_token_valid = false;

if (!empty($token)) {
    // 1. Cek validitas token di database
    $sql_check_token = "SELECT user_id, expires_at FROM password_resets WHERE token = ?";
    if ($stmt = $conn->prepare($sql_check_token)) {
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            $expires_at = strtotime($row['expires_at']);
            $current_time = time();

            if ($current_time < $expires_at) {
                $is_token_valid = true;
                $user_id_from_token = $row['user_id'];
            } else {
                // Token kadaluarsa, hapus dari DB
                $sql_delete_expired = "DELETE FROM password_resets WHERE token = ?";
                if ($stmt_del = $conn->prepare($sql_delete_expired)) {
                    $stmt_del->bind_param("s", $token);
                    $stmt_del->execute();
                    $stmt_del->close();
                }
                $_SESSION['lupa_password_message'] = "Tautan reset password sudah kadaluarsa. Silakan minta tautan baru.";
                $_SESSION['lupa_password_message_type'] = "warning";
                header('Location: lupa_password.php');
                exit;
            }
        } else {
            $_SESSION['lupa_password_message'] = "Tautan reset password tidak valid atau sudah digunakan.";
            $_SESSION['lupa_password_message_type'] = "danger";
            header('Location: lupa_password.php');
            exit;
        }
        $stmt->close();
    } else {
        $_SESSION['lupa_password_message'] = "Terjadi kesalahan database.";
        $_SESSION['lupa_password_message_type'] = "danger";
        header('Location: lupa_password.php');
        exit;
    }
} else {
    // Jika tidak ada token sama sekali, langsung redirect
    header('Location: lupa_password.php');
    exit;
}

$conn->close();
$nama_toko = "Casual Steps"; // Anda bisa buat ini dinamis jika perlu
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> - <?php echo htmlspecialchars($nama_toko); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .form-container {
            max-width: 450px;
            margin-top: 80px;
            margin-bottom: 80px;
            background-color: #fff;
            padding: 30px 40px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }

        .btn-submit {
            background-color: #0d6efd;
            border-color: #0d6efd;
            padding: 10px 15px;
            font-weight: 500;
        }
    </style>
</head>

<body>

    <body class="form-page-bg d-flex flex-column min-vh-100">
        <header>
            <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
                <nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top shadow-sm">
                    <div class="container">
                        <a class="navbar-brand fw-bold" href="index.php"><?php echo htmlspecialchars($nama_toko); ?></a>
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavGlobal"><span class="navbar-toggler-icon"></span></button>
                        <div class="collapse navbar-collapse justify-content-center" id="navbarNavGlobal">
                        </div>
                        <div class="d-flex align-items-center">
                            <a href="login_pelanggan.php" class="nav-link text-dark me-2">Login</a>
                            <a href="register.php" class="btn btn-primary btn-sm">Register</a>
                        </div>
                    </div>
                </nav>
        </header>

        <main class="container flex-grow-1 d-flex align-items-center justify-content-center">
            <div class="col-md-6 col-lg-5 col-xl-4">
                <div class="form-card">
                    <div class="text-center mb-4">
                        <h2 class="form-card-title"><?php echo htmlspecialchars($page_title); ?></h2>
                        <p class="text-muted">Buat password baru yang kuat untuk akun Anda.</p>
                    </div>

                    <?php
                    if (isset($_SESSION['password_reset_message'])) {
                        echo '<div class="alert alert-' . htmlspecialchars($_SESSION['password_reset_message_type']) . '" role="alert">' . htmlspecialchars($_SESSION['password_reset_message']) . '</div>';
                        unset($_SESSION['password_reset_message']);
                        unset($_SESSION['password_reset_message_type']);
                    }
                    if (isset($_SESSION['password_reset_message'])) {
                        echo '<div class="alert alert-' . htmlspecialchars($_SESSION['password_reset_message_type']) . ' alert-dismissible fade show" role="alert">'
                            . htmlspecialchars($_SESSION['password_reset_message'])
                            . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                        unset($_SESSION['password_reset_message']);
                        unset($_SESSION['password_reset_message_type']);
                    }
                    if ($is_token_valid):
                    ?>
                        <form action="proses_reset_password.php" method="POST">
                            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                            <div class="mb-3">
                                <label for="new_password" class="form-label">Password Baru <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="new_password" name="new_password" placeholder="Minimal 8 karakter" required>
                            </div>
                            <div class="mb-3">
                                <label for="confirm_new_password" class="form-label">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="confirm_new_password" name="confirm_new_password" placeholder="Konfirmasi password baru" required>
                            </div>
                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary btn-lg">Atur Ulang Password</button>
                            </div>
                        </form>
                    <?php else: ?>
                        <p class="text-center">Silakan kembali ke halaman <a href="lupa_password.php">Lupa Password</a> untuk mencoba lagi.</p>
                    <?php endif; ?>
                </div>
            </div>
        </main>

        <footer class="bg-dark text-white py-4 mt-auto">
            <div class="container text-center">
                <p class="mb-0">© <?php echo date("Y"); ?> <?php echo htmlspecialchars($nama_toko); ?>. All rights reserved.</p>
            </div>
        </footer>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>

</html>