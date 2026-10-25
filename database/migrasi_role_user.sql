-- =========================================================
-- Migrasi untuk database findit_db yang SUDAH diimpor sebelumnya.
-- Menambah role "user" dan kolom verifikasi laporan.
-- Cara: phpMyAdmin -> pilih findit_db -> tab SQL -> tempel isi file ini -> Go.
-- Data lama TIDAK hilang; semua laporan lama otomatis berstatus "disetujui" (tetap tampil).
-- Jalankan SEKALI saja.
-- =========================================================

ALTER TABLE users
  MODIFY role ENUM('superadmin','admin','user') NOT NULL DEFAULT 'user';

ALTER TABLE barang
  ADD COLUMN verifikasi ENUM('pending','disetujui','ditolak') NOT NULL DEFAULT 'disetujui' AFTER status,
  ADD COLUMN catatan_admin VARCHAR(255) NULL AFTER verifikasi;
