

## AUDIT APLLICATION CONTROL E-COMMERCE CASUAL STEPS
## MATA KULIAH MANAJEMEN SUMBER DAYA IT




## Kelompok 6:
## 1. Helmy Ainur Ridho                                 23.11.5569
## 2. Faiz Nashwan Mudrik                            23.11.5597
## 3. M Dian Fauzi                                          23.11.5604
## 4. Muh Hafis Rizik Habibulah                     23.11.5615
## 5. Silvester Ramawijaya Kstaria Kinasih    23.11.5625







## PROGRAM STUDI  INFORMATIKA
## FAKULTAS ILMU KOMPUTER
## UNIVERSITAS AMIKOM YOGYAKARTA
## 2026

## LAPORAN KERTAS KERJA AUDIT
Audit Application Control - Sistem Informasi E-Commerce CASUAL STEPS
Objek Audit (URL) https://casual-steps.up.railway.app/
## Tanggal Pelaksanaan 07 Juli 2026
## Dilaksanakan Oleh Auditor Sistem Informasi
## Metode Audit
Black-box testing berbasis pengamatan eksternal (page fetch), tanpa akses
source code/database/kredensial admin, kecuali dinyatakan lain

Catatan Metodologi dan Keterbatasan Audit
● Pengambilan bukti dilakukan melalui HTTP GET (page fetch) tanpa kemampuan menjalankan JavaScript interaktif,
menyimpan cookie/session, atau mengirim form POST (login, registrasi, tambah ke keranjang, checkout, upload file).
● Pengujian yang membutuhkan sesi login aktif (Dashboard, Profile, Admin Panel, Upload, Export/Cetak Laporan, CRUD
data) tidak dapat dieksekusi langsung; temuan terkait ditandai eksplisit "Tidak dapat diuji - keterbatasan akses".
● Pada pengujian parameter GET (id, kategori, keyword) ditemukan indikasi bahwa alat fetch yang digunakan
kemungkinan mengambil hasil dari cache/index, bukan selalu memanggil server aplikasi secara live dengan parameter
persis yang dikirim. Oleh karena itu, hasil pengujian injection (SQL Injection/XSS via URL) tidak disimpulkan secara
konklusif dan ditandai sebagai indikatif, memerlukan verifikasi manual lanjutan.
● Seluruh temuan disusun berdasarkan bukti nyata yang berhasil diobservasi, bukan asumsi.
● Penomoran Kertas Kerja Audit (KKA) pada kategori Boundary Control dan Input Control telah diperbaiki agar berurutan
dan konsisten dengan Ringkasan Eksekutif (sebelumnya terdapat nomor KKA yang tidak berurutan/tidak konsisten
antara judul dan field Index).
● Prinsip penilaian Tingkat Risiko: temuan berstatus “Tidak dapat diuji - keterbatasan akses” pada umumnya diberi status
None karena tidak ada bukti yang dapat dievaluasi. Pengecualian diberlakukan untuk kontrol yang secara inheren kritikal
terhadap dampak (mis. metode penyimpanan password), yang tetap diberi tingkat risiko berdasarkan potensi dampak
(inherent risk) meskipun belum dapat diverifikasi secara langsung.

## Ringkasan Eksekutif
## No Kategori Jumlah Temuan Tidak Dapat Diuji Risiko Tertinggi
1 1. BOUNDARY CONTROL 5 1 Sedang
2 2. INPUT CONTROL 4 1 Sedang
3 3. PROCESS CONTROL 3 2 Sedang
4 4. OUTPUT CONTROL 3 2 None
5 5. DATABASE CONTROL 2 2 Tinggi
## 6
## 6. COMMUNICATION
## CONTROL
## 4 2 Rendah


## KATEGORI 1. BOUNDARY CONTROL
Kertas Kerja Audit BC-01
Objek Audit Halaman Login Pelanggan (index.php?url=Auth/login)
Index BC-01
## Kriteria Audit
OWASP ASVS - Authentication: mekanisme login harus dilindungi dari serangan
brute force/automated attack, salah satunya dengan CAPTCHA.
Tujuan Pemeriksaan Memastikan form login memiliki mekanisme anti-otomatisasi (bot/brute force).
## Bukti Audit
Hasil fetch halaman index.php?url=Auth/login menampilkan form Email,
Password, checkbox "Ingat Saya", dan tombol "Masuk Sekarang" tanpa elemen
CAPTCHA/reCAPTCHA/hCaptcha apa pun.

## Kondisi Existing
Form login hanya berisi email dan password tanpa verifikasi tambahan (CAPTCHA)
sebelum submit.
Temuan Tidak ditemukan implementasi CAPTCHA pada form login.
## Dampak
Aplikasi berpotensi rentan terhadap serangan brute force dan credential stuffing
menggunakan skrip otomatis.
## Tingkat Risiko Sedang
## Skor Risiko Numerik Likelihood 2 × Impact 2 =  4
## Rekomendasi
Menambahkan CAPTCHA (mis. Google reCAPTCHA v2/v3) pada form login dan
registrasi, terutama setelah beberapa kali percobaan gagal.
Tanggapan Auditee (belum diisi)

Kertas Kerja Audit BC-02
## Objek Audit Halaman Login Pelanggan
Index BC-02
## Kriteria Audit
OWASP ASVS 2.2 - Account lockout/rate limiting terhadap percobaan login gagal
berulang.

## Tujuan Pemeriksaan
Memastikan terdapat pembatasan jumlah percobaan login gagal (lock
account/delay).
## Bukti Audit
Tidak ditemukan indikator/pesan terkait pembatasan percobaan login pada
elemen statis form login yang dapat diamati.

## Kondisi Existing
Mekanisme pembatasan percobaan login tidak dapat dipastikan keberadaannya
dari sisi antarmuka (front-end).
## Temuan
Indikasi awal tidak adanya mekanisme account lockout/rate limiting yang terlihat
dari sisi pengguna. Pengujian aktual (submit password salah berkali-kali) tidak
dapat dilakukan karena keterbatasan alat (tidak dapat mengirim request
POST/menjaga sesi).
## Dampak
Jika benar tidak diterapkan di sisi server, akun pelanggan berisiko terhadap
serangan brute force.
## Tingkat Risiko Sedang
## Skor Risiko Numerik Likelihood 2 × Impact 2 = 4
## Rekomendasi
Terapkan penguncian akun sementara atau penambahan jeda setelah 3-5 kali
percobaan login gagal berturut-turut, serta catat percobaan gagal ke log audit.
Tanggapan Auditee (belum diisi)

Kertas Kerja Audit BC-03
Objek Audit Password Policy pada Form Registrasi
Index BC-03
Kriteria Audit OWASP ASVS 2.1 - Kebijakan password minimum (panjang & kompleksitas).
Tujuan Pemeriksaan Menilai kebijakan panjang/kompleksitas password yang dipersyaratkan sistem.
## Bukti Audit
Halaman Registrasi menampilkan keterangan di bawah field Password: "Minimal 8
karakter, mengandung huruf & angka."


## Kondisi Existing
Kebijakan password (panjang minimum 8 karakter, kombinasi huruf & angka)
sudah diinformasikan pada UI.
## Temuan
Secara kebijakan (client-side hint) sudah cukup baik. Namun validasi aktual di sisi
server tidak dapat diuji karena keterbatasan alat (tidak dapat submit form POST).
Tidak ada informasi mengenai syarat karakter khusus/simbol maupun kebijakan
masa berlaku password.
## Dampak
Jika validasi hanya di sisi client (JavaScript) tanpa validasi server, aturan dapat
dilewati (bypass) melalui request langsung ke server.
## Tingkat Risiko Sedang
## Skor Risiko Numerik Likelihood 2 × Impact 2 = 4
## Rekomendasi
Pastikan validasi password diterapkan di sisi server, tambahkan syarat karakter
spesial, dan pertimbangkan kebijakan riwayat password.
Tanggapan Auditee (belum diisi)

Kertas Kerja Audit BC-04
Objek Audit Session Management, Logout, "Ingat Saya", dan Access Control Dashboard/Admin
Index BC-04
## Kriteria Audit
OWASP ASVS 3.x - Session Management; kontrol otorisasi berbasis role (customer
vs admin).
## Tujuan Pemeriksaan
Menilai keamanan pengelolaan sesi, mekanisme logout, dan pemisahan hak akses
antara pelanggan dan admin.
## Bukti Audit
Tidak dapat diperoleh bukti langsung karena pengujian membutuhkan sesi login
aktif (cookie session) yang tidak dapat dipertahankan oleh alat fetch. Percobaan
menebak rute admin (url=Admin/login) menghasilkan pengalihan otomatis ke
halaman Registrasi (bukan error 404/500 mentah).

## Kondisi Existing
Struktur URL aplikasi menggunakan pola index.php?url=Controller/Action; rute
admin tidak ditemukan melalui penebakan pola umum dari sisi eksternal.
## Temuan
Tidak dapat diuji untuk aspek: masa berlaku sesi, regenerasi session ID setelah
login, keamanan fungsi Logout, serta pemisahan hak akses Dashboard Pelanggan
vs Admin Panel - karena keterbatasan akses (tidak memiliki kredensial login/sesi
aktif).
Dampak Tidak dapat dinilai dampaknya tanpa bukti lebih lanjut.
## Tingkat Risiko None (0)
## Rekomendasi
Auditee agar menyediakan akun demo (customer & admin) sehingga pengujian
session management, logout, dan access control dapat dilakukan secara
menyeluruh.
Tanggapan Auditee (belum diisi)

Kertas Kerja Audit BC-05
Objek Audit Halaman Keranjang Belanja (Cart/index) untuk pengguna tamu (guest)
Index BC-05
Kriteria Audit Kontrol otorisasi - akses fitur sesuai peruntukan peran pengguna.
## Tujuan Pemeriksaan
Memastikan halaman/fitur transaksional dapat diakses sesuai dengan peran
(guest vs member).
## Bukti Audit
Halaman Cart/index dapat diakses tanpa login dan menampilkan status
"Keranjang Anda Kosong"; pada halaman detail produk, tombol pembelian
berlabel "Login untuk Membeli".

## Kondisi Existing
Pengguna tamu dapat melihat halaman keranjang (kosong) namun diarahkan
untuk login terlebih dahulu sebelum dapat menambahkan produk/membeli.
## Temuan
Kontrol otorisasi transaksi (pembelian) sudah diarahkan ke proses login terlebih
dahulu, sesuai praktik umum e-commerce, tidak ditemukan celah bypass dari sisi
tampilan.
Dampak Tidak ada dampak negatif; kondisi ini sesuai (positive finding).
## Tingkat Risiko None (0)
Rekomendasi Tidak ada tindakan perbaikan diperlukan untuk poin ini.
Tanggapan Auditee (belum diisi)



## KATEGORI 2. INPUT CONTROL
Kertas Kerja Audit IC-01
## Objek Audit Form Registrasi & Login (field Email, Password, Nama)
Index IC-01
## Kriteria Audit
OWASP ASVS 5.x - Validasi Input; setiap input harus divalidasi format &
panjangnya baik di client maupun server.
Tujuan Pemeriksaan Menilai keberadaan validasi format (email, panjang password) pada form.
## Bukti Audit
Field Email menggunakan label "Email *" (mandatory), field Password disertai
keterangan format "Minimal 8 karakter, mengandung huruf & angka".

Kondisi Existing Indikasi validasi format sudah ada di level UI (hint dan tanda wajib isi).
## Temuan
Validasi client-side (hint UI) sudah teridentifikasi. Namun validasi server-side
(menolak email tanpa "@", password terlalu pendek, tag HTML/script pada field
Nama) tidak dapat diuji karena keterbatasan alat (tidak dapat submit form POST).
## Dampak
Jika validasi hanya berjalan di client dan dapat dilewati, data tidak
valid/berbahaya (termasuk payload XSS pada field Nama) berpotensi tersimpan ke
database.
## Tingkat Risiko Sedang (2)
## Skor Risiko Numerik Likelihood 2 × Impact 2 = 4
## Rekomendasi
Auditee agar memverifikasi bahwa seluruh validasi diterapkan di sisi server, bukan
hanya di JavaScript front-end.
Tanggapan Auditee (belum diisi)

Kertas Kerja Audit IC-02
Objek Audit Parameter URL id pada Product/detail dan kategori pada Product/index

Index IC-02
Kriteria Audit OWASP Top 10 - A03:2021 Injection (SQL Injection melalui parameter GET).
Tujuan Pemeriksaan Menilai ketahanan parameter URL terhadap payload SQL Injection sederhana.
## Bukti Audit
Percobaan menyisipkan karakter (id=45') dan id tidak valid (id=99999999)
menghasilkan respons yang berbeda-beda dan tidak konsisten antar percobaan,
sehingga tidak dapat dijadikan bukti yang andal.
## Kondisi Existing
Tidak ditemukan pesan error SQL mentah pada seluruh respons yang berhasil
diamati (indikasi positif awal).
## Temuan
Tidak dapat disimpulkan secara konklusif apakah parameter id/kategori rentan
SQL Injection karena hasil pengujian dari sisi saya tidak dapat diandalkan
(kemungkinan cache pada alat fetch). Tidak ditemukan bukti error SQL yang
bocor, namun ini tidak sama dengan "aman dari SQLi".
## Dampak
Apabila query database dibangun dengan penggabungan string (bukan prepared
statement), risiko SQL Injection tetap terbuka meski pesan error disembunyikan.
## Tingkat Risiko Sedang
## Skor Risiko Numerik Likelihood 2 × Impact 2 = 4
## Rekomendasi
Pastikan seluruh query menggunakan Prepared Statement/Parameterized Query
(PDO/MySQLi bind param). Lakukan pengujian SQLi menggunakan SQLMap pada
environment staging sebagai validasi lanjutan.
Tanggapan Auditee (belum diisi)

Kertas Kerja Audit IC-03
## Objek Audit
Form Login, Register, Lupa Password, Checkout, Tambah ke Keranjang (CSRF
## Protection)
Index IC-03
## Kriteria Audit
OWASP ASVS 4.2 - Setiap form yang mengubah state (state-changing request)
harus dilindungi CSRF token.
Tujuan Pemeriksaan Menilai keberadaan token anti-CSRF pada form.
## Bukti Audit
Pemeriksaan struktur HTML form Login/Register/Lupa Password yang berhasil
diambil tidak menunjukkan adanya hidden input bertipe token CSRF pada markup
yang teramati.

Kondisi Existing Tidak ditemukan elemen token CSRF pada form-form yang dapat diperiksa.

## Temuan
Indikasi awal tidak adanya proteksi CSRF token pada form
transaksional/otentikasi. Kemungkinan token digenerate dinamis melalui
JavaScript yang tidak tereksekusi pada mekanisme fetch statis, sehingga temuan
bersifat indikatif dan perlu konfirmasi source code.
## Dampak
Jika benar tidak ada CSRF token, penyerang dapat memanfaatkan sesi aktif korban
untuk melakukan aksi tanpa sepengetahuan pengguna melalui teknik CSRF.
## Tingkat Risiko Sedang
## Skor Risiko Numerik Likelihood 2 × Impact 2 = 4
## Rekomendasi
Implementasikan CSRF token unik per sesi/per form pada seluruh request yang
mengubah data, dan validasi token tersebut di sisi server.
Tanggapan Auditee (belum diisi)

Kertas Kerja Audit IC-04
Objek Audit Fitur Duplicate Data (Registrasi Email Ganda)
Index IC-04
Kriteria Audit Integritas data - sistem harus mencegah duplikasi data unik (email/username).
Tujuan Pemeriksaan Menilai apakah sistem mencegah pendaftaran dengan email yang sudah terdaftar.
## Bukti Audit
Tidak dapat diuji karena memerlukan submit form registrasi (POST) yang tidak
dapat dilakukan melalui alat fetch yang tersedia.
Kondisi Existing Tidak dapat ditentukan.
Temuan Tidak dapat diuji - keterbatasan alat (tidak dapat mengirim data registrasi).
Dampak Tidak dapat dinilai.
## Tingkat Risiko None
## Rekomendasi
Auditee agar melakukan pengujian manual: daftar dua kali dengan email yang
sama dan pastikan sistem menolak dengan pesan yang jelas.
Tanggapan Auditee (belum diisi)



## KATEGORI 3. PROCESS CONTROL
Kertas Kerja Audit PC-01
Objek Audit Menu Navigasi "PRODUCT" pada seluruh halaman
Index PC-01
## Kriteria Audit
Konsistensi proses navigasi - tautan menu harus konsisten mengarahkan ke fungsi
yang sesuai.
Tujuan Pemeriksaan Menilai konsistensi hasil saat mengakses menu Produk secara langsung.
## Bukti Audit
Permintaan ke index.php?url=Product/index (tautan menu "PRODUCT") pada satu
percobaan mengembalikan konten halaman Login, pada percobaan lain dengan
parameter tambahan mengembalikan konten halaman Lupa Password.
Kondisi Existing Perilaku routing untuk Product/index tidak konsisten pada pengujian eksternal.
## Temuan
Ditemukan indikasi inkonsistensi respons pada endpoint Product/index.
Kemungkinan besar dipengaruhi perilaku caching pada alat fetch, sehingga
bersifat indikatif dan wajib diverifikasi langsung oleh auditee.
## Dampak
Jika inkonsistensi ini nyata terjadi pada aplikasi, pengguna dapat mengalami
kegagalan mengakses katalog produk, mengganggu proses bisnis inti (penjualan).
## Tingkat Risiko Sedang
## Skor Risiko Numerik Likelihood 2 × Impact 2 = 4
## Rekomendasi
Auditee melakukan pengujian ulang menu "PRODUCT" secara manual dari
beberapa browser/perangkat berbeda, dan memeriksa log server pada waktu
pengujian.
Tanggapan Auditee (belum diisi)

Kertas Kerja Audit PC-02
## Objek Audit Proses Checkout, Update Status Pesanan, Manajemen Stok
Index PC-02
## Kriteria Audit
Konsistensi data & validasi proses bisnis (stok tidak boleh minus, status pesanan
sesuai alur/workflow).
Tujuan Pemeriksaan Menilai validasi proses transaksi pembelian dan pengelolaan stok.
## Bukti Audit
Tidak dapat diakses karena memerlukan akun customer aktif (login) untuk
checkout, serta akun admin untuk manajemen stok/pesanan.
Kondisi Existing Tidak dapat ditentukan dari luar.
## Temuan
Tidak dapat diuji - keterbatasan akses (memerlukan sesi login pelanggan dan
admin).
Dampak Tidak dapat dinilai.

## Tingkat Risiko None
## Rekomendasi
Auditee agar menyediakan kredensial akun demo customer dan admin agar
auditor dapat menelusuri validasi stok, konsistensi status pesanan, dan
penanganan race condition.
Tanggapan Auditee (belum diisi)

Kertas Kerja Audit PC-03
Objek Audit Logging aktivitas transaksi & login
Index PC-03
## Kriteria Audit
Kontrol proses - setiap transaksi/perubahan data penting harus tercatat dalam log
(audit trail).
Tujuan Pemeriksaan Menilai keberadaan mekanisme logging pada proses bisnis.
## Bukti Audit
Tidak ada antarmuka publik yang menampilkan log aktivitas; keberadaan logging
internal tidak dapat diverifikasi tanpa akses backend/database.
Kondisi Existing Tidak dapat ditentukan dari sisi eksternal.
## Temuan
Tidak dapat diuji - keterbatasan akses (memerlukan akses ke source
code/database backend).
Dampak Tidak dapat dinilai.
## Tingkat Risiko None
## Rekomendasi
Auditee agar memastikan tabel log/audit trail mencatat waktu, user, aksi, dan
alamat IP untuk aktivitas penting.
Tanggapan Auditee (belum diisi)



## KATEGORI 4. OUTPUT CONTROL
Kertas Kerja Audit OC-01
Objek Audit Halaman Detail Produk (Onitsuka Tiger Tokuten Piedmont Grey Metropolis, ID 45)
Index OC-01
## Kriteria Audit
Akurasi informasi output - data yang ditampilkan ke pengguna harus lengkap dan
akurat.
Tujuan Pemeriksaan Menilai kelengkapan & keakuratan tampilan informasi produk.
## Bukti Audit
Halaman detail produk menampilkan nama produk, kategori (Onitsuka Tiger), dan
informasi terkait secara wajar tanpa anomali data yang teramati.
Kondisi Existing Informasi produk tersaji dengan format yang konsisten.
Temuan Tidak ditemukan anomali output pada halaman yang diperiksa.
Dampak Tidak ada.
## Tingkat Risiko None
## Rekomendasi
Tidak ada tindakan diperlukan; disarankan tetap dilakukan pengecekan berkala
terhadap akurasi harga/stok yang tampil vs data aktual di database.
Tanggapan Auditee (belum diisi)

Kertas Kerja Audit OC-02
Objek Audit Fitur Export Laporan & Cetak (Admin/Dashboard)
Index OC-02
## Kriteria Audit
Hak akses output - fitur export/cetak data hanya boleh diakses oleh pihak
berwenang, dan data yang diekspor harus sesuai hak akses peran.
Tujuan Pemeriksaan Menilai kontrol akses dan keakuratan fitur export/cetak.
## Bukti Audit
Fitur ini kemungkinan berada di dalam Admin Panel/Dashboard yang memerlukan
login; tidak dapat diakses tanpa kredensial.
Kondisi Existing Tidak dapat ditentukan.
Temuan Tidak dapat diuji - keterbatasan akses (memerlukan login admin).
Dampak Tidak dapat dinilai.
## Tingkat Risiko None
## Rekomendasi
Auditee agar menyediakan akses demo admin untuk pengujian kesesuaian hak
akses export dan potensi IDOR pada endpoint export/laporan.
Tanggapan Auditee (belum diisi)


Kertas Kerja Audit OC-03
Objek Audit Notifikasi Transaksi (Email/Notifikasi Pemesanan)
Index OC-03
## Kriteria Audit
Output control - sistem harus memberi notifikasi yang jelas atas hasil proses bisnis
## (sukses/gagal).
Tujuan Pemeriksaan Menilai keberadaan notifikasi ke pengguna atas transaksi.
## Bukti Audit
Tidak dapat diuji tanpa melakukan transaksi/registrasi aktual (memerlukan POST
& email aktif).
Kondisi Existing Tidak dapat ditentukan.
Temuan Tidak dapat diuji - keterbatasan akses.
Dampak Tidak dapat dinilai.
## Tingkat Risiko None
## Rekomendasi
Auditee agar menguji manual apakah setelah checkout/registrasi pengguna
menerima notifikasi (email/on-screen) yang informatif.
Tanggapan Auditee (belum diisi)



## KATEGORI 5. DATABASE CONTROL
Kertas Kerja Audit DC-01
## Objek Audit Integritas Data, Backup, Recovery, Audit Trail, Penghapusan Data
Index DC-01
## Kriteria Audit
Kontrol basis data - ketersediaan backup rutin, mekanisme recovery, audit trail,
dan kebijakan penghapusan data (soft delete vs hard delete).
Tujuan Pemeriksaan Menilai tata kelola database aplikasi.
## Bukti Audit
Tidak dapat diperiksa karena memerlukan akses langsung ke server/database
(phpMyAdmin, panel Railway, atau source code).
Kondisi Existing Tidak dapat ditentukan dari luar aplikasi.
## Temuan
Tidak dapat diuji - keterbatasan akses (bukan kewenangan/kemampuan pengujian
black-box dari sisi pengguna).
Dampak Tidak dapat dinilai.
## Tingkat Risiko None
## Rekomendasi
Auditee agar memastikan backup database terjadwal dan disimpan terpisah,
mekanisme soft delete untuk data transaksi penting, serta tabel audit trail untuk
perubahan data kritis.
Tanggapan Auditee (belum diisi)

Kertas Kerja Audit DC-02
Objek Audit Penyimpanan Kredensial Pengguna (Password Storage)
Index DC-02
## Kriteria Audit
OWASP ASVS 2.4 - Password harus disimpan dalam bentuk hash yang kuat
(bcrypt/Argon2), bukan plaintext atau hash lemah.
Tujuan Pemeriksaan Menilai metode penyimpanan password pengguna.
Bukti Audit Tidak dapat diverifikasi tanpa akses ke database/source code backend.
Kondisi Existing Tidak dapat ditentukan.
## Temuan
Tidak dapat diuji - keterbatasan akses. Namun ini merupakan aspek kritikal yang
wajib dipastikan oleh auditee secara internal.
## Dampak
Jika password disimpan plaintext/hash lemah, kebocoran database akan langsung
mengekspos seluruh kredensial pengguna.
## Tingkat Risiko Tinggi
## Skor Risiko Numerik Likelihood 3 × Impact 3 = 9

## Rekomendasi
Pastikan seluruh password disimpan menggunakan fungsi hash adaptif seperti
password_hash() (bcrypt) di PHP, bukan md5()/sha1()/plaintext.
Tanggapan Auditee (belum diisi)



## KATEGORI 6. COMMUNICATION CONTROL
Kertas Kerja Audit CC-01
Objek Audit Protokol Komunikasi Website (HTTPS/SSL/TLS)
Index CC-01
## Kriteria Audit
Seluruh komunikasi data (khususnya form login/registrasi/transaksi) wajib
dienkripsi menggunakan HTTPS/TLS.
Tujuan Pemeriksaan Memastikan seluruh halaman diakses melalui protokol aman.
## Bukti Audit
Seluruh URL yang diakses selama audit konsisten menggunakan skema
https://casual-steps.up.railway.app/... (platform Railway menyediakan TLS
otomatis by default).

Kondisi Existing Website dapat diakses melalui HTTPS.
Temuan Protokol HTTPS sudah diterapkan pada domain utama.
Dampak Positif - data dalam transit terenkripsi antara browser dan server.
## Tingkat Risiko None
## Rekomendasi
Pastikan tidak ada opsi akses melalui HTTP murni tanpa redirect paksa ke HTTPS,
dan konfigurasikan header HSTS (Strict-Transport-Security).
Tanggapan Auditee (belum diisi)

Kertas Kerja Audit CC-02
Objek Audit Metadata Halaman (Open Graph / Twitter Card meta tag)
Index CC-02
## Kriteria Audit
Konsistensi penggunaan protokol aman pada seluruh referensi URL, termasuk
metadata halaman.
Tujuan Pemeriksaan Memastikan tidak ada referensi URL tidak aman (mixed content) pada halaman.
## Bukti Audit
Pada tag meta og:url dan twitter:url di beberapa halaman (Login, Lupa Password),
nilai yang tercantum menggunakan skema http:// (bukan https://).
## Kondisi Existing
Konten halaman utama sudah HTTPS, namun metadata Open Graph/Twitter Card
masih mereferensikan skema http://.
## Temuan
Ditemukan inkonsistensi protokol pada meta tag (mixed reference), meskipun
tidak berdampak langsung pada enkripsi trafik pengguna karena hanya bersifat
metadata untuk keperluan social sharing/SEO.

## Dampak
Dampak keamanan rendah/tidak langsung; berpotensi menyebabkan tautan yang
dibagikan ke media sosial mengarah ke versi tidak aman atau tidak konsisten
dengan branding HTTPS.
## Tingkat Risiko Rendah
## Skor Risiko Numerik Likelihood 2 × Impact 2 = 4
## Rekomendasi
Perbarui seluruh nilai meta tag og:url dan twitter:url agar konsisten menggunakan
skema https://.
Tanggapan Auditee (belum diisi)

Kertas Kerja Audit CC-03
Objek Audit HTTP Security Headers (CSP, X-Frame-Options, X-Content-Type-Options, HSTS)
Index CC-03
## Kriteria Audit
OWASP Secure Headers Project - aplikasi web sebaiknya menerapkan security
header untuk mitigasi clickjacking, MIME sniffing, dan XSS.
Tujuan Pemeriksaan Memastikan header keamanan HTTP diterapkan pada respons server.
## Bukti Audit
Alat fetch yang tersedia tidak menampilkan raw HTTP response header, sehingga
keberadaan header seperti Content-Security-Policy, X-Frame-Options, X-Content-
Type-Options, Strict-Transport-Security tidak dapat diverifikasi.
Kondisi Existing Tidak dapat ditentukan dari alat yang tersedia.
Temuan Tidak dapat diuji - keterbatasan alat (tidak dapat membaca header HTTP mentah).
Dampak Tidak dapat dinilai tanpa bukti lebih lanjut.
## Tingkat Risiko None
## Rekomendasi
Auditee agar memeriksa header respons melalui browser DevTools (tab Network)
atau tools seperti curl -I / securityheaders.com, dan menambahkan header yang
belum ada.
Tanggapan Auditee (belum diisi)

Kertas Kerja Audit CC-04
Objek Audit Cookie Security (Session Cookie Attributes)
Index CC-04
## Kriteria Audit
Cookie sesi harus memiliki atribut HttpOnly, Secure, dan SameSite untuk
mencegah pencurian sesi via XSS/CSRF.
Tujuan Pemeriksaan Memastikan cookie sesi dikonfigurasi secara aman.
## Bukti Audit
Tidak dapat diperiksa karena memerlukan sesi login aktif dan kemampuan
membaca header Set-Cookie, di luar kapabilitas alat fetch statis.
Kondisi Existing Tidak dapat ditentukan.

Temuan Tidak dapat diuji - keterbatasan akses/alat.
Dampak Tidak dapat dinilai.
## Tingkat Risiko None
## Rekomendasi
Auditee agar memeriksa konfigurasi session cookie (session.cookie_httponly,
session.cookie_secure, session.cookie_samesite pada PHP) melalui DevTools >
Application > Cookies setelah login.
Tanggapan Auditee (belum diisi)
