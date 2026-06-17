-- ============================================
-- MUSLIMAH SHOP - E-Commerce Database
-- Pertemuan 11-16 | Pemrograman Web
-- ============================================

CREATE DATABASE IF NOT EXISTS ecommerce_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ecommerce_db;

-- ─── TABEL USERS ────────────────────────────
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ─── TABEL KATEGORI ─────────────────────────
CREATE TABLE IF NOT EXISTS kategori (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(100) NOT NULL,
    deskripsi TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ─── TABEL PRODUK ───────────────────────────
CREATE TABLE IF NOT EXISTS produk (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_produk VARCHAR(200) NOT NULL,
    deskripsi TEXT,
    harga DECIMAL(15,2) NOT NULL,
    stok INT DEFAULT 0,
    gambar VARCHAR(255),
    kategori_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (kategori_id) REFERENCES kategori(id) ON DELETE SET NULL
);

-- ─── TABEL KERANJANG ────────────────────────
CREATE TABLE IF NOT EXISTS keranjang (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    produk_id INT NOT NULL,
    jumlah INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (produk_id) REFERENCES produk(id) ON DELETE CASCADE
);

-- ─── TABEL PESANAN ──────────────────────────
CREATE TABLE IF NOT EXISTS pesanan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_harga DECIMAL(15,2) NOT NULL,
    status ENUM('pending','diproses','dikirim','selesai','dibatalkan') DEFAULT 'pending',
    alamat_kirim TEXT NOT NULL,
    catatan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- ─── TABEL DETAIL PESANAN ───────────────────
CREATE TABLE IF NOT EXISTS detail_pesanan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pesanan_id INT NOT NULL,
    produk_id INT NOT NULL,
    jumlah INT NOT NULL,
    harga_satuan DECIMAL(15,2) NOT NULL,
    FOREIGN KEY (pesanan_id) REFERENCES pesanan(id) ON DELETE CASCADE,
    FOREIGN KEY (produk_id) REFERENCES produk(id)
);

-- ─── DATA AWAL: USERS ────────────────────────
-- Password: admin123 (bcrypt hash)
INSERT INTO users (nama, email, password, role) VALUES
('Administrator', 'admin@shop.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('Siti Aisyah', 'user@shop.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user');

-- ─── DATA AWAL: KATEGORI ─────────────────────
INSERT INTO produk (nama_produk, deskripsi, harga, stok, gambar, kategori_id) VALUES
('Hijab Voal Premium', 'Hijab voal anti tembus, lembut di kulit, 145x145cm', 85000, 50, 'hijab voal.jpg', 1),

('Hijab Pashmina Silk', 'Bahan silk premium, jatuh dan elegan', 120000, 30, 'pasmina.jpg', 1),

('Gamis Syari Polos', 'Gamis full kancing, bahan crepe premium, all size', 250000, 25, 'gamis syari.jpg', 2),

('Gamis Brokat Modern', 'Kombinasi brokat dan jersey, cocok untuk pesta', 450000, 15, 'gamis brokat.jpg', 2),

('Bros Bunga Cantik', 'Bros bunga handmade, berbagai warna tersedia', 35000, 100, 'bros.jpg', 3),

('Manset Panjang', 'Manset polos stretch, cocok untuk semua baju', 25000, 80, 'manset.jpg', 3),

('Tas Selempang Cantik', 'Bahan kulit sintetis premium, banyak kantong', 185000, 20, 'tas salempang.jpg', 4),

('Dompet Muslimah', 'Dompet panjang motif geometris, anti air', 95000, 35, 'dompet.jpg', 4);
