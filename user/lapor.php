<?php
require '../includes/auth.php';
wajib_role('user');
require '../includes/validasi_barang.php';

const BATAS_PENDING = 5;   // cegah spam: maksimal laporan yang menunggu verifikasi

$uid = user()['id'];
$pending = (int)db_one("SELECT COUNT(*) AS n FROM barang WHERE created_by = ? AND verifikasi = 'pending'", 'i', [$uid])['n'];
if ($pending >= BATAS_PENDING) {
    flash_set('warning', 'Kamu sudah punya ' . BATAS_PENDING . ' laporan yang menunggu verifikasi. Tunggu admin memeriksanya dulu.');
    redirect('user/dashboard.php');
}

$data = ['judul' => '', 'deskripsi' => '', 'kategori_id' => 0, 'lokasi' => '', 'tanggal' => date('Y-m-d'),
         'status' => 'hilang', 'kontak' => '', 'foto' => null];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    [$data, $errors] = ambil_input_barang(['hilang', 'ditemukan']);
    $data['foto'] = null;
    if ($data['kontak'] === '') $errors[] = 'Kontak wajib diisi agar pemilik atau penemu bisa menghubungimu.';
    $up = upload_foto($_FILES['foto'] ?? null);
    if (!$up['ok']) $errors[] = $up['error'];

    if (!$errors) {
        db_exec(
            "INSERT INTO barang (judul, deskripsi, kategori_id, lokasi, tanggal, status, foto, kontak, created_by, verifikasi)
             VALUES (?,?,?,?,?,?,?,?,?, 'pending')",
            'ssisssssi',
            [$data['judul'], $data['deskripsi'], $data['kategori_id'], $data['lokasi'], $data['tanggal'],
             $data['status'], $up['file'], $data['kontak'], $uid]
        );
        flash_set('success', 'Laporan terkirim. Admin akan memeriksanya sebelum ditampilkan.');
        redirect('user/dashboard.php');
    }
    if ($up['ok'] && $up['file']) hapus_foto($up['file']);
}

$daftarKategori = db_all('SELECT * FROM kategori ORDER BY id');
if (!$data['kategori_id'] && $daftarKategori) $data['kategori_id'] = $daftarKategori[0]['id'];
$mode = 'tambah';
$judul = 'Buat laporan';
require '../includes/header.php';
?>
<div class="container page-pad">
  <a class="back-link" href="<?= url('user/dashboard.php') ?>"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
  <h1 class="page-title mt-2 mb-2">Buat laporan barang</h1>
  <p class="text-muted mb-4">Pilih <strong>Hilang</strong> kalau kamu kehilangan barang, atau <strong>Ditemukan</strong> kalau kamu menemukan barang milik orang lain.</p>
  <?php
    $form_back = 'user/dashboard.php'; $status_opsi = ['hilang', 'ditemukan']; $catatan_kontak = true;
    require '../includes/form_barang.php';
  ?>
</div>
<?php require '../includes/footer.php'; ?>
