-- DATABASE: ggmart
CREATE DATABASE IF NOT EXISTS ggmart;
USE ggmart;

-- TABLE: user
CREATE TABLE IF NOT EXISTS user (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    no_hp VARCHAR(15),
    alamat VARCHAR(255),
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user', 'pimpinan') DEFAULT 'user',
    tanggal_dibuat DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- TABLE: kategori
CREATE TABLE IF NOT EXISTS kategori (
    id_kategori INT AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(100) NOT NULL,
    deskripsi TEXT
);

-- TABLE: produk
CREATE TABLE IF NOT EXISTS produk (
    kode_produk VARCHAR(15) PRIMARY KEY,
    id_kategori INT,
    nama_produk VARCHAR(150) NOT NULL,
    harga_jual DECIMAL(12, 2) NOT NULL,
    deskripsi TEXT,
    satuan_dasar VARCHAR(10),
    gambar VARCHAR(255),
    asal_produk VARCHAR(100),
    stok INT DEFAULT 0,
    terjual INT DEFAULT 0,
    tanggal_dibuat DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_produk_kategori
        FOREIGN KEY (id_kategori)
        REFERENCES kategori(id_kategori)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
);

-- TABLE: mutasi_stok
CREATE TABLE IF NOT EXISTS mutasi_stok (
  id_mutasi INT AUTO_INCREMENT PRIMARY KEY,
  kode_produk VARCHAR(15),
  type ENUM('masuk','keluar') NOT NULL,
  jumlah INT NOT NULL,
  harga_pokok DECIMAL(12, 2),
  keterangan TEXT,
  sisa_stok INT,
  tanggal DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (kode_produk) REFERENCES produk(kode_produk)
    ON DELETE SET NULL
    ON UPDATE CASCADE
);

-- TABLE: transaksi
CREATE TABLE IF NOT EXISTS transaksi (
    kode_transaksi VARCHAR(15) PRIMARY KEY,
    id_user INT,
    tanggal_transaksi DATETIME DEFAULT CURRENT_TIMESTAMP,
    total_harga DECIMAL(12, 2) NOT NULL,
    total_pokok DECIMAL(12, 2),
    status ENUM('diproses','dibatalkan','selesai') DEFAULT 'diproses',
    metode_bayar ENUM('qris','tunai') DEFAULT 'tunai',
    CONSTRAINT fk_transaksi_user
        FOREIGN KEY (id_user)
        REFERENCES user(id_user)
        ON DELETE SET NULL
        ON UPDATE CASCADE
);

-- TABLE: detail_transaksi
CREATE TABLE IF NOT EXISTS detail_transaksi (
    id_detail INT AUTO_INCREMENT PRIMARY KEY,
    kode_transaksi VARCHAR(15) NOT NULL,
    kode_produk VARCHAR(15),
    jumlah INT DEFAULT 1,
    harga_satuan DECIMAL(12, 2),
    harga_pokok DECIMAL(12, 2),

    CONSTRAINT fk_detail_transaksi
        FOREIGN KEY (kode_transaksi)
        REFERENCES transaksi(kode_transaksi)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_detail_produk
        FOREIGN KEY (kode_produk)
        REFERENCES produk(kode_produk)
        ON DELETE SET NULL
        ON UPDATE CASCADE
);

-- TABLE: keranjang
CREATE TABLE IF NOT EXISTS keranjang (
    id_keranjang INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT,
    kode_produk VARCHAR(15),
    jumlah INT DEFAULT 1,
    CONSTRAINT fk_keranjang_user
        FOREIGN KEY (id_user)
        REFERENCES user(id_user)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_keranjang_produk
        FOREIGN KEY (kode_produk)
        REFERENCES produk(kode_produk)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

-- index
ALTER TABLE produk
  ADD INDEX idx_kode_produk (kode_produk),
  ADD INDEX idx_nama_produk (nama_produk),
  ADD INDEX idx_id_kategori (id_kategori),
  ADD INDEX idx_tanggal_dibuat (tanggal_dibuat),
  ADD INDEX idx_produk_penjualan (id_kategori, terjual);

ALTER TABLE kategori ADD INDEX idx_id_kategori (id_kategori);

ALTER TABLE detail_transaksi
  ADD INDEX idx_kode_transaksi (kode_transaksi),
  ADD INDEX idx_kode_produk (kode_produk);

ALTER TABLE transaksi
  ADD INDEX idx_kode_transaksi (kode_transaksi),
  ADD INDEX idx_tanggal_transaksi (tanggal_transaksi),
  ADD INDEX idx_metode_bayar (metode_bayar),
  ADD INDEX idx_id_user (id_user);

ALTER TABLE user
  ADD INDEX idx_email (email),
  ADD INDEX idx_id_user (id_user);

ALTER TABLE mutasi_stok
  ADD INDEX idx_tanggal(tanggal);


ALTER TABLE users 
ADD is_verified TINYINT(1) DEFAULT 0,
ADD verify_token VARCHAR(255),
ADD token_expired DATETIME;
ADD reset_token VARCHAR(255),
ADD reset_expired DATETIME;