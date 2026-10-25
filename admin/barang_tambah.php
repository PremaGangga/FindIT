<?php
require '../includes/auth.php';
wajib_role('admin');
require '../includes/validasi_barang.php';

$data = ['judul' => '', 'deskripsi' => '', 'kategori_id' => 0, 'lokasi' => '', 'tanggal' => date('Y-m-d'),
         'status' => 'hilang', 'kontak' => '', 'foto' => null];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    [$data, $errors] = ambil_input_barang();
    $data['foto'] = null;
    $up = upload_foto($_FILES['foto'] ?? null);
    if (!$up['ok']) $errors[] = $up['error'];

    if (!$errors) {
        db_exec(
            "INSERT INTO barang (judul, deskripsi, kategori_id, lokasi, tanggal, status, foto, kontak, created_by, verifikasi)
             VALUES (?,?,?,?,?,?,?,?,?, 'disetujui')",
            'ssisssssi',
            [$data['judul'], $data['deskripsi'], $data['kategori_id'], $data['lokasi'], $data['tanggal'],
             $data['status'], $up['file'], $data['kontak'], user()['id']]
        );
        flash_set('success', 'Laporan "' . $data['judul'] . '" berhasil ditambahkan.');
        redirect('admin/dashboard.php');
    }
    if ($up['ok']) hapus_foto($up['file']);   // ada error lain -> buang foto yang sempat tersimpan
}

$daftarKategori = db_all('SELECT * FROM kategori ORDER BY nama');
if (!$data['kategori_id'] && $daftarKategori) $data['kategori_id'] = $daftarKategori[0]['id'];
$mode = 'tambah';
$judul = 'Tambah laporan';
require '../includes/header.php';
?>
<div class="container page-pad">
  <a class="back-link" href="<?= url('admin/dashboard.php') ?>"><i class="bi bi-arrow-left me-1"></i>Kembali ke dashboard</a>
  <h1 class="page-title mt-2 mb-4">Tambah laporan barang</h1>
  <?php $form_back = 'admin/dashboard.php'; $status_opsi = ['hilang', 'ditemukan', 'dikembalikan']; $catatan_kontak = false;
  require '../includes/form_barang.php'; ?>
</div>
<?php require '../includes/footer.php'; ?>
