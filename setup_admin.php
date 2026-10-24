
<?php
require_once __DIR__ . '/config/koneksi.php';

$host = 'localhost';
$db   = 'findit';
$user = 'root';
$pass = '';

// Koneksi langsung agar tidak bergantung pada nama variabel
// yang digunakan di config/koneksi.php.
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die('Koneksi database gagal: ' . $conn->connect_error);
}

$conn->set_charset('utf8mb4');

$nama = 'Super Administrator';
$username = 'superadmin';
$password = 'GantiPassword123!';
$role = 'superadmin';

// Pastikan akun belum ada.
$stmt = $conn->prepare(
    'SELECT id FROM users WHERE username = ?'
);
$stmt->bind_param('s', $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    die('Akun superadmin sudah ada. Periksa tabel users.');
}

// Password disimpan sebagai hash yang aman.
$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare(
    'INSERT INTO users (nama, username, password, role)
     VALUES (?, ?, ?, ?)'
);
$stmt->bind_param('ssss', $nama, $username, $hash, $role);

if ($stmt->execute()) {
    echo 'Akun berhasil dibuat!<br>';
    echo 'Username: superadmin<br>';
    echo 'Password: GantiPassword123!<br>';
    echo 'Silakan login, lalu hapus file setup_admin.php.';
} else {
    echo 'Gagal membuat akun: ' . $stmt->error;
}

$stmt->close();
$conn->close();
?>
