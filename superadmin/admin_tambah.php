<?php
require '../includes/auth.php';
wajib_role('superadmin');
require '_validasi_admin.php';

$data = ['nama' => '', 'username' => '', 'password' => ''];
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    [$data, $errors] = ambil_input_admin(0, 'tambah');
    if (!$errors) {
        db_exec("INSERT INTO users (nama, username, password, role) VALUES (?,?,?, 'admin')", 'sss',
            [$data['nama'], $data['username'], password_hash($data['password'], PASSWORD_DEFAULT)]);
        flash_set('success', 'Akun admin "' . $data['username'] . '" berhasil dibuat.');
        redirect('superadmin/dashboard.php');
    }
}
$mode = 'tambah';
$judul = 'Tambah admin';
require '../includes/header.php';
?>
<div class="container page-pad">
  <a class="back-link" href="<?= url('superadmin/dashboard.php') ?>"><i class="bi bi-arrow-left me-1"></i>Kembali ke dashboard</a>
  <h1 class="page-title mt-2 mb-4">Tambah admin</h1>
  <div class="row"><div class="col-lg-6"><?php require '_form_admin.php'; ?></div></div>
</div>
<?php require '../includes/footer.php'; ?>
