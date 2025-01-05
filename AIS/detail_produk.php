<?php
session_start();
include 'koneksi.php';

// Ambil ID produk dari URL
if (!isset($_GET['id_produk']) || empty($_GET['id_produk'])) {
    header("Location: menu_utama.php");
    exit();
}

$id_produk = $_GET['id_produk'];

// Query untuk mengambil detail produk
$query = "SELECT * FROM produk WHERE id_produk = ?";
$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, "i", $id_produk);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Periksa jika produk ditemukan
if ($result && mysqli_num_rows($result) > 0) {
    $produk = mysqli_fetch_assoc($result);

    // Pisahkan ukuran berdasarkan koma
    $sizes = explode(',', $produk['ukuran']);
} else {
    echo "<script>alert('Produk tidak ditemukan'); window.location.href = 'menu_utama.php';</script>";
    exit();
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
    <link rel="stylesheet" href="detail_produk.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,100..900;1,100..900&family=Parkinsans:wght@500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
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

    <!-- Detail Produk -->
    <section id="rincian" class="section_p1">
        <div class="single-pro-image">
            <img src="uploads/<?= htmlspecialchars($produk['gambar']); ?>" 
                 alt="<?= htmlspecialchars($produk['nama_produk']); ?>" width="100%">
        </div>
        <div class="single-pro-details">
            <h6>AStore</h6>
            <h4><?= htmlspecialchars($produk['nama_produk']); ?></h4>
            <h2>Rp. <?= number_format($produk['harga'], 0, ',', '.'); ?></h2>

            <!-- Pilihan Ukuran Produk -->
            <?php if (!empty($sizes)) : ?>
                <label for="ukuran">Pilih Ukuran:</label>
                <select id="ukuran" name="ukuran" class="form-control" required>
                    <?php foreach ($sizes as $size) : ?>
                        <option value="<?= htmlspecialchars(trim($size)); ?>">
                            <?= htmlspecialchars(trim($size)); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>

            <!-- Input Jumlah Produk -->
            <label for="jumlah">Jumlah:</label>
            <input type="number" id="jumlah" name="jumlah" value="1" min="1" max="<?= $produk['stok']; ?>">
            <p>Stok Tersedia: <strong><?= $produk['stok']; ?></strong></p>
            
            <!-- Form untuk pembelian -->
            <form action="resi_detail.php" method="POST">
                <input type="hidden" name="id_produk" value="<?= $produk['id_produk']; ?>">
                <input type="hidden" name="nama_produk" value="<?= $produk['nama_produk']; ?>">
                <input type="hidden" name="harga" value="<?= $produk['harga']; ?>">
                <input type="hidden" name="stok" value="<?= $produk['stok']; ?>">
                <input type="hidden" name="gambar" value="<?= $produk['gambar']; ?>">
                
                <!-- Ambil ukuran yang dipilih -->
                <input type="hidden" name="ukuran" id="selected_size">

                <!-- Tambahkan input jumlah yang ingin dibeli -->
                <label for="jumlah">Jumlah:</label>
                <input type="number" name="jumlah" value="1" min="1" value="1" required>

                <button type="submit" onclick="setUkuran()">Beli Sekarang</button>
            </form>

            <!-- Deskripsi Produk -->
            <h4>Deskripsi Produk</h4>
            <span><?= nl2br(htmlspecialchars($produk['deskripsi'])); ?></span>
        </div>
    </section>

    <script>
        // Menyimpan ukuran yang dipilih
        function setUkuran() {
            var ukuranSelect = document.getElementById('ukuran');
            var selectedSize = ukuranSelect.options[ukuranSelect.selectedIndex].value;
            document.getElementById('selected_size').value = selectedSize;
        }
    </script>

</body>
</html>
