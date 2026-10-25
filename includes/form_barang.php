<?php
/**
 * Form bersama tambah/edit barang (admin & user).
 * Butuh: $data, $errors, $daftarKategori, $mode, $form_back (path tombol Batal),
 *        $status_opsi (status yang boleh dipilih), $catatan_kontak (tampilkan peringatan privasi)
 */
?>
<form method="post" enctype="multipart/form-data" class="form-card wide" novalidate>
  <?= csrf_field() ?>

  <?php if ($errors): ?>
    <div class="alert alert-danger">
      <strong>Data belum bisa disimpan:</strong>
      <ul class="mb-0 mt-1"><?php foreach ($errors as $er): ?><li><?= e($er) ?></li><?php endforeach; ?></ul>
    </div>
  <?php endif; ?>

  <div class="row g-3">
    <div class="col-12">
      <label class="form-label" for="judul">Nama barang</label>
      <input class="form-control" id="judul" name="judul" maxlength="150" value="<?= e($data['judul']) ?>" placeholder="Contoh: Dompet kulit hitam" required>
    </div>
    <div class="col-md-6">
      <label class="form-label" for="kategori_id">Kategori</label>
      <select class="form-select" id="kategori_id" name="kategori_id" required>
        <?php foreach ($daftarKategori as $k): ?>
          <option value="<?= $k['id'] ?>" <?= (int)$data['kategori_id'] === (int)$k['id'] ? 'selected' : '' ?>><?= e($k['nama']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-6">
      <label class="form-label" for="status">Status</label>
      <select class="form-select" id="status" name="status">
        <?php foreach ($status_opsi as $st): ?>
          <option value="<?= $st ?>" <?= $data['status'] === $st ? 'selected' : '' ?>><?= status_label($st) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-6">
      <label class="form-label" for="lokasi">Lokasi</label>
      <input class="form-control" id="lokasi" name="lokasi" maxlength="100" value="<?= e($data['lokasi']) ?>" placeholder="Contoh: Perpustakaan lantai 2" required>
    </div>
    <div class="col-md-6">
      <label class="form-label" for="tanggal">Tanggal kejadian</label>
      <input type="date" class="form-control" id="tanggal" name="tanggal" value="<?= e($data['tanggal']) ?>" max="<?= date('Y-m-d') ?>" required>
    </div>
    <div class="col-12">
      <label class="form-label" for="deskripsi">Ciri-ciri dan keterangan</label>
      <textarea class="form-control" id="deskripsi" name="deskripsi" rows="4" placeholder="Warna, merk, isi barang, tanda khusus. Jangan tulis data pribadi yang sensitif."><?= e($data['deskripsi']) ?></textarea>
    </div>
    <div class="col-12">
      <label class="form-label" for="kontak">Kontak yang bisa dihubungi</label>
      <input class="form-control" id="kontak" name="kontak" maxlength="100" value="<?= e($data['kontak']) ?>" placeholder="Contoh: 0812-xxxx-xxxx (WhatsApp) atau Pos Satpam Gedung A">
      <?php if (!empty($catatan_kontak)): ?>
        <div class="form-text"><i class="bi bi-info-circle me-1"></i>Kontak ini akan tampil di halaman detail setelah laporan disetujui admin. Isi hanya kontak yang kamu izinkan dilihat publik.</div>
      <?php endif; ?>
    </div>
    <div class="col-12">
      <label class="form-label" for="foto">Foto barang <span class="text-muted fw-normal">(JPG, PNG, atau WEBP, maks. 2 MB)</span></label>
      <?php if ($mode === 'edit' && $data['foto']): ?>
        <div class="mb-2 d-flex align-items-center gap-3">
          <img src="<?= url('assets/uploads/' . e($data['foto'])) ?>" alt="Foto saat ini" class="thumb-lg">
          <span class="text-muted small">Foto saat ini. Pilih file baru untuk menggantinya.</span>
        </div>
      <?php endif; ?>
      <input type="file" class="form-control" id="foto" name="foto" accept="image/jpeg,image/png,image/webp">
    </div>
  </div>

  <div class="d-flex gap-2 mt-4">
    <button class="btn btn-primary-ink" type="submit"><i class="bi bi-check-lg me-1"></i><?= $mode === 'edit' ? 'Simpan perubahan' : 'Simpan laporan' ?></button>
    <a class="btn btn-light border" href="<?= url($form_back) ?>">Batal</a>
  </div>
</form>
