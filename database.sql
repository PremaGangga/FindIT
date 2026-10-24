CREATE DATABASE IF NOT EXISTS findit
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE findit;

CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('superadmin', 'admin') NOT NULL DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(80) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS items (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(150) NOT NULL,
    deskripsi TEXT NOT NULL,
    kategori_id INT UNSIGNED NULL,
    jenis ENUM('hilang', 'ditemukan') NOT NULL,
    lokasi VARCHAR(150) NOT NULL,
    tanggal_kejadian DATE NOT NULL,
    kontak VARCHAR(120) NOT NULL,
    foto VARCHAR(255) NULL,
    status ENUM(
        'hilang',
        'ditemukan',
        'dikembalikan'
    ) NOT NULL DEFAULT 'hilang',
    created_by INT UNSIGNED NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_items_category
        FOREIGN KEY (kategori_id)
        REFERENCES categories(id)
        ON DELETE SET NULL,

    CONSTRAINT fk_items_user
        FOREIGN KEY (created_by)
        REFERENCES users(id)
        ON DELETE SET NULL
);

INSERT IGNORE INTO categories (nama) VALUES
('Elektronik'),
('Dokumen/Kartu'),
('Tas'),
('Aksesori'),
('Kunci'),
('Lainnya');