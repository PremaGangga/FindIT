<?php
require '../includes/auth.php';
wajib_role('user');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('user/dashboard.php');
csrf_check();

$id = (int)($_POST['id'] ?? 0);
$b = db_one("SELECT foto, judul FROM barang WHERE id = ? AND created_by = ? AND verifikasi <> 'disetujui'", 'ii', [$id, user()['id']]);
if ($b) {
    db_exec('DELETE FROM barang WHERE id = ? AND created_by = ?', 'ii', [$id, user()['id']]);
    hapus_foto($b['foto']);
    flash_set('success', 'Laporan "' . $b['judul'] . '" dihapus.');
} else {
    flash_set('danger', 'Laporan tidak ditemukan atau sudah tayang.');
}
redirect('user/dashboard.php');
