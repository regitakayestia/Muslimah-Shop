# Muslimah-Shop
Website E-Commerce Muslimah berbasis PHP Native dengan pendekatan Object-Oriented Programming (OOP). Project ini dibuat sebagai tugas UAS mata kuliah Pemrograman Berorientasi Objek.
Muslimah Shop menyediakan platform belanja online untuk busana muslimah seperti hijab, gamis, aksesoris, dan tas, dengan tampilan modern dan responsif menggunakan Bootstrap.

##fitur
-halaman beranda
  Hero banner, statistik produk & member, navigasi utama
-login
  Autentikasi user dengan email & password (hash)
-register
  Pendaftaran akun user baru
-Katalog produk
  Filter berdasarkan kategori (Hijab, Gamis, Aksesoris, Tas)
-keranjang
  Tambah, ubah jumlah, dan hapus item
-dashboar user
  Halaman ringkasan setelah login
-logut
  Mengakhiri session pengguna

## Teknologi yang digunakan
1. PHP Native: Bahasa pemrograman utama (OOP)
2. MySQL: Database
3. Bootstrap: Framework CSS untuk tampilan
4. XAMPP: Local web server

## Struktur folder
Muslimah/
├── index.php          # Halaman beranda
├── produk.php         # Katalog produk + filter kategori
├── keranjang.php      # Keranjang belanja + checkout
├── login.php          # Form login
├── register.php       # Form registrasi
├── logout.php         # Hapus session
├── dashboard.php      # Dashboard user
├── database.php       # Class koneksi database (OOP)
├── database11.php     # Koneksi alternatif
├── database.sql       # Script SQL (struktur tabel + data dummy)
└── README.md 

## Konsep OOP yang diterapkan
1. Class
  Database, User, Product, Cart
2. Object
  new User(), new Product(), new Cart()
3. Constructor
  __construct() di setiap class
4. Property
  $host, $conn, $email, $password, $items
5. Method
  register(), login(), getAllProducts(), addItem(), getTotal()
6. Inheritance
  User extends Database, Product extends Database
7. Encapsulation
  Property private untuk melindungi data

## cara menjalankan
1. Import database: Buka phpMyAdmin, import file database.sql
2. Jalankan xampp: Aktifkan Apache dan MySQL
3. Letakkan folder di xampp/htdocs/muslimah
4. Akses localhost:8080/muslimah

## Akun demo
  -Role: Admin, email: admin@shop.com, Password: password
  -Role: User, Email: user@shop.com, Password: password

## Lisensi
Project ini dibuat untuk keperluan akademik (tugas UAS) dan bebas digunakan sebagai referensi pembelajaran.

