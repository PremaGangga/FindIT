<?php
/**
 * Proses hapus pengguna. Dipanggil dari admin/user_hapus.php dan superadmin/user_hapus.php
 * SETELAH wajib_role(). Butuh: $kembali (path halaman daftar pengguna).
 * Hanya akun ber-role "user" yang bisa dihapus (admin & superadmin aman).
 */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect($kembali);
csrf_check();

$id = (int)($_POST['id'] ?? 0);
$target = db_one("SELECT id, nama FROM users WHERE id = ? AND role = 'user'", 'i', [$id]);
if (!$target) { flash_set('danger', 'Pengguna tidak ditemukan.'); redirect($kembali); }

$pesanLaporan = '';
if (!empty($_POST['hapus_laporan'])) {
    $fotos = db_all('SELECT foto FROM barang WHERE created_by = ?', 'i', [$id]);
    db_exec('DELETE FROM barang WHERE created_by = ?', 'i', [$id]);
    foreach ($fotos as $f) hapus_foto($f['foto']);
    $pesanLaporan = count($fotos) > 0 ? ' beserta ' . count($fotos) . ' laporannya' : '';
}
db_exec("DELETE FROM users WHERE id = ? AND role = 'user'", 'i', [$id]);

flash_set('success', 'Pengguna "' . $target['nama'] . '"' . $pesanLaporan . ' sudah dihapus.');
redirect($kembali);