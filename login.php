<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'includes/config.php';

// Redirect jika sudah login
if (isLoggedIn()) {
    redirect(SITE_URL . '/index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = clean($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Email dan password wajib diisi!';
    } else {

        // FIX: users (bukan user)
        $stmt = $conn->prepare("SELECT id, nama, email, password, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {

            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['nama']    = $user['nama'];
                $_SESSION['email']   = $user['email'];
                $_SESSION['role']    = $user['role'];

                setFlash('sukses', 'Selamat datang, ' . $user['nama'] . '! 👋');
                redirect(SITE_URL . '/index.php');

            } else {
                $error = 'Password salah!';
            }

        } else {
            $error = 'Email tidak ditemukan!';
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Muslimah Shop</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

    <!-- FIX FONT LINK -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #0f3460 0%, #2c5364 50%, #1a7a63 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            border-radius:20px;
            border:none;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        .login-left {
            background: linear-gradient(135deg, #0f3460, #16213e);
            color: white;
            padding: 50px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .login-right { padding: 50px 40px; }
        .brand-name { font-size: 2.2rem; font-weight:700; }
        .brand-name span { color:#f9c74f; }
        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
        }
        .form-control:focus {
            border-color: #2c5364;
            box-shadow:0 0 0 0.25rem rgba(44,83,100,0.15);
        }
        .btn-login {
            background: linear-gradient(135deg, #0f3460, #2c5364);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
        }
        .divider {
            display:flex;
            align-items:center;
            gap:10px;
            color:#aaa;
            margin:20px 0;
        }
        .divider::before,
        .divider::after {
            content:'';
            flex:1;
            height:1px;
            background:#e9ecef;
        }
    </style>
</head>

<body>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card login-card">
                <div class="row g-0">

                    <!-- LEFT -->
                    <div class="col-md-5 login-left d-none d-md-flex">
                        <div>
                            <div class="brand-name mb-3">🌙 <span>Muslimah</span> Shop</div>
                            <p style="opacity:0.8; line-height:1.8;">
                                Temukan koleksi busana muslimah terbaik.
                            </p>
                        </div>
                    </div>

                    <!-- RIGHT -->
                    <div class="col-md-7 login-right">

                        <h3 class="fw-bold mb-1">Masuk ke Akun</h3>

                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>

                        <!-- DEMO -->
                        <div class="alert alert-info py-2 mb-3" style="font-size:0.85rem;">
                            Admin: admin@shop.com / password<br>
                            User: user@shop.com / password
                        </div>

                        <form method="POST">

                            <div class="mb-3">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control"
                                       value="<?= clean($_POST['email'] ?? '') ?>" required>
                            </div>

                            <div class="mb-3">
                                <label>Password</label>

                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-lock"></i>
                                    </span>

                                    <input type="password" name="password" id="password"
                                           class="form-control" required>

                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePass()">
                                        <i class="bi bi-eye" id="eyeIcon"></i>
                                    </button>
                                </div>
                            </div>

                            <button class="btn btn-login btn-primary w-100 text-white">
                                Masuk
                            </button>
                        </form>

                        <div class="divider">atau</div>

                        <p class="text-center">
                            Belum punya akun?
                            <a href="register.php">Daftar</a>
                        </p>

                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePass() {
    const inp = document.getElementById('password');
    const icon = document.getElementById('eyeIcon');

    if (inp.type === 'password') {
        inp.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        inp.type = 'password';
        icon.className = 'bi bi-eye';
    }
}
</script>

</body>
</html>