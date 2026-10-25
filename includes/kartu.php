<?php
/** Kartu barang yang dipakai di beranda dan halaman daftar barang. */
function kartu_barang($b) { ?>
  <article class="item-card">
    <a class="item-photo" href="<?= url('detail.php?id=' . $b['id']) ?>" tabindex="-1" aria-hidden="true">
      <?php if ($b['foto']): ?>
        <img src="<?= url('assets/uploads/' . e($b['foto'])) ?>" alt="" loading="lazy">
      <?php else: ?>
        <i class="bi <?= kategori_icon($b['kategori']) ?>"></i>
      <?php endif; ?>
    </a>
    <span class="pill-kat kat-<?= kategori_slug($b['kategori']) ?>"><?= e($b['kategori'] ?? 'Lainnya') ?></span>
    <span class="status pill-status status-<?= e($b['status']) ?>"><?= status_label($b['status']) ?></span>
    <div class="item-body">
      <h3><a href="<?= url('detail.php?id=' . $b['id']) ?>"><?= e($b['judul']) ?></a></h3>
      <p class="meta"><i class="bi bi-geo-alt"></i><?= e($b['lokasi'] ?: 'Lokasi belum diisi') ?></p>
      <p class="meta"><i class="bi bi-calendar3"></i><?= tgl($b['tanggal']) ?></p>
      <p class="desc"><?= e(teks_potong($b['deskripsi'], 95)) ?></p>
      <a class="btn btn-detail" href="<?= url('detail.php?id=' . $b['id']) ?>">Lihat Detail <i class="bi bi-arrow-right ms-1"></i></a>
    </div>
  </article>
<?php }
