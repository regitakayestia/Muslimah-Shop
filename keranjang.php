<?php
// ============================================
// KERANJANG BELANJA
// File: keranjang.php
// Pertemuan 15: Shopping Cart & Checkout
// ============================================
require_once 'includes/config.php';
requireLogin();

$action = $_GET['action'] ?? '';
$id     = (int)($_GET['id'] ?? 0);
$uid    = $_SESSION['user_id'];

// ─── TAMBAH KE KERANJANG ─────────────────────
if ($action === 'tambah' && $id) {
    // Cek apakah produk sudah ada di keranjang
    $cek = $conn->prepare("SELECT id, jumlah FROM keranjang WHERE user_id=? AND produk_id=?");
    $cek->bind_param("ii", $uid, $id);
    $cek->execute();
    $ada = $cek->get_result()->fetch_assoc();

    if ($ada) {
        // Update jumlah
        $conn->query("UPDATE keranjang SET jumlah=jumlah+1 WHERE id={$ada['id']}");
    } else {
        $ins = $conn->prepare("INSERT INTO keranjang (user_id, produk_id, jumlah) VALUES (?,?,1)");
        $ins->bind_param("ii", $uid, $id);
        $ins->execute();
    }
    setFlash('sukses', 'Produk ditambahkan ke keranjang! 🛒');
    redirect(SITE_URL . '/keranjang.php');
}

// ─── UPDATE JUMLAH ───────────────────────────
if ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $jumlah   = (int)($_POST['jumlah'] ?? 1);
    $item_id  = (int)($_POST['item_id'] ?? 0);
    if ($jumlah < 1) $jumlah = 1;
    $conn->query("UPDATE keranjang SET jumlah=$jumlah WHERE id=$item_id AND user_id=$uid");
    redirect(SITE_URL . '/keranjang.php');
}

// ─── HAPUS ITEM ──────────────────────────────
if ($action === 'hapus' && $id) {
    $conn->query("DELETE FROM keranjang WHERE id=$id AND user_id=$uid");
    setFlash('sukses', 'Item dihapus dari keranjang.');
    redirect(SITE_URL . '/keranjang.php');
}

// ─── CHECKOUT ─────────────────────────────────
if ($action === 'checkout' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $alamat  = clean($_POST['alamat'] ?? '');
    $catatan = clean($_POST['catatan'] ?? '');

    if (empty($alamat)) {
        setFlash('error', 'Alamat pengiriman wajib diisi!');
        redirect(SITE_URL . '/keranjang.php');
    }

    // Hitung total
    $items = $conn->query("
        SELECT k.*, p.harga, p.nama_produk, p.stok
        FROM keranjang k JOIN produk p ON k.produk_id = p.id
        WHERE k.user_id = $uid
    ");

    $total = 0;
    $items_arr = [];
    while ($item = $items->fetch_assoc()) {
        if ($item['stok'] < $item['jumlah']) {
            setFlash('error', "Stok {$item['nama_produk']} tidak cukup!");
            redirect(SITE_URL . '/keranjang.php');
        }
        $total += $item['harga'] * $item['jumlah'];
        $items_arr[] = $item;
    }

    if (empty($items_arr)) {
        setFlash('error', 'Keranjang kosong!');
        redirect(SITE_URL . '/keranjang.php');
    }

    // Buat pesanan
    $stmt = $conn->prepare("INSERT INTO pesanan (user_id, total_harga, alamat_kirim, catatan) VALUES (?,?,?,?)");
    $stmt->bind_param("idss", $uid, $total, $alamat, $catatan);
    $stmt->execute();
    $pesanan_id = $conn->insert_id;

    // Insert detail & kurangi stok
    foreach ($items_arr as $item) {
        $di = $conn->prepare("INSERT INTO detail_pesanan (pesanan_id, produk_id, jumlah, harga_satuan) VALUES (?,?,?,?)");
        $di->bind_param("iiid", $pesanan_id, $item['produk_id'], $item['jumlah'], $item['harga']);
        $di->execute();
        $conn->query("UPDATE produk SET stok=stok-{$item['jumlah']} WHERE id={$item['produk_id']}");
    }

    // Kosongkan keranjang
    $conn->query("DELETE FROM keranjang WHERE user_id=$uid");
    setFlash('sukses', "Pesanan #$pesanan_id berhasil dibuat! 🎉");
    redirect(SITE_URL . '/pesanan.php?id=' . $pesanan_id);
}

// ─── AMBIL DATA KERANJANG ─────────────────────
$keranjang = $conn->query("
    SELECT k.id as item_id, k.jumlah, p.id as produk_id, p.nama_produk, p.harga, p.gambar, p.stok, k2.nama_kategori
    FROM keranjang k
    JOIN produk p ON k.produk_id = p.id
    LEFT JOIN kategori k2 ON p.kategori_id = k2.id
    WHERE k.user_id = $uid
    ORDER BY k.created_at DESC
");

$items = $keranjang->fetch_all(MYSQLI_ASSOC);
$total = array_sum(array_map(fn($i) => $i['harga'] * $i['jumlah'], $items));

$page_title = 'Keranjang Belanja';
include 'includes/header.php';
?>

<h4 class="fw-700 mb-4"><i class="bi bi-cart3 me-2"></i>Keranjang Belanja</h4>

<?php if (empty($items)): ?>
<div class="text-center py-5">
    <div style="font-size:5rem;">🛒</div>
    <h5 class="mt-3">Keranjangmu masih kosong</h5>
    <p class="text-muted">Yuk belanja produk muslimah pilihan!</p>
    <a href="produk.php" class="btn btn-primary mt-2"><i class="bi bi-grid me-2"></i>Lihat Produk</a>
</div>

<?php else: ?>
<div class="row g-4">
    <!-- DAFTAR ITEM -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-body p-0">
                <?php foreach ($items as $item): ?>
                <div class="d-flex align-items-center gap-3 p-3 border-bottom">
                    <div style="width:70px;height:70px;background:#f0f4f8;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;overflow:hidden;">

    <?php
    // FIX GAMBAR SAMA SEPERTI INDEX.PHP
    $gambar = $item['gambar'];
    $imgPath = 'image/' . $gambar;
    $imgReal = __DIR__ . '/image/' . $gambar;
    ?>

    <?php if (!empty($gambar) && file_exists($imgReal)): ?>
        <img src="<?= $imgPath ?>"
             width="70"
             height="70"
             style="object-fit:cover;border-radius:10px;">
    <?php else: ?>
        <span style="font-size:2rem;">🛍️</span>
    <?php endif; ?>

</div>
                    <div class="flex-grow-1">
                        <div class="fw-600"><?= clean($item['nama_produk']) ?></div>
                        <div class="text-muted small"><?= clean($item['nama_kategori'] ?? '') ?></div>
                        <div class="fw-700 text-primary mt-1"><?= rupiah($item['harga']) ?></div>
                    </div>
                    <!-- Update jumlah -->
                    <form method="POST" action="?action=update" class="d-flex align-items-center gap-2">
                        <input type="hidden" name="item_id" value="<?= $item['item_id'] ?>">
                        <button type="button" onclick="this.form.jumlah.value=Math.max(1,+this.form.jumlah.value-1);this.form.submit()" class="btn btn-sm btn-outline-secondary">−</button>
                        <input type="number" name="jumlah" value="<?= $item['jumlah'] ?>" min="1" max="<?= $item['stok'] ?>" class="form-control form-control-sm text-center" style="width:55px;">
                        <button type="button" onclick="this.form.jumlah.value=Math.min(<?= $item['stok'] ?>,+this.form.jumlah.value+1);this.form.submit()" class="btn btn-sm btn-outline-secondary">+</button>
                    </form>
                    <div class="text-end" style="min-width:100px;">
                        <div class="fw-700"><?= rupiah($item['harga'] * $item['jumlah']) ?></div>
                        <a href="?action=hapus&id=<?= $item['item_id'] ?>" class="btn btn-sm btn-outline-danger mt-1"
                           onclick="return confirm('Hapus item ini?')">
                            <i class="bi bi-trash"></i>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- RINGKASAN & CHECKOUT -->
    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-header" style="background:#f8f9fa;"><strong>💰 Ringkasan Belanja</strong></div>
            <div class="card-body">
                <?php foreach ($items as $item): ?>
                <div class="d-flex justify-content-between small mb-1">
                    <span class="text-muted"><?= clean(substr($item['nama_produk'],0,20)) ?>... (×<?= $item['jumlah'] ?>)</span>
                    <span><?= rupiah($item['harga'] * $item['jumlah']) ?></span>
                </div>
                <?php endforeach; ?>
                <hr>
                <div class="d-flex justify-content-between fw-700 fs-5">
                    <span>Total</span>
                    <span class="text-primary"><?= rupiah($total) ?></span>
                </div>
            </div>
        </div>

        <!-- FORM CHECKOUT -->
        <div class="card">
            <div class="card-header" style="background:#f8f9fa;"><strong>🏠 Alamat Pengiriman</strong></div>
            <div class="card-body">
                <form method="POST" action="?action=checkout">
                    <div class="mb-3">
                        <label class="form-label fw-600">Alamat Lengkap <span class="text-danger">*</span></label>
                        <textarea name="alamat" class="form-control" rows="3" placeholder="Jl. Contoh No. 1, Kelurahan, Kecamatan, Kota, Kode Pos" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-600">Catatan (opsional)</label>
                        <input type="text" name="catatan" class="form-control" placeholder="Warna, ukuran, dll.">
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-2"
                            onclick="return confirm('Konfirmasi pesanan senilai <?= rupiah($total) ?>?')">
                        <i class="bi bi-check-circle me-2"></i>Buat Pesanan (<?= rupiah($total) ?>)
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
