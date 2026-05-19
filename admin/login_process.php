<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'db_connect.php'; // Sertakan file koneksi

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username_input = trim($_POST['username']);
    $password_input = trim($_POST['password']);

    if (empty($username_input)) {
        $error_message = "Username tidak boleh kosong.";
    } elseif (empty($password_input)) {
        $error_message = "Password tidak boleh kosong.";
    } else {
        $sql = "SELECT id, username, password FROM admins WHERE username = ?"; //

        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $param_username);
            $param_username = $username_input;

            if ($stmt->execute()) {
                $stmt->store_result();
                if ($stmt->num_rows == 1) {
                    $stmt->bind_result($id, $db_username, $hashed_password_from_db);
                    if ($stmt->fetch()) {
                        if (password_verify($password_input, $hashed_password_from_db)) {
                            unset($_SESSION['login_error']);
                            $_SESSION['admin_loggedin'] = true;
                            $_SESSION['admin_id'] = $id;
                            $_SESSION['admin_username'] = $db_username;
                            header("location: admin_dashboard.php");
                            exit;
                        } else {
                            $error_message = "Username atau password yang Anda masukkan salah.";
                        }
                    }
                } else {
                    $error_message = "Username atau password yang Anda masukkan salah.";
                }
            } else {
                $error_message = "Oops! Terjadi kesalahan pada server.";
            }
            $stmt->close();
        } else {
            $error_message = "Oops! Terjadi kesalahan pada persiapan database.";
        }
    }
} else {
    header("location: admin_login.php");
    exit;
}

if (!empty($error_message)) {
    $_SESSION['login_error'] = $error_message;
    header("location: admin_login.php");
    exit;
}

$conn->close();
?>