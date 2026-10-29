<?php
require '../includes/auth.php';
wajib_role('admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('admin/dashboard.php');
csrf_check();

$id = (int)($_POST['id'] ?? 0);
$b = db_one('SELECT foto, judul FROM barang WHERE id = ?', 'i', [$id]);

if ($b) {
    // 1. Catat waktu penghapusan dan ubah flag dihapus_admin menjadi 1 (Soft Delete)
    $waktu_sekarang = date('Y-m-d H:i:s');
    db_exec('UPDATE barang SET dihapus_admin = 1, dihapus_at = ? WHERE id = ?', 'si', [$waktu_sekarang, $id]);

    // 2. hapus_foto($b['foto']); 
    // Catatan: Baris hapus_foto di atas DIKOMENTARI agar file foto tetap ada
    // sehingga riwayat tampilan di dashboard user tetap dapat menampilkan gambarnya.

    flash_set('success', 'Laporan "' . $b['judul'] . '" berhasil diarsipkan/dihapus dari tampilan admin.');
} else {
    flash_set('danger', 'Laporan tidak ditemukan.');
}

redirect('admin/dashboard.php');