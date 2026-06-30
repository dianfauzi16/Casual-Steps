<div align="center">
  <h1>👟 Casual-Steps</h1>
  <p><strong>Modern & Scalable E-Commerce Platform</strong></p>

  [![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=flat-square&logo=php)](https://www.php.net/)
  [![MySQL](https://img.shields.io/badge/MySQL-Database-4479A1?style=flat-square&logo=mysql)](https://www.mysql.com/)
  [![Docker](https://img.shields.io/badge/Docker-Ready-2496ED?style=flat-square&logo=docker)](https://www.docker.com/)
  [![Railway](https://img.shields.io/badge/Deployed_on-Railway-0B0D0E?style=flat-square&logo=railway)](https://railway.app/)
  
  <br />
  <b><a href="https://casual-steps.up.railway.app/">🚀 Lihat Live Demo</a></b>
</div>

---

## 📖 Tentang Project
**Casual-Steps** adalah aplikasi web e-commerce komprehensif yang dirancang khusus untuk penjualan sepatu kasual kekinian. Project ini dibangun dengan arsitektur **MVC (Model-View-Controller)** menggunakan PHP Native, yang membuktikan pemahaman mendalam tentang struktur sistem pemrograman tanpa bergantung pada framework besar. 

Aplikasi ini tidak hanya sekadar katalog online, melainkan sistem yang siap untuk skala produksi (*production-ready*) yang telah diintegrasikan dengan berbagai layanan modern cloud dan berjalan stabil di atas infrastruktur container (Docker).

---

## ✨ Fitur Unggulan

### 🔐 Keamanan & Autentikasi
- **Google OAuth Login**: Integrasi Firebase Auth untuk login dengan 1-klik yang mudah dan aman.
- **Role-Based Access Control (RBAC)**: Pemisahan hak akses antara Pelanggan dan Administrator.
- **Secure Credentials**: Penggunaan Environment Variables (`.env`) untuk merahasiakan API Key & sandi database.

### 💳 Integrasi Pihak Ketiga
- **Cloudinary Storage**: Penyimpanan aset gambar (produk & profil) secara *stateless*. Lebih cepat, dan tidak membebani kapasitas server (optimal untuk CI/CD).
- **Midtrans Payment Gateway**: Checkout otomatis yang mendukung simulasi berbagai metode pembayaran (GoPay, Virtual Account, Credit Card) via Sandbox.
- **SMTP Email Service**: Pengiriman email otomatis.

### 🖥️ Admin Dashboard & Manajemen Data
- **Manajemen Produk & Kategori**: CRUD (Create, Read, Update, Delete) produk secara real-time dengan upload gambar instan ke Cloudinary.
- **Manajemen Pesanan (Order)**: Pemantauan transaksi dari pengguna.
- **Peringatan Stok & Dashboard Analytics**: Ringkasan data penjualan dan notifikasi peringatan jika ada produk dengan stok menipis.

### 🎨 Frontend UI / UX
- **Desain Modern**: Pendekatan antarmuka kekinian menggunakan Bootstrap 5, dipadukan dengan efek *Glassmorphism*.
- **Micro-animations**: Transisi mulus yang responsif menggunakan AOS (Animate on Scroll).
- **Responsive 100%**: Pengalaman pengguna yang nyaman di Desktop, Tablet, maupun Mobile.

---

## 🛠️ Arsitektur & Teknologi

**Backend & Infrastruktur:**
- **Bahasa**: PHP 8.2
- **Arsitektur**: Custom MVC (Model-View-Controller)
- **Database**: MySQL 8.0
- **Containerization**: Docker (Dockerfile khusus untuk environment Apache+PHP)
- **Deployment**: Railway Cloud Platform

**Frontend:**
- **Framework CSS**: Bootstrap 5.3
- **Animasi**: AOS (Animate on Scroll)
- **Ikon**: FontAwesome 6

**Libraries / SDK (via Composer):**
- `kreait/firebase-php` (Verifikasi Token Google)
- `cloudinary/cloudinary_php` (Manajemen Aset)
- `midtrans/midtrans-php` (Payment Gateway)
- `vlucas/phpdotenv` (Manajemen Variabel Lingkungan)

---

## 🚀 Instalasi Lokal (Development)

Untuk menjalankan project ini di komputermu (misal menggunakan Laragon/XAMPP):

1. **Clone repository ini**
   ```bash
   git clone https://github.com/dianfauzi16/Casual-Steps.git
   cd Casual-Steps
   ```

2. **Install dependensi PHP via Composer**
   ```bash
   composer install
   ```

3. **Konfigurasi Environment**
   - Duplikat file `.env.example` (jika ada) dan ubah namanya menjadi `.env`.
   - Isi konfigurasi koneksi Database, Cloudinary URL, kredensial SMTP, Firebase, dan Midtrans Server Key kamu.

4. **Import Database**
   - Buka `phpMyAdmin` atau terminal MySQL.
   - Buat database baru bernama `pemro-web`.
   - Import file `pemro-web-railway-utf8.sql` ke database tersebut.

5. **Jalankan Aplikasi**
   - Akses via web server lokal (contoh: `http://localhost/Casual-Steps/`)

---

## 👨‍💻 Author

**M Dian Fauzi**
- GitHub: [@dianfauzi16](https://github.com/dianfauzi16)

> *Project ini dibuat sebagai dedikasi dan demonstrasi keahlian dalam Fullstack Web Development, Integrasi Cloud API, dan Docker Containerization.*
