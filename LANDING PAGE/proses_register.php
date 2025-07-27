<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../ADMIN MENU/db_connect.php'; // Sesuaikan path jika perlu

// Inisialisasi array untuk menyimpan error dan data form
$errors = [];
$_SESSION['form_data'] = $_POST; // Simpan data form untuk diisi ulang jika ada error

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil dan bersihkan data input
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone_number = trim($_POST['phone_number'] ?? '');
    $password = $_POST['password'] ?? ''; // Jangan trim password agar spasi di awal/akhir tetap valid jika diinginkan
    $confirm_password = $_POST['confirm_password'] ?? '';
    $agree_terms = isset($_POST['agree_terms']);

    // Gabungkan nama depan dan nama belakang
    $full_name = $first_name . ' ' . $last_name;

    // 1. Validasi Input
    if (empty($first_name)) {
        $errors[] = "Nama depan wajib diisi.";
    }
    if (empty($last_name)) {
        $errors[] = "Nama belakang wajib diisi.";
    }
    if (empty($email)) {
        $errors[] = "Email wajib diisi.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Format email tidak valid.";
    } else {
        // Cek apakah email sudah terdaftar
        $sql_check_email = "SELECT id FROM users WHERE email = ?";
        if ($stmt_check = $conn->prepare($sql_check_email)) {
            $stmt_check->bind_param("s", $email);
            $stmt_check->execute();
            $stmt_check->store_result();
            if ($stmt_check->num_rows > 0) {
                $errors[] = "Email sudah terdaftar. Silakan gunakan email lain atau login.";
            }
            $stmt_check->close();
        } else {
            $errors[] = "Terjadi kesalahan pada database saat memeriksa email.";
        }
    }

    // Nomor telepon bisa opsional, jadi tidak ada validasi wajib kosong,
    // tapi bisa ditambahkan validasi format jika diisi
    if (!empty($phone_number) && !preg_match('/^[0-9\-\+\s\(\)]{7,20}$/', $phone_number)) {
         // $errors[] = "Format nomor telepon tidak valid."; 
         // Validasi nomor telepon bisa kompleks, untuk sementara kita sederhanakan
    }


    if (empty($password)) {
        $errors[] = "Password wajib diisi.";
    } elseif (strlen($password) < 8) {
        $errors[] = "Password minimal harus 8 karakter.";
    } elseif (!preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $errors[] = "Password harus mengandung setidaknya satu huruf dan satu angka.";
    }


    if (empty($confirm_password)) {
        $errors[] = "Konfirmasi password wajib diisi.";
    } elseif ($password !== $confirm_password) {
        $errors[] = "Password dan konfirmasi password tidak cocok.";
    }

    if (!$agree_terms) {
        $errors[] = "Anda harus menyetujui Syarat & Ketentuan dan Kebijakan Privasi.";
    }

    // 2. Jika tidak ada error validasi, lanjutkan proses
    if (empty($errors)) {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Mulai transaksi untuk memastikan data user dan profilnya konsisten
        $conn->begin_transaction();
        try {
            // Siapkan query INSERT ke tabel users
            $sql_insert_user = "INSERT INTO users (name, email, password, phone_number) VALUES (?, ?, ?, ?)";
            $stmt_insert_user = $conn->prepare($sql_insert_user);
            if (!$stmt_insert_user) throw new Exception("Gagal mempersiapkan statement user: " . $conn->error);

            $phone_to_save = !empty($phone_number) ? $phone_number : null;
            $stmt_insert_user->bind_param("ssss", $full_name, $email, $hashed_password, $phone_to_save);
            if (!$stmt_insert_user->execute()) {
                if ($conn->errno == 1062) throw new Exception("Email sudah terdaftar. Silakan gunakan email lain.");
                throw new Exception("Gagal menyimpan data user: " . $stmt_insert_user->error);
            }

            $new_user_id = $stmt_insert_user->insert_id;
            $stmt_insert_user->close();

            // Buat entri profil kosong untuk pengguna baru, sama seperti di proses_firebase_login.php
            $sql_insert_profile = "INSERT INTO user_profiles (user_id) VALUES (?)";
            $stmt_insert_profile = $conn->prepare($sql_insert_profile);
            if (!$stmt_insert_profile) throw new Exception("Gagal mempersiapkan statement profil: " . $conn->error);

            $stmt_insert_profile->bind_param("i", $new_user_id);
            if (!$stmt_insert_profile->execute()) {
                throw new Exception("Gagal membuat profil untuk user baru: " . $stmt_insert_profile->error);
            }
            $stmt_insert_profile->close();

            // Jika semua berhasil, commit transaksi
            $conn->commit();

            // Registrasi berhasil
            $_SESSION['register_success'] = "Registrasi berhasil! Silakan login dengan akun Anda.";
            unset($_SESSION['form_data']); // Hapus data form dari session
            header('Location: register.php');
            exit;

        } catch (Exception $e) {
            $conn->rollback(); // Batalkan semua query jika ada yang gagal
            $_SESSION['register_error'] = $e->getMessage();
        }

    } else {
        // Jika ada error validasi, simpan error ke session
        $_SESSION['register_error'] = implode("<br>", $errors);
    }

    // Tutup koneksi database
    if (isset($conn)) {
        $conn->close();
    }

    // Redirect kembali ke halaman registrasi jika ada error atau setelah proses
    header('Location: register.php');
    exit;

} else {
    // Jika bukan metode POST, arahkan ke halaman registrasi
    header('Location: register.php');
    exit;
}
?>