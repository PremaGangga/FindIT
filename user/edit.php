<?php
require '../includes/auth.php';
wajib_role('user');
require '../includes/validasi_barang.php';

$id = (int)($_GET['id'] ?? 0);
// Hanya laporan milik sendiri yang belum tayang
$lama = db_one("SELECT * FROM barang WHERE id = ? AND created_by = ? AND verifikasi <> 'disetujui'", 'ii', [$id, user()['id']]);
if (!$lama) { flash_set('danger', 'Laporan tidak ditemukan atau sudah tayang (hubungi admin untuk mengubahnya).'); redirect('user/dashboard.php'); }

$data = $lama;
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    [$data, $errors] = ambil_input_barang(['hilang', 'ditemukan']);
    $data['foto'] = $lama['foto'];
    if ($data['kontak'] === '') $errors[] = 'Kontak wajib diisi agar pemilik atau penemu bisa menghubungimu.';
    $up = upload_foto($_FILES['foto'] ?? null);
    if (!$up['ok']) $errors[] = $up['error'];

    if (!$errors) {
        $fotoBaru = $up['file'] ?: $lama['foto'];
        // Setelah diperbaiki, laporan masuk antrean verifikasi lagi
        db_exec(
            "UPDATE barang SET judul=?, deskripsi=?, kategori_id=?, lokasi=?, tanggal=?, status=?, foto=?, kontak=?,
                    verifikasi='pending', catatan_admin=NULL WHERE id=? AND created_by=?",
            'ssisssssii',
            [$data['judul'], $data['deskripsi'], $data['kategori_id'], $data['lokasi'], $data['tanggal'],
             $data['status'], $fotoBaru, $data['kontak'], $id, user()['id']]
        );
        if ($up['file']) hapus_foto($lama['foto']);
        flash_set('success', 'Laporan diperbarui dan dikirim ulang untuk diverifikasi.');
        redirect('user/dashboard.php');
    }
    if ($up['ok'] && $up['file']) hapus_foto($up['file']);
}

$daftarKategori = db_all('SELECT * FROM kategori ORDER BY id');
$mode = 'edit';
$judul = 'Ubah laporan';
require '../includes/header.php';
?>
<div class="container page-pad">
  <a class="back-link" href="<?= url('user/dashboard.php') ?>"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
  <h1 class="page-title mt-2 mb-3">Ubah laporan</h1>
  <?php if ($lama['verifikasi'] === 'ditolak'): ?>
    <div class="alert alert-danger mb-4"><strong>Laporan ini ditolak admin.</strong>
      <?= $lama['catatan_admin'] ? 'Alasan: ' . e($lama['catatan_admin']) : '' ?>
      Perbaiki lalu simpan untuk mengirim ulang.</div>
  <?php endif; ?>
  <?php
    $form_back = 'user/dashboard.php'; $status_opsi = ['hilang', 'ditemukan']; $catatan_kontak = true;
    require '../includes/form_barang.php';
  ?>
</div>
<?php require '../includes/footer.php'; ?>
