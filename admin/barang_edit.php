<?php
require '../includes/auth.php';
wajib_role('admin');
require '../includes/validasi_barang.php';

$id  = (int)($_GET['id'] ?? 0);
$lama = db_one('SELECT * FROM barang WHERE id = ?', 'i', [$id]);
if (!$lama) { flash_set('danger', 'Laporan tidak ditemukan.'); redirect('admin/dashboard.php'); }

$data = $lama;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    [$data, $errors] = ambil_input_barang();
    $data['foto'] = $lama['foto'];
    $up = upload_foto($_FILES['foto'] ?? null);
    if (!$up['ok']) $errors[] = $up['error'];

    if (!$errors) {
        $fotoBaru = $up['file'] ?: $lama['foto'];
        db_exec(
            'UPDATE barang SET judul=?, deskripsi=?, kategori_id=?, lokasi=?, tanggal=?, status=?, foto=?, kontak=? WHERE id=?',
            'ssisssssi',
            [$data['judul'], $data['deskripsi'], $data['kategori_id'], $data['lokasi'], $data['tanggal'],
             $data['status'], $fotoBaru, $data['kontak'], $id]
        );
        if ($up['file']) hapus_foto($lama['foto']);   // foto lama diganti
        flash_set('success', 'Perubahan berhasil disimpan.');
        redirect($lama['verifikasi'] === 'disetujui' ? 'admin/dashboard.php' : 'admin/verifikasi.php');
    }
    if ($up['ok'] && $up['file']) hapus_foto($up['file']);
}

$daftarKategori = db_all('SELECT * FROM kategori ORDER BY nama');
$mode = 'edit';
$judul = 'Ubah laporan';
require '../includes/header.php';
?>
<div class="container page-pad">
  <a class="back-link" href="<?= url('admin/dashboard.php') ?>"><i class="bi bi-arrow-left me-1"></i>Kembali ke dashboard</a>
  <h1 class="page-title mt-2 mb-4">Ubah laporan barang</h1>
  <?php $form_back = 'admin/dashboard.php'; $status_opsi = ['hilang', 'ditemukan', 'dikembalikan']; $catatan_kontak = false;
  require '../includes/form_barang.php'; ?>
</div>
<?php require '../includes/footer.php'; ?>
