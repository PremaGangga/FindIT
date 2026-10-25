<?php
require_once 'includes/auth.php';
if (user()) redirect(dashboard_path(user()['role']));

$data = ['nama' => '', 'username' => ''];
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $data['nama']     = trim($_POST['nama'] ?? '');
    $data['username'] = trim($_POST['username'] ?? '');
    $pass  = $_POST['password'] ?? '';
    $pass2 = $_POST['password2'] ?? '';

    if ($data['nama'] === '' || teks_panjang($data['nama']) > 100) $errors[] = 'Nama lengkap wajib diisi (maksimal 100 karakter).';
    if (!preg_match('/^[A-Za-z0-9_]{3,30}$/', $data['username'])) {
        $errors[] = 'Username harus 3 sampai 30 karakter: huruf, angka, atau garis bawah.';
    } elseif (db_one('SELECT id FROM users WHERE username = ?', 's', [$data['username']])) {
        $errors[] = 'Username sudah dipakai. Pilih yang lain.';
    }
    if (strlen($pass) < 6) $errors[] = 'Password minimal 6 karakter.';
    if ($pass !== $pass2)  $errors[] = 'Konfirmasi password tidak sama.';

    if (!$errors) {
        db_exec("INSERT INTO users (nama, username, password, role) VALUES (?,?,?, 'user')", 'sss',
            [$data['nama'], $data['username'], password_hash($pass, PASSWORD_DEFAULT)]);
        $row = db_one('SELECT * FROM users WHERE username = ?', 's', [$data['username']]);
        session_regenerate_id(true);
        $_SESSION['user'] = ['id' => (int)$row['id'], 'nama' => $row['nama'], 'username' => $row['username'], 'role' => 'user'];
        flash_set('success', 'Akun berhasil dibuat. Selamat datang, ' . $row['nama'] . '!');
        redirect('user/dashboard.php');
    }
}
$judul = 'Daftar';
require 'includes/header.php';
?>
<div class="container page-pad">
  <div class="row justify-content-center">
    <div class="col-md-8 col-lg-5 col-xl-4">
      <div class="form-card">
        <h1 class="h3 mb-1">Buat akun</h1>
        <p class="text-muted mb-4">Daftar gratis untuk melaporkan barang hilang atau barang temuan.</p>

        <?php if ($errors): ?>
          <div class="alert alert-danger"><ul class="mb-0 ps-3"><?php foreach ($errors as $er): ?><li><?= e($er) ?></li><?php endforeach; ?></ul></div>
        <?php endif; ?>

        <form method="post" autocomplete="off" novalidate>
          <?= csrf_field() ?>
          <div class="mb-3">
            <label class="form-label" for="nama">Nama lengkap</label>
            <input class="form-control" id="nama" name="nama" maxlength="100" value="<?= e($data['nama']) ?>" required autofocus>
          </div>
          <div class="mb-3">
            <label class="form-label" for="username">Username</label>
            <input class="form-control" id="username" name="username" maxlength="30" value="<?= e($data['username']) ?>" required>
            <div class="form-text">Huruf, angka, atau garis bawah (3 sampai 30 karakter).</div>
          </div>
          <div class="mb-3">
            <label class="form-label" for="password">Password</label>
            <div class="input-group">
              <input type="password" class="form-control" id="password" name="password" required>
              <button class="btn btn-light border" type="button" data-toggle-password="#password" aria-label="Tampilkan password"><i class="bi bi-eye"></i></button>
            </div>
            <div class="form-text">Minimal 6 karakter.</div>
          </div>
          <div class="mb-4">
            <label class="form-label" for="password2">Ulangi password</label>
            <input type="password" class="form-control" id="password2" name="password2" required>
          </div>
          <button class="btn btn-primary-ink w-100" type="submit">Daftar</button>
        </form>
        <p class="text-center text-muted small mt-4 mb-0">Sudah punya akun? <a href="<?= url('login.php') ?>" class="fw-semibold">Masuk</a></p>
      </div>
    </div>
  </div>
</div>
<?php require 'includes/footer.php'; ?>
