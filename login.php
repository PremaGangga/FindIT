<?php
require_once __DIR__ . '/config/koneksi.php';
require_once __DIR__ . '/includes/auth.php';

if (is_logged_in()) {
    if ($_SESSION['role'] === 'superadmin') {
        header('Location: superadmin/dashboard.php');
    } else {
        header('Location: admin/dashboard.php');
    }
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare(
        'SELECT id, nama, username, password, role
         FROM users
         WHERE username = ?
         LIMIT 1'
    );

    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);

        $_SESSION['user_id'] = (int) $user['id'];
        $_SESSION['nama'] = $user['nama'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        if ($user['role'] === 'superadmin') {
            header('Location: superadmin/dashboard.php');
        } else {
            header('Location: admin/dashboard.php');
        }

        exit;
    }

    $error = 'Username atau password salah.';
}

$pageTitle = 'Login Admin';
require __DIR__ . '/includes/header.php';
?>

<div class="auth-wrap">
    <div class="auth-card">

        <span class="eyebrow">Area Administrator</span>

        <h1 style="margin-top:15px">Selamat Datang</h1>

        <p class="muted">
            Login untuk mengelola laporan FindIT.
        </p>

        <?php if ($error): ?>
            <div class="alert alert-error">
                <?= e($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <div class="field">
                <label for="username">Username</label>

                <input
                    class="input"
                    type="text"
                    id="username"
                    name="username"
                    required
                    autocomplete="username"
                >
            </div>

            <div class="field">
                <label for="password">Password</label>

                <input
                    class="input"
                    type="password"
                    id="password"
                    name="password"
                    required
                    autocomplete="current-password"
                >
            </div>

            <div class="form-actions">
                <button class="btn" type="submit">
                    Login
                </button>

                <a href="index.php" class="btn btn-secondary">
                    Kembali
                </a>
            </div>

        </form>

        <p class="auth-note">
            Halaman ini hanya untuk akun administrator.
        </p>

    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>