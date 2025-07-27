<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../ADMIN MENU/db_connect.php';

use Kreait\Firebase\Factory;
use Kreait\Firebase\Exception\Auth\IdTokenVerificationFailed; // Menggunakan Exception yang benar untuk v5+
use Kreait\Firebase\Exception\Auth\InvalidToken;

header('Content-Type: application/json');

$response = ['success' => false, 'message' => 'Terjadi kesalahan tidak diketahui.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && (strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false)) {
    $input = json_decode(file_get_contents('php://input'), true);
    $idToken = $input['idToken'] ?? null;

    if (!$idToken) {
        $response['message'] = 'ID Token tidak ditemukan.';
        echo json_encode($response);
        exit;
    }

    try {
        // Ganti dengan nama file kunci proyek BARU Anda
        $factory = (new Factory())->withServiceAccount(__DIR__ . '/casula-steps-firebase-adminsdk-fbsvc-d0c59a3521.json');
        $auth = $factory->createAuth();

        $verifiedIdToken = $auth->verifyIdToken($idToken);

        $uid = $verifiedIdToken->claims()->get('sub'); // atau 'uid'
        $email = $verifiedIdToken->claims()->get('email');
        $name = $verifiedIdToken->claims()->get('name') ?? 'Pengguna Google';

        // Cek pengguna di database lokal
        $sql_check_user = "SELECT id, name, email, account_status FROM users WHERE email = ?";
        if ($stmt_check = $conn->prepare($sql_check_user)) {
            $stmt_check->bind_param("s", $email);
            $stmt_check->execute();
            $result_check = $stmt_check->get_result();

            $user_id_local = null;
            $user_name_local = null;

            if ($result_check->num_rows > 0) {
                $user_data = $result_check->fetch_assoc();
                if ($user_data['account_status'] === 'nonaktif') {
                    $response['message'] = 'Akun Anda telah dinonaktifkan.';
                    echo json_encode($response);
                    exit;
                }
                $user_id_local = $user_data['id'];
                $user_name_local = $user_data['name'];
                // PENYEMPURNAAN: Update nama jika berbeda dengan di Google
                if ($user_data['name'] !== $name) {
                    $sql_update_name = "UPDATE users SET name = ? WHERE id = ?";
                    if ($stmt_update = $conn->prepare($sql_update_name)) {
                        $stmt_update->bind_param("si", $name, $user_id_local);
                        $stmt_update->execute();
                        $stmt_update->close();
                    }
                }
            } else {
                // Pengguna baru, daftarkan ke database lokal dalam sebuah transaksi
                $conn->begin_transaction();
                try {
                    $sql_insert_user = "INSERT INTO users (name, email, password, account_status) VALUES (?, ?, ?, 'aktif')";
                    if ($stmt_insert = $conn->prepare($sql_insert_user)) {
                        $dummy_password_hash = password_hash(uniqid('firebase_user_', true), PASSWORD_DEFAULT);
                        $stmt_insert->bind_param("sss", $name, $email, $dummy_password_hash);
                        if (!$stmt_insert->execute()) {
                            throw new Exception("Gagal mendaftarkan pengguna baru: " . $stmt_insert->error);
                        }
                        $user_id_local = $stmt_insert->insert_id;
                        $user_name_local = $name;
                        $stmt_insert->close();

                        // Buat entri profil kosong untuk pengguna baru
                        $sql_insert_profile = "INSERT INTO user_profiles (user_id) VALUES (?)";
                        if ($stmt_profile = $conn->prepare($sql_insert_profile)) {
                            $stmt_profile->bind_param("i", $user_id_local);
                            if (!$stmt_profile->execute()) {
                                throw new Exception("Gagal membuat profil pengguna: " . $stmt_profile->error);
                            }
                            $stmt_profile->close();
                        } else {
                            throw new Exception("Gagal mempersiapkan statement profil pengguna: " . $conn->error);
                        }

                        $conn->commit();
                    } else {
                        throw new Exception("Gagal mempersiapkan statement pendaftaran pengguna: " . $conn->error);
                    }
                } catch (Exception $e) {
                    $conn->rollback();
                    throw $e; // Lemparkan kembali exception untuk ditangkap oleh blok catch di luar
                }
            }
            $stmt_check->close();

            // Buat sesi login PHP
            $_SESSION['user_id'] = $user_id_local;
            $_SESSION['user_name'] = $user_name_local;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_loggedin'] = true;

            // Kirim respons sukses
            $response['success'] = true;
            $response['message'] = 'Login berhasil.';
            $response['user_name'] = $user_name_local;
            $response['redirect_url'] = 'index.php';
        } else {
            throw new Exception("Gagal mempersiapkan pengecekan pengguna.");
        }
    } catch (IdTokenVerificationFailed $e) {
        // Menangkap error spesifik dari v5+
        $response['message'] = 'Verifikasi token gagal: ' . $e->getMessage();
        http_response_code(401);
        echo json_encode($response);
        exit;
    } catch (\Exception $e) {
        // Menangkap error lainnya (misal: error database)
        $response['message'] = 'Terjadi kesalahan server: ' . $e->getMessage();
        http_response_code(500);
        echo json_encode($response);
        exit;
    }
} else {
    $response['message'] = 'Metode request tidak valid.';
}

echo json_encode($response);

if (isset($conn)) {
    $conn->close();
}
