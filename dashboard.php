<?php
require_once 'database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
</head>
<body>

<h1>Dashboard Muslimah Shop</h1>

<p>Selamat datang,
<b><?php echo $_SESSION['nama']; ?></b>
</p>

<p>Role:
<b><?php echo $_SESSION['role']; ?></b>
</p>

<a href="logout.php">Logout</a>

</body>
</html>