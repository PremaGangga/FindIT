<?php
/** Validasi input admin. $idSendiri = id yang sedang diedit (0 jika baru). */
function ambil_input_admin($idSendiri, $mode) {
    $data = ['nama' => trim($_POST['nama'] ?? ''), 'username' => trim($_POST['username'] ?? ''), 'password' => $_POST['password'] ?? ''];
    $errors = [];
    if ($data['nama'] === '') $errors[] = 'Nama lengkap wajib diisi.';
    if (!preg_match('/^[A-Za-z0-9_]{3,30}$/', $data['username'])) {
        $errors[] = 'Username harus 3 sampai 30 karakter: huruf, angka, atau garis bawah.';
    } elseif (db_one('SELECT id FROM users WHERE username = ? AND id <> ?', 'si', [$data['username'], $idSendiri])) {
        $errors[] = 'Username sudah dipakai. Pilih yang lain.';
    }
    if ($mode === 'tambah' || $data['password'] !== '') {
        if (strlen($data['password']) < 6) $errors[] = 'Password minimal 6 karakter.';
    }
    return [$data, $errors];
}
