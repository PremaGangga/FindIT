-- =========================================================
-- FindIT - struktur dan data awal
-- Cara pakai LOKAL : phpMyAdmin -> buat database "findit_db" -> pilih -> tab Import
-- Cara pakai HOSTING: buat database di panel InfinityFree, buka phpMyAdmin-nya,
--                     pilih database itu, lalu Import file ini.
-- =========================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS barang;
DROP TABLE IF EXISTS kategori;
DROP TABLE IF EXISTS users;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(100) NOT NULL,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('superadmin','admin','user') NOT NULL DEFAULT 'user',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE kategori (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE barang (
  id INT AUTO_INCREMENT PRIMARY KEY,
  judul VARCHAR(150) NOT NULL,
  deskripsi TEXT,
  kategori_id INT NULL,
  lokasi VARCHAR(100),
  tanggal DATE,
  status ENUM('hilang','ditemukan','dikembalikan') NOT NULL DEFAULT 'hilang',
  verifikasi ENUM('pending','disetujui','ditolak') NOT NULL DEFAULT 'disetujui',
  catatan_admin VARCHAR(255) NULL,
  foto VARCHAR(255) NULL,
  kontak VARCHAR(100),
  created_by INT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_barang_kategori FOREIGN KEY (kategori_id) REFERENCES kategori(id) ON DELETE SET NULL,
  CONSTRAINT fk_barang_user FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Akun awal. Password: superadmin & admin = admin123, mahasiswa = user123
-- (GANTI sebelum presentasi/hosting!)
INSERT INTO users (nama, username, password, role) VALUES
('Super Admin', 'superadmin', '$2y$10$JgyA.EYhItXekAGAcSiocOywiPS0LEjzaNAgQJZUkf2PesO0QKz/S', 'superadmin'),
('Admin Kampus', 'admin', '$2y$10$JgyA.EYhItXekAGAcSiocOywiPS0LEjzaNAgQJZUkf2PesO0QKz/S', 'admin'),
('Mahasiswa Contoh', 'mahasiswa', '$2y$10$1pHzMU1/jYHAgI/eIAZgVu7Wobar.FYue6VT44C89duUZuVPJdRCO', 'user');

INSERT INTO kategori (nama) VALUES
('Elektronik'), ('Tas & Dompet'), ('Dokumen'), ('Pakaian'), ('Aksesoris'), ('Lainnya');

-- Contoh data (tanggal mengikuti hari ini)
INSERT INTO barang (judul, deskripsi, kategori_id, lokasi, tanggal, status, kontak, created_by) VALUES
('Tas Ransel Hitam', 'Tas ransel warna hitam merek Eiger, terdapat buku dan laptop di dalamnya.', 2, 'Perpustakaan Pusat', DATE_SUB(CURDATE(), INTERVAL 2 DAY), 'hilang', '0812-0000-0001 (Rina)', 2),
('iPhone 13', 'Ditemukan di area kantin kampus. Kondisi masih baik, bisa diambil di pos keamanan.', 1, 'Gedung A, Lantai 2', DATE_SUB(CURDATE(), INTERVAL 3 DAY), 'ditemukan', 'Pos Keamanan Gedung A', 2),
('Dompet Kulit Coklat', 'Dompet berisi beberapa kartu dan uang tunai. Mohon hubungi admin untuk pengambilan.', 2, 'Area Parkir Motor', DATE_SUB(CURDATE(), INTERVAL 4 DAY), 'ditemukan', 'Admin FindIT (WhatsApp 0812-0000-0002)', 2),
('Hoodie Putih', 'Hoodie warna putih dengan logo kampus. Terakhir terlihat saat jam kuliah.', 4, 'Kelas B3', DATE_SUB(CURDATE(), INTERVAL 6 DAY), 'hilang', '0812-0000-0003 (Dimas)', 2),
('Kartu Tanda Mahasiswa', 'KTM atas nama Anisa, ditemukan di dekat mesin fotokopi.', 3, 'Gedung Rektorat', DATE_SUB(CURDATE(), INTERVAL 7 DAY), 'ditemukan', 'Loket Akademik', 2),
('Headphone Nirkabel Biru', 'Headphone over-ear warna biru tua dengan case hitam.', 1, 'Laboratorium Komputer', DATE_SUB(CURDATE(), INTERVAL 9 DAY), 'hilang', '0812-0000-0004 (Salsa)', 2),
('Jam Tangan Digital', 'Jam tangan hitam dengan tali karet. Sudah diambil pemiliknya.', 5, 'Lapangan Basket', DATE_SUB(CURDATE(), INTERVAL 12 DAY), 'dikembalikan', 'Admin FindIT', 2),
('Kunci Motor dan Gantungan', 'Kunci motor Honda dengan gantungan kucing warna oranye.', 6, 'Kantin Pusat', DATE_SUB(CURDATE(), INTERVAL 14 DAY), 'hilang', '0812-0000-0005 (Bagas)', 2);

-- Contoh laporan dari pengguna yang menunggu verifikasi admin (belum tampil di publik)
INSERT INTO barang (judul, deskripsi, kategori_id, lokasi, tanggal, status, verifikasi, kontak, created_by) VALUES
('Payung Lipat Hitam', 'Payung lipat hitam dengan gagang biru, tertinggal di bangku taman.', 6, 'Taman Fakultas', DATE_SUB(CURDATE(), INTERVAL 1 DAY), 'ditemukan', 'pending', '0812-0000-0006 (Mahasiswa Contoh)', 3);
