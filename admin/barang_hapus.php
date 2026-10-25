<?php
require '../includes/auth.php';
wajib_role('admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('admin/dashboard.php');
csrf_check();

$id = (int)($_POST['id'] ?? 0);
$b = db_one('SELECT foto, judul FROM barang WHERE id = ?', 'i', [$id]);
if ($b) {
    db_exec('DELETE FROM barang WHERE id = ?', 'i', [$id]);
    hapus_foto($b['foto']);
    flash_set('success', 'Laporan "' . $b['judul'] . '" sudah dihapus.');
} else {
    flash_set('danger', 'Laporan tidak ditemukan.');
}
redirect('admin/dashboard.php');
