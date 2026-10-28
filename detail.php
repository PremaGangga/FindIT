<?php
require_once 'includes/auth.php';
require_once 'includes/kartu.php';
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
          <h3>Laporan tidak ditemukan</h3><p>Laporan ini mungkin sudah dihapus atau barangnya sudah dikembalikan.</p>
          <a class="btn btn-pill btn-pill-fill" href="' . url('barang.php') . '">Kembali ke daftar</a></div></div>';
    require 'includes/footer.php';
    exit;
}
$judul = $b['judul'];
require 'includes/header.php';

$label = $b['status'] === 'hilang' ? 'Hilang pada' : 'Ditemukan pada';

// Nomor HP di kolom kontak -> tombol WhatsApp (0812... atau +62812... diubah ke 62812...)
$wa = null;
if (preg_match('/(?:\+?62|0)[\d\s\-]{8,14}\d/', (string)$b['kontak'], $m)) {
    $digits = preg_replace('/\D+/', '', $m[0]);
    $wa = ($digits[0] === '0') ? '62' . substr($digits, 1) : $digits;
}

// Laporan serupa: kategori sama, sudah tayang, masih aktif
$serupa = db_all(
    "SELECT b.*, k.nama AS kategori FROM barang b LEFT JOIN kategori k ON k.id = b.kategori_id
     WHERE b.kategori_id = ? AND b.id <> ? AND b.verifikasi = 'disetujui' AND b.status IN ('hilang','ditemukan')
     ORDER BY b.created_at DESC LIMIT 4", 'ii', [(int)$b['kategori_id'], $b['id']]
);
?>
<section class="page-head" style="padding-bottom:3.6rem">
  <div class="container">
    <a class="back-link" href="<?= url('barang.php') ?>"><i class="bi bi-arrow-left me-1"></i>Kembali ke daftar barang</a>
  </div>
</section>

<div class="container" style="margin-top:-2.2rem; position:relative; z-index:2">
  <?php if ($b['verifikasi'] !== 'disetujui'): ?>
    <div class="alert alert-<?= $b['verifikasi'] === 'ditolak' ? 'danger' : 'warning' ?> mb-3">
      <strong>Laporan ini belum tampil untuk publik.</strong>
      <?= $b['verifikasi'] === 'ditolak' ? 'Ditolak admin' . ($b['catatan_admin'] ? ': ' . e($b['catatan_admin']) : '.') : 'Menunggu verifikasi admin.' ?>
    </div>
  <?php endif; ?>

  <div class="row g-4 g-lg-5">
    <div class="col-md-5">
      <div class="detail-photo ph-<?= kategori_slug($b['kategori']) ?>">
        <span class="pill-kat kat-<?= kategori_slug($b['kategori']) ?>"><?= e($b['kategori'] ?? 'Lainnya') ?></span>
        <?php if ($b['foto']): ?>
          <img src="<?= url('assets/uploads/' . e($b['foto'])) ?>" alt="Foto <?= e($b['judul']) ?>">
        <?php else: ?>
          <i class="bi <?= kategori_icon($b['kategori']) ?>"></i>
          <small>Belum ada foto</small>
        <?php endif; ?>
      </div>
    </div>

    <div class="col-md-7">
      <div class="detail-card">
        <span class="status status-inline status-<?= e($b['status']) ?>"><?= status_label($b['status']) ?></span>
        <h1 class="detail-title"><?= e($b['judul']) ?></h1>

        <div class="detail-meta">
          <div class="dm-tile"><i class="bi bi-tag"></i><small>Kategori</small><strong><?= e($b['kategori'] ?? 'Lainnya') ?></strong></div>
          <div class="dm-tile"><i class="bi bi-geo-alt"></i><small>Lokasi</small><strong><?= e($b['lokasi'] ?: '-') ?></strong></div>
          <div class="dm-tile"><i class="bi bi-calendar3"></i><small><?= $label ?></small><strong><?= tgl($b['tanggal']) ?></strong></div>
        </div>

        <?php if ($b['deskripsi']): ?>
          <h2 class="detail-sub">Ciri-ciri dan keterangan</h2>
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
            <div class="flex-grow-1">
              <strong><?= $b['status'] === 'hilang' ? 'Pernah melihat barang ini?' : 'Ini barang milikmu?' ?></strong><br>
              Hubungi: <span class="contact-value"><?= e($b['kontak'] ?: 'Kontak belum diisi') ?></span>
              <div class="small text-muted mt-1">Siapkan bukti kepemilikan, misalnya ciri khusus atau foto lama.</div>
              <div class="contact-actions">
                <?php if ($wa): ?>
                  <a class="btn btn-pill btn-wa" href="https://wa.me/<?= e($wa) ?>" target="_blank" rel="noopener"><i class="bi bi-whatsapp me-2"></i>Chat WhatsApp</a>
                <?php endif; ?>
                <?php if ($b['kontak']): ?>
                  <button type="button" class="btn btn-pill btn-pill-outline" data-copy="<?= e($b['kontak']) ?>"><i class="bi bi-clipboard me-1"></i>Salin kontak</button>
                <?php endif; ?>
                <button type="button" class="btn btn-pill btn-pill-outline" data-copy="url"><i class="bi bi-link-45deg me-1"></i>Salin tautan</button>
              </div>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <?php if ($serupa): ?>
    <section class="home-section">
      <div class="section-head mb-0"><h2>Laporan serupa</h2><p>Barang lain di kategori <?= e($b['kategori'] ?? 'Lainnya') ?>.</p></div>
      <div class="row g-4">
        <?php foreach ($serupa as $s): ?>
          <div class="col-sm-6 col-lg-3"><?php kartu_barang($s); ?></div>
        <?php endforeach; ?>
      </div>
    </section>
  <?php endif; ?>
</div>
<?php require 'includes/footer.php'; ?>