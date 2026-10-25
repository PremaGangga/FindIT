<?php
require '../includes/auth.php';
wajib_role('superadmin');
require '_validasi_admin.php';

$id = (int)($_GET['id'] ?? 0);
$lama = db_one("SELECT * FROM users WHERE id = ? AND role = 'admin'", 'i', [$id]);
if (!$lama) { flash_set('danger', 'Akun admin tidak ditemukan.'); redirect('superadmin/dashboard.php'); }

$data = ['nama' => $lama['nama'], 'username' => $lama['username'], 'password' => ''];
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    [$data, $errors] = ambil_input_admin($id, 'edit');
    if (!$errors) {
        if ($data['password'] !== '') {
            db_exec('UPDATE users SET nama=?, username=?, password=? WHERE id=?', 'sssi',
                [$data['nama'], $data['username'], password_hash($data['password'], PASSWORD_DEFAULT), $id]);
        } else {
            db_exec('UPDATE users SET nama=?, username=? WHERE id=?', 'ssi', [$data['nama'], $data['username'], $id]);
        }
        flash_set('success', 'Data admin berhasil diperbarui.');
        redirect('superadmin/dashboard.php');
    }
}
$mode = 'edit';
$judul = 'Ubah admin';
require '../includes/header.php';
?>
<div class="container page-pad">
  <a class="back-link" href="<?= url('superadmin/dashboard.php') ?>"><i class="bi bi-arrow-left me-1"></i>Kembali ke dashboard</a>
  <h1 class="page-title mt-2 mb-4">Ubah admin</h1>
  <div class="row"><div class="col-lg-6"><?php require '_form_admin.php'; ?></div></div>
</div>
<?php require '../includes/footer.php'; ?>
