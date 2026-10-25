<?php /** Form bersama tambah & edit admin. Butuh: $data, $errors, $mode */ ?>
<form method="post" class="form-card" autocomplete="off" novalidate>
  <?= csrf_field() ?>
  <?php if ($errors): ?>
    <div class="alert alert-danger">
      <strong>Data belum bisa disimpan:</strong>
      <ul class="mb-0 mt-1"><?php foreach ($errors as $er): ?><li><?= e($er) ?></li><?php endforeach; ?></ul>
    </div>
  <?php endif; ?>
  <div class="mb-3">
    <label class="form-label" for="nama">Nama lengkap</label>
    <input class="form-control" id="nama" name="nama" maxlength="100" value="<?= e($data['nama']) ?>" required>
  </div>
  <div class="mb-3">
    <label class="form-label" for="username">Username</label>
    <input class="form-control" id="username" name="username" maxlength="30" value="<?= e($data['username']) ?>" required>
    <div class="form-text">3 sampai 30 karakter: huruf, angka, atau garis bawah.</div>
  </div>
  <div class="mb-3">
    <label class="form-label" for="password">Password<?= $mode === 'edit' ? ' baru' : '' ?></label>
    <div class="input-group">
      <input type="password" class="form-control" id="password" name="password" <?= $mode === 'tambah' ? 'required' : '' ?>>
      <button class="btn btn-light border" type="button" data-toggle-password="#password" aria-label="Tampilkan password"><i class="bi bi-eye"></i></button>
    </div>
    <div class="form-text"><?= $mode === 'edit' ? 'Kosongkan jika password tidak ingin diganti. ' : '' ?>Minimal 6 karakter.</div>
  </div>
  <div class="d-flex gap-2 mt-4">
    <button class="btn btn-primary-ink" type="submit"><i class="bi bi-check-lg me-1"></i><?= $mode === 'edit' ? 'Simpan perubahan' : 'Buat akun admin' ?></button>
    <a class="btn btn-light border" href="<?= url('superadmin/dashboard.php') ?>">Batal</a>
  </div>
</form>
