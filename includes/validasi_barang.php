<?php
/** Ambil & validasi input form barang. $statusBoleh = status yang boleh dipilih pengguna. Return [$data, $errors] */
function ambil_input_barang($statusBoleh = ['hilang', 'ditemukan', 'dikembalikan']) {
    $data = [
        'judul'       => trim($_POST['judul'] ?? ''),
        'deskripsi'   => trim($_POST['deskripsi'] ?? ''),
        'kategori_id' => (int)($_POST['kategori_id'] ?? 0),
        'lokasi'      => trim($_POST['lokasi'] ?? ''),
        'tanggal'     => $_POST['tanggal'] ?? '',
        'status'      => $_POST['status'] ?? 'hilang',
        'kontak'      => trim($_POST['kontak'] ?? ''),
    ];
    $errors = [];
    if ($data['judul'] === '' || teks_panjang($data['judul']) > 150) $errors[] = 'Nama barang wajib diisi (maksimal 150 karakter).';
    if (!db_one('SELECT id FROM kategori WHERE id = ?', 'i', [$data['kategori_id']])) $errors[] = 'Pilih kategori yang tersedia.';
    if ($data['lokasi'] === '') $errors[] = 'Lokasi wajib diisi.';
    $d = DateTime::createFromFormat('Y-m-d', $data['tanggal']);
    if (!$d || $d->format('Y-m-d') !== $data['tanggal']) $errors[] = 'Tanggal tidak valid.';
    elseif ($data['tanggal'] > date('Y-m-d')) $errors[] = 'Tanggal tidak boleh di masa depan.';
    if (!in_array($data['status'], $statusBoleh, true)) $errors[] = 'Status tidak valid.';
    return [$data, $errors];
}
