<?php
require_once 'includes/auth.php';
$id = (int)($_GET['id'] ?? 0);
$b = db_one(
    'SELECT b.*, k.nama AS kategori FROM barang b
     LEFT JOIN kategori k ON k.id = b.kategori_id WHERE b.id = ?', 'i', [$id]
);
$u = user();
$bolehLihat = $b && (($b['verifikasi'] === 'disetujui' && $b['status'] !== 'dikembalikan')
    || ($u && (in_array($u['role'], ['admin', 'superadmin'], true) || (int)$b['created_by'] === (int)$u['id'])));
if (!$bolehLihat) {
    http_response_code(404);
    $judul = 'Barang tidak ditemukan';
    require 'includes/header.php';
    echo '<div class="container py-5"><div class="empty-state"><i class="bi bi-question-circle"></i>
          <h3>Laporan tidak ditemukan</h3><p>Laporan ini mungkin sudah dihapus oleh admin.</p>
          <a class="btn btn-primary-ink" href="' . url('barang.php') . '">Kembali ke daftar</a></div></div>';
    require 'includes/footer.php';
    exit;
}
$judul = $b['judul'];
require 'includes/header.php';

$label = $b['status'] === 'hilang' ? 'Hilang pada' : 'Ditemukan pada';
?>
<div class="container page-pad">
  <?php if ($b['verifikasi'] !== 'disetujui'): ?>
    <div class="alert alert-<?= $b['verifikasi'] === 'ditolak' ? 'danger' : 'warning' ?> mb-3">
      <strong>Laporan ini belum tampil untuk publik.</strong>
      <?= $b['verifikasi'] === 'ditolak' ? 'Ditolak admin' . ($b['catatan_admin'] ? ': ' . e($b['catatan_admin']) : '.') : 'Menunggu verifikasi admin.' ?>
    </div>
  <?php endif; ?>
  <a class="back-link" href="<?= url('barang.php') ?>"><i class="bi bi-arrow-left me-1"></i>Kembali ke daftar</a>

  <div class="row g-4 g-lg-5 mt-1">
    <div class="col-md-5">
      <div class="detail-photo">
        <?php if ($b['foto']): ?>
          <img src="<?= url('assets/uploads/' . e($b['foto'])) ?>" alt="Foto <?= e($b['judul']) ?>">
        <?php else: ?>
          <i class="bi <?= kategori_icon($b['kategori']) ?>"></i>
          <small>Belum ada foto</small>
        <?php endif; ?>
      </div>
    </div>

    <div class="col-md-7">
      <span class="status status-inline status-<?= e($b['status']) ?>"><?= status_label($b['status']) ?></span>
      <h1 class="detail-title"><?= e($b['judul']) ?></h1>

      <dl class="detail-list">
        <div><dt><i class="bi bi-tag"></i>Kategori</dt><dd><?= e($b['kategori'] ?? 'Lainnya') ?></dd></div>
        <div><dt><i class="bi bi-geo-alt"></i>Lokasi</dt><dd><?= e($b['lokasi'] ?: '-') ?></dd></div>
        <div><dt><i class="bi bi-calendar3"></i><?= $label ?></dt><dd><?= tgl($b['tanggal']) ?></dd></div>
      </dl>

      <?php if ($b['deskripsi']): ?>
        <h2 class="h6 mt-4">Ciri-ciri dan keterangan</h2>
        <p class="detail-desc"><?= nl2br(e($b['deskripsi'])) ?></p>
      <?php endif; ?>

      <?php if ($b['status'] === 'dikembalikan'): ?>
        <div class="contact-box done">
          <i class="bi bi-check-circle-fill"></i>
          <div><strong>Barang ini sudah dikembalikan.</strong><br>Tidak perlu menghubungi pelapor lagi.</div>
        </div>
      <?php else: ?>
        <div class="contact-box">
          <i class="bi bi-chat-dots-fill"></i>
          <div>
            <strong><?= $b['status'] === 'hilang' ? 'Pernah melihat barang ini?' : 'Ini barang milikmu?' ?></strong><br>
            Hubungi: <span class="contact-value"><?= e($b['kontak'] ?: 'Kontak belum diisi') ?></span>
            <div class="small text-muted mt-1">Siapkan bukti kepemilikan, misalnya ciri khusus atau foto lama.</div>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>
<?php require 'includes/footer.php'; ?>
