<?php
require_once 'includes/config.php';
$page_title = 'Beranda';
include 'includes/header.php';

// Produk terbaru
$produk_baru = $conn->query("
    SELECT p.*, k.nama_kategori
    FROM produk p
    LEFT JOIN kategori k ON p.kategori_id = k.id
    ORDER BY p.created_at DESC
    LIMIT 6
");

// kategori
$kategori_all = $conn->query("
    SELECT *, 
    (SELECT COUNT(*) FROM produk WHERE kategori_id = kategori.id) as jumlah_produk 
    FROM kategori
");

// statistik
$total_produk  = $conn->query("SELECT COUNT(*) as c FROM produk")->fetch_assoc()['c'];
$total_user    = $conn->query("SELECT COUNT(*) as c FROM users WHERE role='user'")->fetch_assoc()['c'];
$total_pesanan = $conn->query("SELECT COUNT(*) as c FROM pesanan")->fetch_assoc()['c'];
?>

<!-- HERO SECTION  -->
<div class="card mb-5" style="background: linear-gradient(135deg, #0f3460, #2c5364); color:white; border-radius:20px; overflow:hidden;">
    <div class="card-body p-5">
        <div class="row align-items-center">
            <div class="col-md-7">
                <span class="badge bg-warning text-dark mb-3">🌟 Koleksi Terbaru 2024</span>
                <h1 class="display-5 fw-700 mb-3">Busana Muslimah <br><span style="color:#f9c74f;">Elegan & Syari</span></h1>
                <p class="mb-4" style="opacity:0.9;">Temukan koleksi hijab, gamis, dan aksesoris muslimah pilihan. Kualitas premium, harga bersahabat.</p>

                <div class="d-flex gap-3">
                    <a href="produk.php" class="btn btn-warning fw-600 px-4">Lihat Produk</a>

                    <?php if (!isLoggedIn()): ?>
                    <a href="register.php" class="btn btn-outline-light fw-600 px-4">Daftar Gratis</a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-md-5 text-center mt-4 mt-md-0">
                <div class="row g-3">
                    <div class="col-4">
                        <div class="p-3 rounded-3" style="background:rgba(255,255,255,0.1);">
                            <div class="fw-700 fs-3" style="color:#f9c74f;"><?= $total_produk ?>+</div>
                            <div style="font-size:0.75rem;">Produk</div>
                        </div>
                    </div>

                    <div class="col-4">
                        <div class="p-3 rounded-3" style="background:rgba(255,255,255,0.1);">
                            <div class="fw-700 fs-3" style="color:#f9c74f;"><?= $total_user ?>+</div>
                            <div style="font-size:0.75rem;">Member</div>
                        </div>
                    </div>

                    <div class="col-4">
                        <div class="p-3 rounded-3" style="background:rgba(255,255,255,0.1);">
                            <div class="fw-700 fs-3" style="color:#f9c74f;">4.9⭐</div>
                            <div style="font-size:0.75rem;">Rating</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- KATEGORI  -->
<h4 class="fw-700 mb-3"><i class="bi bi-tags me-2"></i>Kategori Produk</h4>
<div class="row g-3 mb-5">

<?php while ($kat = $kategori_all->fetch_assoc()): ?>
    <div class="col-6 col-md-3">
        <div class="card p-3 text-center">
            <div style="font-size:2rem;">🧕</div>
            <div><?= clean($kat['nama_kategori']) ?></div>
            <small><?= $kat['jumlah_produk'] ?> produk</small>
        </div>
    </div>
<?php endwhile; ?>

</div>

<!-- PRODUK TERBARU  -->
<h4>Produk Terbaru</h4>

<div class="row">

<?php while ($p = $produk_baru->fetch_assoc()): ?>

<div class="col-6 col-md-4 mb-4">
    <div class="card h-100 product-card">

        <!-- GAMBAR FIX  -->
        <div style="height:200px; display:flex; align-items:center; justify-content:center; background:#f5f5f5; border-radius:10px 10px 0 0;">

            <?php
            $gambar = $p['gambar'];

            // karena folder image ADA DI DALAM FOLDER muslimah
            $imgPath = 'image/' . $gambar;
            $imgReal = __DIR__ . '/image/' . $gambar;
            ?>

            <?php if (!empty($gambar) && file_exists($imgReal)): ?>
                <img src="<?= $imgPath ?>" style="max-height:180px; max-width:100%;" alt="<?= clean($p['nama_produk']) ?>">
            <?php else: ?>
                <span style="font-size:4rem;">🛍️</span>
            <?php endif; ?>

        </div>

        <!-- BODY PRODUK -->
        <div class="card-body d-flex flex-column">

            <span class="badge bg-light text-secondary mb-2">
                <?= clean($p['nama_kategori'] ?? 'Produk') ?>
            </span>

            <h6 class="fw-600">
                <?= clean($p['nama_produk']) ?>
            </h6>

            <p class="text-muted small flex-grow-1">
                <?= clean(substr($p['deskripsi'], 0, 60)) ?>...
            </p>

            <div class="d-flex justify-content-between align-items-center mt-auto">
                <span class="fw-700 text-primary">
                    <?= rupiah($p['harga']) ?>
                </span>

                <span class="badge <?= $p['stok'] > 0 ? 'bg-success' : 'bg-danger' ?>">
                    <?= $p['stok'] > 0 ? 'Stok: '.$p['stok'] : 'Habis' ?>
                </span>
            </div>

            <div class="mt-3 d-grid gap-2">

                <a href="detail_produk.php?id=<?= $p['id'] ?>" class="btn btn-outline-primary btn-sm">
                    Detail
                </a>

                <?php if (isLoggedIn() && $p['stok'] > 0): ?>
                <a href="keranjang.php?action=tambah&id=<?= $p['id'] ?>" class="btn btn-primary btn-sm">
                    Tambah ke Keranjang
                </a>
                <?php endif; ?>

            </div>

        </div>

    </div>
</div>

<?php endwhile; ?>

</div>

<?php include 'includes/footer.php'; ?>