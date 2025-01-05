<?php
// Memulai session
session_start();

include 'koneksi.php';

// Pastikan pengguna login
if (!isset($_SESSION['username'])) {
    echo "<script>
            alert('Anda harus login untuk menghapus akun!');
            window.location='login.php';
          </script>";
    exit();
}

$username = $_SESSION['username'];

// Fungsi untuk menghapus akun
function hapus_akun($username) {
    global $koneksi;

    // Hapus data pengguna dari database
    $delete_stmt = mysqli_prepare($koneksi, "DELETE FROM users WHERE username = ?");
    mysqli_stmt_bind_param($delete_stmt, "s", $username);
    mysqli_stmt_execute($delete_stmt);

    if (mysqli_stmt_affected_rows($delete_stmt) > 0) {
        echo "<script>alert('Akun berhasil dihapus!'); window.location='login.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus akun!'); window.history.back();</script>";
    }
}

// Konfirmasi penghapusan akun
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hapus_akun'])) {
    hapus_akun($username);

    // Hapus sesi setelah akun dihapus
    session_unset();
    session_destroy();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>AStore</title>
  <!--CSS-link-->
  <link rel="stylesheet" href="privasi.css">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,100..900;1,100..900&family=Parkinsans:wght@500&display=swap"
    rel="stylesheet">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css"
    integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />

  <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">
</head>
<body>
  <header class="sticky">
    <div class="logo">
      <img src="ASTORE.PNG" alt="AStore Logo">
      <h1>AStore</h1>
    </div>
    <ul class="navmenu">
      <li><a href="menu_utama.php">Menu Utama</a></li>
      <li><a href="tentang_kami.php">Tentang Kami</a></li>
      <li><a href="#"></a></li>
    </ul>

    <div class="search-bar">
      <input type="text" placeholder="SEARCH">
    </div>
    <div class="nav-icon">
      <a href="keranjang.php"><i class='bx bx-cart'></i></a>
      <a href="profile.php"><i class='bx bx-user'></i></a>
    </div>
  </header>

  <div class="profile-container">
    <div class="sidebar">
      <h3>Akun Saya</h3>
      <ul>
        <li><a href="profile.php">Profil</a></li>
        <li><a href="ubah_password.php">Ubah Password</a></li>
        <li><b><a href="privasi.php">Pengaturan Privasi</a></b></li>
        <li><a href="login.php">Logout</a></li>
      </ul>
    </div>

    <div class="profile-content">
      <h2>Privasi Akun</h2>
      <p>Minta penghapusan akun</p>
      <div class="form-container">
        <form method="POST">
          <button type="submit" name="hapus_akun" class="btn-syariah">Menghapus</button>
        </form>
      </div>
    </div>
  </div>
</body>
</html>
