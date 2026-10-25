# FindIT - Platform Barang Hilang dan Temuan Kampus

Website PHP + MySQL untuk tugas kelompok ETS.

| Peran | Bisa apa |
|---|---|
| Pengunjung | Melihat beranda, mencari di daftar barang, membuka detail (tanpa login) |
| User (terdaftar) | Membuat laporan barang hilang/temuan, memantau status verifikasi, memperbaiki laporan yang ditolak |
| Admin | Memverifikasi (setujui/tolak) laporan user, CRUD semua laporan barang, tandai barang dikembalikan |
| Superadmin | CRUD akun admin |

Alur: user melapor, status **Menunggu**, admin menyetujui, laporan **Tayang** di daftar barang.
Kalau ditolak, user melihat alasannya dan bisa memperbaiki lalu kirim ulang.

## Menjalankan di komputer sendiri (XAMPP)

1. Clone repo ini ke `C:\xampp\htdocs\findit`.
2. Buka XAMPP, **Start** Apache dan MySQL.
3. Buka `http://localhost/phpmyadmin`, buat database **findit_db** (collation `utf8mb4_general_ci`).
4. Pilih database itu, tab **Import**, pilih file `database/findit_db.sql`, klik **Import**.
5. Buka `http://localhost/findit/`.

Pengaturan database default (`root`, tanpa password) ada di `config/koneksi.php`.

## Akun contoh

| Role | Username | Password |
|---|---|---|
| Superadmin | `superadmin` | `admin123` |
| Admin | `admin` | `admin123` |
| User | `mahasiswa` | `user123` |

**Ganti password sebelum website dihosting.** Superadmin bisa mengganti password admin lewat menu Ubah.
Pengguna baru bisa mendaftar sendiri lewat halaman Daftar.
Untuk mengganti password superadmin, jalankan di phpMyAdmin (tab SQL), ganti `PASSWORD_BARU` dulu:
buat hash lewat file PHP sementara berisi `<?php echo password_hash('PASSWORD_BARU', PASSWORD_DEFAULT);`,
lalu `UPDATE users SET password='HASIL_HASH' WHERE username='superadmin';`

## Kalau database sudah terlanjur diimpor dari versi lama

Jangan import ulang (data bisa hilang). Cukup jalankan `database/migrasi_role_user.sql` lewat tab **SQL** di phpMyAdmin.
Lalu buat akun user lewat halaman Daftar.

## Struktur folder

```
config/        koneksi.php (database + fungsi bantu), hosting.example.php
includes/      header.php, footer.php, auth.php, kartu.php, form_barang.php, validasi_barang.php
assets/        css/style.css, js/script.js, uploads/ (foto barang)
user/          dashboard, lapor, edit, hapus laporan (role user)
admin/         CRUD barang + verifikasi laporan (role admin)
superadmin/    CRUD akun admin (role superadmin)
database/      findit_db.sql
index.php      halaman pengunjung    detail.php   detail barang
tentang.php    cara kerja            login.php / logout.php
```

Mengubah tampilan: warna dan ukuran ada di bagian `:root` paling atas `assets/css/style.css`.
Navbar ada di `includes/header.php`, footer di `includes/footer.php`.

## Hosting di InfinityFree

1. Di panel InfinityFree buat database MySQL, catat host (`sqlXXX.infinityfree.com`), nama db, user, dan password.
2. Buka phpMyAdmin dari panel, pilih database itu, **Import** `database/findit_db.sql`.
3. Salin `config/hosting.example.php` menjadi `config/hosting.php`, isi dengan data langkah 1.
4. Upload semua file ke folder `htdocs` lewat File Manager atau FTP (sertakan `config/hosting.php`).
5. Pastikan folder `assets/uploads` bisa ditulis (permission 755/775), lalu tes login dan upload foto.

`BASE_URL` dideteksi otomatis, jadi tidak ada path yang perlu diubah saat pindah dari lokal ke hosting.

## Kerja kelompok dengan Git

- Jangan commit langsung ke `main`. Buat branch per fitur: `git checkout -b fitur-nama`.
- `git pull origin main` sebelum mulai kerja. Buka Pull Request di GitHub untuk digabung.
- `config/hosting.php` dan isi `assets/uploads/` sengaja tidak ikut Git (lihat `.gitignore`).
- Kalau struktur tabel berubah, export ulang `database/findit_db.sql` dan kabari grup agar semua import ulang.
