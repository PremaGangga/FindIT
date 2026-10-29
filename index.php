<?php
$judul = null;
require 'includes/header.php';
require 'includes/kartu.php';

// Hanya laporan disetujui DAN belum dihapus admin yang tampil ke publik
$terbaru = db_all(
    "SELECT b.*, k.nama AS kategori FROM barang b LEFT JOIN kategori k ON k.id = b.kategori_id
     WHERE b.verifikasi = 'disetujui' AND b.status IN ('hilang','ditemukan') AND b.dihapus_admin = 0 
     ORDER BY b.created_at DESC, b.id DESC LIMIT 4"
);

// Statistik hanya menghitung barang yang belum dihapus oleh admin
$s = ['hilang' => 0, 'ditemukan' => 0, 'dikembalikan' => 0];
foreach (db_all("SELECT status, COUNT(*) AS n FROM barang WHERE verifikasi = 'disetujui' AND dihapus_admin = 0 GROUP BY status") as $r) {
    $s[$r['status']] = (int)$r['n'];
}
?>

<section class="hero">
  <div class="container">
    <div class="row align-items-center g-4">
      <div class="col-lg-5">
        <h1 class="hero-title">Barang hilang?<br>Cari di <span>FindIT.</span></h1>
        <p class="hero-lead">Temukan informasi barang hilang dan barang temuan di lingkungan kampus melalui satu platform yang mudah digunakan.</p>
        <div class="d-flex flex-wrap gap-3">
          <a class="btn btn-pill btn-pill-fill btn-pill-lg" href="<?= url('barang.php') ?>"><i class="bi bi-search me-2"></i>Cari Barang <i class="bi bi-arrow-right ms-2"></i></a>
          <a class="btn btn-pill btn-pill-outline btn-pill-lg" href="<?= url('user/lapor.php') ?>"><i class="bi bi-plus-circle me-2"></i>Lapor Barang</a>
        </div>
        <ul class="hero-trust">
          <li><i class="bi bi-check-circle-fill"></i>Gratis</li>
          <li><i class="bi bi-check-circle-fill"></i>Diverifikasi admin</li>
          <li><i class="bi bi-check-circle-fill"></i>Tanpa tatap muka</li>
        </ul>
      </div>

      <div class="col-lg-4 col-md-6">
        <div class="hero-art" aria-hidden="true">
          <div class="lens"><i class="bi bi-backpack2-fill"></i></div>
          <span class="float-ico f1"><i class="bi bi-wallet2"></i></span>
          <span class="float-ico f2"><i class="bi bi-phone"></i></span>
          <span class="float-ico f3"><i class="bi bi-headphones"></i></span>
        </div>
      </div>

      <div class="col-lg-3 col-md-6">
        <p class="handwrite">Temukan.<br>Hubungkan.<br>Kembalikan.</p>
        <p class="hero-side">Informasi yang tepat membantu barang kembali kepada pemiliknya.</p>
      </div>
    </div>
  </div>
</section>

<div class="container">
  <div class="stat-band reveal">
    <div class="sb-item"><span class="sb-ico sb-hilang"><i class="bi bi-search"></i></span><div><span class="num" data-count="<?= $s['hilang'] ?>"><?= $s['hilang'] ?></span>barang sedang dicari</div></div>
    <div class="sb-item"><span class="sb-ico sb-ditemukan"><i class="bi bi-box-seam"></i></span><div><span class="num" data-count="<?= $s['ditemukan'] ?>"><?= $s['ditemukan'] ?></span>barang temuan menunggu pemilik</div></div>
    <div class="sb-item"><span class="sb-ico sb-kembali"><i class="bi bi-check2-circle"></i></span><div><span class="num" data-count="<?= $s['dikembalikan'] ?>"><?= $s['dikembalikan'] ?></span>sudah kembali ke pemilik</div></div>
  </div>
</div>

<section class="band-soft">
  <div class="container">
    <div class="section-head center reveal">
      <h2>Cara kerja FindIT</h2>
      <p>Tanpa perlu datang ke kantor admin. Cukup lapor lewat website.</p>
    </div>
    <div class="row g-4 how-row">
      <div class="col-md-4 reveal" style="--d:.05s">
        <div class="how-step">
          <span class="how-num">1</span>
          <span class="how-ico"><i class="bi bi-pencil-square"></i></span>
          <h3>Buat laporan</h3>
          <p>Daftar atau masuk, lalu isi nama barang, lokasi, tanggal, ciri-ciri, dan foto. Bisa untuk barang hilang maupun barang yang kamu temukan.</p>
        </div>
      </div>
      <div class="col-md-4 reveal" style="--d:.15s">
        <div class="how-step">
          <span class="how-num">2</span>
          <span class="how-ico"><i class="bi bi-patch-check"></i></span>
          <h3>Admin memverifikasi</h3>
          <p>Admin memeriksa laporanmu supaya informasi akurat dan bebas spam. Status laporan bisa kamu pantau di dashboard.</p>
        </div>
      </div>
      <div class="col-md-4 reveal" style="--d:.25s">
        <div class="how-step">
          <span class="how-num">3</span>
          <span class="how-ico"><i class="bi bi-chat-dots"></i></span>
          <h3>Hubungi dan kembalikan</h3>
          <p>Setelah tayang, pemilik dan penemu saling menghubungi lewat kontak di halaman detail. Barang kembali, laporan ditutup.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<div class="container">
  <section class="home-section">
    <div class="d-flex justify-content-between align-items-end flex-wrap gap-2 mb-3 reveal">
      <div class="section-head mb-0"><h2>Laporan terbaru</h2><p class="mb-0">Barang yang baru dilaporkan di kampus.</p></div>
      <a class="btn btn-pill btn-pill-outline" href="<?= url('barang.php') ?>">Lihat semua barang <i class="bi bi-arrow-right ms-1"></i></a>
    </div>
    <?php if (!$terbaru): ?>
      <div class="empty-state"><i class="bi bi-inbox"></i><h3>Belum ada laporan</h3><p>Jadilah yang pertama melaporkan barang.</p></div>
    <?php else: ?>
      <div class="row g-4 mt-1">
        <?php foreach ($terbaru as $i => $b): ?>
          <div class="col-sm-6 col-lg-3 reveal" style="--d:<?= $i * .08 ?>s"><?php kartu_barang($b); ?></div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </section>

  <div class="cta-dark reveal">
    <span class="cta-ico"><i class="bi bi-shield-check"></i></span>
    <div class="flex-grow-1">
      <strong>Punya barang hilang atau menemukan barang?</strong>
      <span class="sub">Bantu kami menghubungkan barang dengan pemiliknya. Laporanmu diperiksa admin sebelum tayang.</span>
    </div>
    <a class="btn btn-pill btn-pill-white btn-pill-lg" href="<?= url('user/lapor.php') ?>"><i class="bi bi-plus-circle me-2"></i>Buat Laporan</a>
  </div>
</div>

<?php require 'includes/footer.php'; ?>