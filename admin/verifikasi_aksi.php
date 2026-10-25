<?php
require '../includes/auth.php';
wajib_role('admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('admin/verifikasi.php');
csrf_check();

$id   = (int)($_POST['id'] ?? 0);
$aksi = $_POST['aksi'] ?? '';
$b = db_one('SELECT id, judul FROM barang WHERE id = ?', 'i', [$id]);
if (!$b) { flash_set('danger', 'Laporan tidak ditemukan.'); redirect('admin/verifikasi.php'); }

if ($aksi === 'setujui') {
    db_exec("UPDATE barang SET verifikasi='disetujui', catatan_admin=NULL WHERE id=?", 'i', [$id]);
    flash_set('success', 'Laporan "' . $b['judul'] . '" disetujui dan sekarang tampil di daftar barang.');
    redirect('admin/verifikasi.php');
}
if ($aksi === 'tolak') {
    $catatan = trim($_POST['catatan'] ?? '');
    if ($catatan === '') { flash_set('danger', 'Alasan penolakan wajib diisi.'); redirect('admin/verifikasi.php'); }
    db_exec("UPDATE barang SET verifikasi='ditolak', catatan_admin=? WHERE id=?", 'si', [substr($catatan, 0, 255), $id]);
    flash_set('success', 'Laporan "' . $b['judul'] . '" ditolak. Pelapor bisa melihat alasannya.');
    redirect('admin/verifikasi.php');
}
redirect('admin/verifikasi.php');
