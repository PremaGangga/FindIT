<?php
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../includes/auth.php';

require_role('superadmin');

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

$stmt = $pdo->prepare(
    'SELECT id, nama, username, role FROM users WHERE id = ?'
);

$stmt->execute([$id ?: 0]);
$user = $stmt->fetch();

if (!$user) {
    http_response_code(404);
    exit('Akun tidak ditemukan.');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $role = $_POST['role'] ?? '';
    $password = $_POST['password'] ?? '';

    if (
        $nama === '' ||
        !preg_match('/^[a-zA-Z0-9_.-]{3,50}$/', $username) ||
        !in_array($role, ['admin', 'superadmin'], true) ||
        ($password !== '' && strlen($password) < 8)
    ) {
        $error = 'Data tidak valid. Password baru minimal 8 karakter.';
    } elseif (
        (int) $user['id'] === (int) $_SESSION['user_id'] &&
        $role !== 'superadmin'
    ) {
        $error = 'Anda tidak dapat mengubah role akun sendiri menjadi admin.';
    } else {
        try {
            if ($password !== '') {
                $stmt = $pdo->prepare(
                    'UPDATE users
                     SET nama = ?, username = ?, role = ?, password = ?
                     WHERE id = ?'
                );

                $stmt->execute([
                    $nama,
                    $username,
                    $role,
                    password_hash($password, PASSWORD_DEFAULT),
                    $id
                ]);
            } else {
                $stmt = $pdo->prepare(
                    'UPDATE users
                     SET nama = ?, username = ?, role = ?
                     WHERE id = ?'
                );

                $stmt->execute([
                    $nama,
                    $username,
                    $role,
                    $id
                ]);
            }

            if ((int) $id === (int) $_SESSION['user_id']) {
                $_SESSION['nama'] = $nama;
                $_SESSION['username'] = $username;
            }

            header('Location: dashboard.php?user_updated=1');
            exit;

        } catch (PDOException $e) {
            $error = $e->getCode() === '23000'
                ? 'Username sudah digunakan.'
                : 'Gagal memperbarui akun.';
        }
    }

    $user['nama'] = $nama;
    $user['username'] = $username;
    $user['role'] = $role;
}

$pageTitle = 'Edit Akun';
$assetPrefix = '../';
$homePrefix = '../';

require __DIR__ . '/../includes/header.php';
?>

<div class="section-head">
    <h1>Edit Akun</h1>

    <a href="dashboard.php" class="btn btn-secondary">
        ← Dashboard
    </a>
</div>

<?php if ($error): ?>
    <div class="alert alert-error"><?= e($error) ?></div>
<?php endif; ?>

<form class="panel" method="POST">

    <div class="form-grid">

        <div class="field">
            <label>Nama Lengkap</label>
            <input class="input" name="nama" required
                   value="<?= e($user['nama']) ?>">
        </div>

        <div class="field">
            <label>Username</label>
            <input class="input" name="username" required
                   value="<?= e($user['username']) ?>">
        </div>

        <div class="field">
            <label>Password Baru</label>
            <input class="input" type="password" name="password"
                   minlength="8"
                   placeholder="Kosongkan jika tidak diubah">
        </div>

        <div class="field">
            <label>Role</label>

            <select class="select" name="role">
                <option value="admin"
                    <?= $user['role'] === 'admin' ? 'selected' : '' ?>>
                    Admin
                </option>

                <option value="superadmin"
                    <?= $user['role'] === 'superadmin' ? 'selected' : '' ?>>
                    Superadmin
                </option>
            </select>
        </div>

    </div>

    <div class="form-actions">
        <button class="btn" type="submit">
            Simpan Perubahan
        </button>

        <a href="dashboard.php" class="btn btn-secondary">
            Batal
        </a>
    </div>

</form>

<?php require __DIR__ . '/../includes/footer.php'; ?>