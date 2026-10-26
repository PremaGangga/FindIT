<?php
require '../includes/auth.php';
wajib_role('user');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('user/dashboard.php');
csrf_check();

$id = (int)($_POST['id'] ?? 0);
// Hanya laporan milik sendiri yang sudah tayang dan belum ditandai selesai
$b = db_one("SELECT judul FROM barang WHERE id = ? AND created_by = ? AND verifikasi = 'disetujui' AND status <> 'dikembalikan'",
            'ii', [$id, user()['id']]);
if ($b) {
    db_exec("UPDATE barang SET status = 'dikembalikan' WHERE id = ? AND created_by = ?", 'ii', [$id, user()['id']]);
    flash_set('success', 'Terima kasih! Laporan "' . $b['judul'] . '" ditandai sudah dikembalikan dan tidak tampil lagi di pencarian.');
} else {
    flash_set('danger', 'Laporan tidak ditemukan, belum tayang, atau sudah ditandai selesai.');
}
redirect('user/dashboard.php');