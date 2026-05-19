<?php
// GANTI "PasswordAdmin123!" DENGAN PASSWORD YANG ANDA INGINKAN UNTUK ADMIN
$passwordAsli = "Admin"; 
$hashPassword = password_hash($passwordAsli, PASSWORD_DEFAULT);

echo "Password Asli yang akan Anda gunakan untuk login: " . htmlspecialchars($passwordAsli) . "<br>";
echo "Hasil Hash (untuk dimasukkan ke database): " . htmlspecialchars($hashPassword);
?>