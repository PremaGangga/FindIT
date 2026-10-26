<?php
$judul = 'Cara kerja';
require 'includes/header.php';
?>
<div class="container page-pad">
  <div class="row">
    <div class="col-lg-8">
      <h1 class="page-title">Cara kerja FindIT</h1>
      <p class="lead-text">
        FindIT mengumpulkan laporan barang hilang dan barang temuan di satu tempat.
        Siapa pun yang terdaftar bisa melapor, dan admin memastikan setiap laporan akurat sebelum tayang.
      </p>
    </div>
  </div>

  <div class="row g-4 mt-2">
    <div class="col-md-6">
      <div class="info-block">
        <h2>Kalau barangmu hilang</h2>
        <ol class="steps">
          <li>Cari dulu di <a href="<?= url('barang.php') ?>">daftar barang</a> berdasarkan nama, kategori, atau lokasi.</li>
          <li>Kalau ada yang mirip, buka detailnya dan hubungi penemu lewat kontak yang tertera. Siapkan bukti kepemilikan.</li>
          <li>Belum ada? <a href="<?= url('daftar.php') ?>">Daftar</a>, lalu buat laporan dengan status <span class="status status-inline status-hilang">Hilang</span> agar orang lain bisa membantu.</li>
        </ol>
      </div>
    </div>
    <div class="col-md-6">
      <div class="info-block">
        <h2>Kalau kamu menemukan barang</h2>
        <ol class="steps">
          <li>Masuk, lalu buat laporan dengan status <span class="status status-inline status-ditemukan">Ditemukan</span>. Sertakan foto dan lokasi.</li>
          <li>Admin memeriksa laporanmu. Pantau statusnya di dashboard (menunggu, tayang, atau ditolak beserta alasannya).</li>
          <li>Setelah tayang, pemilik menghubungimu. Kalau barang sudah kembali, klik <strong>Sudah dikembalikan</strong> di dashboard-mu. Laporan otomatis hilang dari daftar pencarian.</li>
        </ol>
      </div>
    </div>
  </div>

  <div class="info-block mt-4">
    <h2>Arti status laporan</h2>
    <div class="row g-3">
      <div class="col-md-4"><span class="status status-inline status-hilang">Hilang</span><p class="mt-2 mb-0">Pemilik sedang mencari barang ini.</p></div>
      <div class="col-md-4"><span class="status status-inline status-ditemukan">Ditemukan</span><p class="mt-2 mb-0">Barang sudah ada di tangan admin, menunggu pemiliknya.</p></div>
      <div class="col-md-4"><span class="status status-inline status-dikembalikan">Dikembalikan</span><p class="mt-2 mb-0">Barang sudah kembali. Laporan tidak lagi tampil di pencarian.</p></div>
    </div>
  </div>

  <div class="text-center mt-5">
    <a class="btn btn-pill btn-pill-fill btn-pill-lg" href="<?= url('barang.php') ?>">Lihat daftar barang</a>
  </div>
</div>
<?php require 'includes/footer.php'; ?>
