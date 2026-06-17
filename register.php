<?php
// ============================================
// HALAMAN REGISTER
// File: register.php
// Pertemuan 12: Registrasi User Baru
// ============================================
require_once 'includes/config.php';

if (isLoggedIn()) redirect(SITE_URL . '/index.php');

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama     = clean($_POST['nama'] ?? '');
    $email    = clean($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $konfirm  = $_POST['konfirm'] ?? '';

    if (empty($nama) || empty($email) || empty($password) || empty($konfirm)) {
        $error = 'Semua field wajib diisi!';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter!';
    } elseif ($password !== $konfirm) {
        $error = 'Konfirmasi password tidak cocok!';
    } else {
        // Cek email sudah dipakai
        $cek = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $cek->bind_param("s", $email);
        $cek->execute();
        $cek->store_result();

        if ($cek->num_rows > 0) {
            $error = 'Email sudah terdaftar! Gunakan email lain.';
        } else {
            // Hash password
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $ins  = $conn->prepare("INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, 'user')");
            $ins->bind_param("sss", $nama, $email, $hash);

            if ($ins->execute()) {
                setFlash('sukses', 'Akun berhasil dibuat! Silakan login.');
                redirect(SITE_URL . '/login.php');
            } else {
                $error = 'Gagal membuat akun. Coba lagi.';
            }
            $ins->close();
        }
        $cek->close();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar | Muslimah Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background: linear-gradient(135deg, #0f3460, #2c5364, #1a7a63); min-height: 100vh; display: flex; align-items: center; }
        .card { border-radius: 20px; border: none; box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
        .form-control { border-radius: 10px; padding: 11px 15px; border: 2px solid #e9ecef; }
        .form-control:focus { border-color: #2c5364; box-shadow: 0 0 0 0.25rem rgba(44,83,100,0.15); }
        .btn-reg { background: linear-gradient(135deg, #0f3460, #2c5364); border: none; border-radius: 10px; padding: 12px; font-weight: 600; }
        .input-group-text { border: 2px solid #e9ecef; }
        .password-strength { height: 4px; border-radius: 2px; margin-top: 6px; transition: all 0.3s; }
    </style>
</head>
<body>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="text-center mb-4 text-white">
                <h2 class="fw-700">🌙 Muslimah Shop</h2>
                <p>Buat akun baru untuk mulai berbelanja</p>
            </div>
            <div class="card p-4 p-md-5">
                <h4 class="fw-700 mb-1">Daftar Akun</h4>
                <p class="text-muted mb-4">Gratis dan mudah! ✨</p>

                <?php if ($error): ?>
                    <div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-2"></i><?= $error ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="mb-3">
                        <label class="form-label fw-600">Nama Lengkap</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-person text-muted"></i></span>
                            <input type="text" name="nama" class="form-control" placeholder="Nama kamu" value="<?= clean($_POST['nama'] ?? '') ?>" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-600">Email</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-envelope text-muted"></i></span>
                            <input type="email" name="email" class="form-control" placeholder="email@contoh.com" value="<?= clean($_POST['email'] ?? '') ?>" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-600">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-lock text-muted"></i></span>
                            <input type="password" name="password" id="passInput" class="form-control" placeholder="Min. 6 karakter" required oninput="checkStrength(this.value)">
                        </div>
                        <div class="password-strength bg-secondary w-0" id="strengthBar"></div>
                        <small class="text-muted" id="strengthText"></small>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-600">Konfirmasi Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-lock-fill text-muted"></i></span>
                            <input type="password" name="konfirm" class="form-control" placeholder="Ulangi password" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-reg btn-primary w-100 text-white">
                        <i class="bi bi-person-plus me-2"></i>Buat Akun
                    </button>
                </form>
                <p class="text-center mt-3 mb-0">
                    Sudah punya akun? <a href="login.php" class="text-decoration-none fw-600" style="color:#0f3460;">Masuk</a>
                </p>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function checkStrength(pass) {
    const bar = document.getElementById('strengthBar');
    const txt = document.getElementById('strengthText');
    let strength = 0;
    if (pass.length >= 6) strength++;
    if (pass.match(/[A-Z]/)) strength++;
    if (pass.match(/[0-9]/)) strength++;
    if (pass.match(/[^A-Za-z0-9]/)) strength++;
    const colors = ['danger','warning','info','success'];
    const labels = ['Lemah','Sedang','Kuat','Sangat Kuat'];
    const widths = ['25%','50%','75%','100%'];
    if (pass.length > 0) {
        bar.className = `password-strength bg-${colors[strength-1] || 'danger'}`;
        bar.style.width = widths[strength-1] || '25%';
        txt.textContent = `Kekuatan: ${labels[strength-1] || 'Lemah'}`;
        txt.className = `text-${colors[strength-1] || 'danger'}`;
    } else { bar.style.width='0'; txt.textContent=''; }
}
</script>
</body>
</html>
