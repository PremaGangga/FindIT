<?php
require_once 'includes/auth.php';

if (user()) redirect(dashboard_path(user()['role']));

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $row = db_one('SELECT * FROM users WHERE username = ?', 's', [$username]);
    if ($row && password_verify($password, $row['password'])) {
        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id' => (int)$row['id'], 'nama' => $row['nama'],
            'username' => $row['username'], 'role' => $row['role'],
        ];
        flash_set('success', 'Selamat datang, ' . $row['nama'] . '.');
        redirect(dashboard_path($row['role']));
    }
    $error = 'Username atau password salah. Periksa lagi huruf besar dan kecilnya.';
}

$judul = 'Masuk';
require 'includes/header.php';
?>
<div class="container page-pad">
  <div class="row justify-content-center">
    <div class="col-md-7 col-lg-5 col-xl-4">
      <div class="form-card">
        <h1 class="h3 mb-1">Masuk ke FindIT</h1>
        <p class="text-muted mb-4">Untuk membuat laporan atau mengelola data. Melihat daftar barang tidak perlu login.</p>

        <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>

        <form method="post" autocomplete="off">
          <?= csrf_field() ?>
          <div class="mb-3">
            <label class="form-label" for="username">Username</label>
            <input class="form-control" id="username" name="username" value="<?= e($_POST['username'] ?? '') ?>" required autofocus>
          </div>
          <div class="mb-4">
            <label class="form-label" for="password">Password</label>
            <div class="input-group">
              <input type="password" class="form-control" id="password" name="password" required>
              <button class="btn btn-light border" type="button" data-toggle-password="#password" aria-label="Tampilkan password"><i class="bi bi-eye"></i></button>
            </div>
          </div>
          <button class="btn btn-primary-ink w-100" type="submit">Masuk</button>
        </form>
        <p class="text-center text-muted small mt-4 mb-0">Belum punya akun? <a href="<?= url('daftar.php') ?>" class="fw-semibold">Daftar sekarang</a></p>
      </div>
    </div>
  </div>
</div>
<?php require 'includes/footer.php'; ?>
