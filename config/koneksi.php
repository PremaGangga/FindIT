<?php
/**
 * Koneksi database + fungsi bantu (helper) yang dipakai semua halaman.
 *
 * Default = pengaturan XAMPP. Untuk hosting (InfinityFree), buat file
 * config/hosting.php (lihat hosting.example.php). File itu tidak ikut Git.
 */

// ---------- Pengaturan database ----------
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'findit_db';

if (file_exists(__DIR__ . '/hosting.php')) {
    require __DIR__ . '/hosting.php';   // menimpa 4 variabel di atas
}

mysqli_report(MYSQLI_REPORT_OFF);
$conn = @mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if (!$conn) {
    http_response_code(500);
    die('Koneksi database gagal. Cek config/koneksi.php dan pastikan MySQL di XAMPP sudah Start.');
}
mysqli_set_charset($conn, 'utf8mb4');

// ---------- Session ----------
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ---------- BASE_URL otomatis ----------
// Lokal  : http://localhost/findit  -> BASE_URL = "/findit"
// Hosting: http://namamu.rf.gd      -> BASE_URL = ""
$__root = str_replace('\\', '/', realpath(__DIR__ . '/..'));
$__doc  = str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT']));
define('BASE_URL', rtrim(str_replace($__doc, '', $__root), '/'));
define('UPLOAD_DIR', realpath(__DIR__ . '/..') . '/assets/uploads/');

// ---------- Helper umum ----------
function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function url($path = '') { return BASE_URL . '/' . ltrim($path, '/'); }
function redirect($path) { header('Location: ' . url($path)); exit; }

// Aman walau ekstensi mbstring tidak aktif di hosting
function teks_panjang($s) { return function_exists('mb_strlen') ? mb_strlen($s) : strlen($s); }
function teks_potong($s, $max) {
    $s = (string)$s;
    if (teks_panjang($s) <= $max) return $s;
    $p = function_exists('mb_substr') ? mb_substr($s, 0, $max - 3) : substr($s, 0, $max - 3);
    return rtrim($p) . '...';
}

function tgl($date) {
    if (!$date) return '-';
    $bulan = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    $t = strtotime($date);
    return date('j', $t) . ' ' . $bulan[(int)date('n', $t)] . ' ' . date('Y', $t);
}

// ---------- Query dengan prepared statement ----------
function db_all($sql, $types = '', $params = []) {
    global $conn;
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) die('Query error: ' . e(mysqli_error($conn)));
    if ($types !== '') mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $rows = $res ? mysqli_fetch_all($res, MYSQLI_ASSOC) : [];
    mysqli_stmt_close($stmt);
    return $rows;
}
function db_one($sql, $types = '', $params = []) {
    $rows = db_all($sql, $types, $params);
    return $rows ? $rows[0] : null;
}
function db_exec($sql, $types = '', $params = []) {
    global $conn;
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) die('Query error: ' . e(mysqli_error($conn)));
    if ($types !== '') mysqli_stmt_bind_param($stmt, $types, ...$params);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $ok;
}

// ---------- Pesan sekali tampil (flash) ----------
function flash_set($type, $msg) { $_SESSION['flash'] = ['type' => $type, 'msg' => $msg]; }
function flash_get() {
    if (!isset($_SESSION['flash'])) return null;
    $f = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $f;
}

// ---------- CSRF (melindungi form) ----------
function csrf_token() {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16));
    return $_SESSION['csrf'];
}
function csrf_field() {
    return '<input type="hidden" name="csrf" value="' . csrf_token() . '">';
}
function csrf_check() {
    $ok = isset($_POST['csrf'], $_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $_POST['csrf']);
    if (!$ok) {
        http_response_code(419);
        die('Sesi form tidak valid. Kembali ke halaman sebelumnya lalu coba lagi.');
    }
}

// ---------- Tampilan status & kategori ----------
function status_label($s) {
    $m = ['hilang' => 'Hilang', 'ditemukan' => 'Ditemukan', 'dikembalikan' => 'Dikembalikan'];
    return $m[$s] ?? $s;
}
function verifikasi_label($v) {
    $m = ['pending' => 'Menunggu verifikasi', 'disetujui' => 'Tayang', 'ditolak' => 'Ditolak'];
    return $m[$v] ?? $v;
}
function kategori_icon($nama) {
    $m = ['Elektronik' => 'bi-laptop', 'Tas & Dompet' => 'bi-bag', 'Dokumen' => 'bi-file-earmark-text',
          'Pakaian' => 'bi-tag', 'Aksesoris' => 'bi-watch', 'Lainnya' => 'bi-box-seam'];
    return $m[$nama] ?? 'bi-box-seam';
}
// "Tas & Dompet" -> "tas-dompet" (dipakai untuk warna pill kategori di CSS)
function kategori_slug($nama) {
    return trim(preg_replace('/[^a-z0-9]+/', '-', strtolower((string)$nama)), '-') ?: 'lainnya';
}

// ---------- Upload foto ----------
// Return: ['ok'=>true,'file'=>nama|null] atau ['ok'=>false,'error'=>pesan]
function upload_foto($file) {
    if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) return ['ok' => true, 'file' => null];
    if ($file['error'] !== UPLOAD_ERR_OK)  return ['ok' => false, 'error' => 'Upload foto gagal. Coba lagi.'];
    if ($file['size'] > 2 * 1024 * 1024)   return ['ok' => false, 'error' => 'Ukuran foto maksimal 2 MB.'];

    $info = @getimagesize($file['tmp_name']);
    $allow = [IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_WEBP => 'webp'];
    if (!$info || !isset($allow[$info[2]])) return ['ok' => false, 'error' => 'Foto harus berformat JPG, PNG, atau WEBP.'];

    $name = bin2hex(random_bytes(8)) . '.' . $allow[$info[2]];
    if (!is_dir(UPLOAD_DIR)) @mkdir(UPLOAD_DIR, 0755, true);
    if (!move_uploaded_file($file['tmp_name'], UPLOAD_DIR . $name)) {
        return ['ok' => false, 'error' => 'Foto tidak bisa disimpan. Cek izin folder assets/uploads.'];
    }
    return ['ok' => true, 'file' => $name];
}
function hapus_foto($name) {
    if ($name && preg_match('/^[a-f0-9]+\.(jpg|png|webp)$/', $name) && file_exists(UPLOAD_DIR . $name)) {
        @unlink(UPLOAD_DIR . $name);
    }
}
