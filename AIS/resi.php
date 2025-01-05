<?php
session_start();
include 'koneksi.php';

// Cek apakah pengguna sudah login dan memiliki username di sesi
if (!isset($_SESSION['username'])) {
    echo "<script>alert('Anda harus login terlebih dahulu!'); window.location.href = 'login.php';</script>";
    exit;
}

// Ambil data pengguna berdasarkan username yang ada di sesi
$username = mysqli_real_escape_string($koneksi, $_SESSION['username']);
$query_user = "SELECT nama, alamat FROM users WHERE username = '$username'";
$result_user = mysqli_query($koneksi, $query_user);

// Periksa jika query berhasil dan data ditemukan
if (!$result_user || mysqli_num_rows($result_user) == 0) {
    echo "<script>alert('Data pengguna tidak ditemukan!'); window.location.href = 'profile.php';</script>";
    exit;
}

// Ambil hasil query dan simpan ke dalam array $user
$user = mysqli_fetch_assoc($result_user);

// Cek apakah data nama dan alamat ada dalam array $user
if (empty($user['nama']) || empty($user['alamat'])) {
    echo "<script>alert('Lengkapi data diri Anda, termasuk nama dan alamat!'); window.location.href = 'profile.php';</script>";
    exit;
}

// Ambil data keranjang pengguna
$query_keranjang = "
    SELECT k.nama_produk, k.jumlah, k.ukuran, p.harga, (k.jumlah * p.harga) AS subtotal
    FROM keranjang k
    JOIN produk p ON k.nama_produk = p.nama_produk
    WHERE k.username = '$username'
";

$result_keranjang = mysqli_query($koneksi, $query_keranjang);

// Jika keranjang kosong, tampilkan pesan
if (!$result_keranjang || mysqli_num_rows($result_keranjang) === 0) {
    die('<p>Keranjang Anda kosong. Tidak ada resi yang dapat dibuat.</p>');
}

// Inisialisasi total harga
$total_harga = 0;

// Membuat ID Resi unik menggunakan timestamp
$id_resi = "RESI" . strtoupper(substr(md5(time()), 0, 8));

// Proses setiap item di keranjang
while ($row = mysqli_fetch_assoc($result_keranjang)) {
    $nama_produk = $row['nama_produk'];
    $ukuran = $row['ukuran'];
    $jumlah = $row['jumlah'];
    $harga = $row['harga'];
    $subtotal = $row['subtotal'];

    // Tambahkan subtotal ke total harga
    $total_harga += $subtotal;

    // Cek stok produk
    $query_produk = mysqli_query($koneksi, "SELECT stok FROM produk WHERE nama_produk = '$nama_produk'");
    $produk = mysqli_fetch_assoc($query_produk);

    if ($produk && $produk['stok'] >= $jumlah) {
        // Simpan data resi ke database
        $alamat = mysqli_real_escape_string($koneksi, $user['alamat']);
        $query_insert_resi = "INSERT INTO resi_pembelian (id_resi, nama_produk, ukuran, jumlah, harga, total_harga, username, alamat) 
                              VALUES ('$id_resi', '$nama_produk', '$ukuran', '$jumlah', '$harga', '$subtotal', '$username', '$alamat')";

        if (mysqli_query($koneksi, $query_insert_resi)) {
            // Update stok produk setelah pembelian berhasil
            $new_stok = $produk['stok'] - $jumlah;
            $query_update_stok = "UPDATE produk SET stok = '$new_stok' WHERE nama_produk = '$nama_produk'";
            mysqli_query($koneksi, $query_update_stok);
        } else {
            echo "<script>alert('Terjadi kesalahan saat menyimpan data resi!'); window.location.href = 'menu_utama.php';</script>";
            exit;
        }
    } else {
        echo "<script>alert('Stok tidak cukup untuk pembelian produk: $nama_produk!'); window.location.href = 'menu_utama.php';</script>";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>AStore - Resi</title>
  <link rel="stylesheet" href="resi.css">
  <style>
    .button-finish {
      display: block;
      margin: 20px auto;
      padding: 10px 20px;
      background-color: #4CAF50;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }

    .button-finish:hover {
      background-color: #45a049;
    }
  </style>
</head>
<body>
  <div class="invoice">
    <div class="invoice-header">
      <img src="ASTORE.png" alt="Logo">
      <h2>AStore</h2>
    </div>
    <hr />
    <div class="invoice-body">
      <label for="id_resi">ID Resi:</label>
      <p><?php echo htmlspecialchars($id_resi); ?></p> <!-- Menampilkan ID Resi -->

      <label for="name">Nama Pelanggan:</label>
      <p><?php echo htmlspecialchars($user['nama']); ?></p>

      <label for="alamat">Alamat:</label>
      <p><?php echo htmlspecialchars($user['alamat']); ?></p>

      <label for="rincian_harga">Rincian Pembelian:</label>
      <table border="1" cellpadding="10" cellspacing="0">
        <thead>
          <tr>
            <th>Nama Produk</th>
            <th>Ukuran</th>
            <th>Jumlah</th>
            <th>Harga per Unit</th>
            <th>Subtotal</th>
          </tr>
        </thead>
        <tbody>
          <?php
          mysqli_data_seek($result_keranjang, 0); // Reset pointer hasil query
          while ($row = mysqli_fetch_assoc($result_keranjang)): ?>
            <tr>
              <td><?php echo htmlspecialchars($row['nama_produk']); ?></td>
              <td><?php echo htmlspecialchars($row['ukuran']); ?></td>
              <td><?php echo $row['jumlah']; ?></td>
              <td>Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></td>
              <td>Rp <?php echo number_format($row['subtotal'], 0, ',', '.'); ?></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>

      <label for="total_harga">Total Harga:</label>
      <p>Rp <?php echo number_format($total_harga, 0, ',', '.'); ?></p>
    </div>
    <hr />
    <div class="footer">
      <p>Resi ini berfungsi sebagai bukti bahwa pembayaran Anda telah berhasil.</p>
      <p>Terima kasih telah berbelanja di AStore!</p>
      <button class="button-finish" onclick="showMessage()">Selesai</button>
    </div>
  </div>

  <script>
    function showMessage() {
        alert("Demo Selesai");
        window.location.href = 'menu_utama.php';
    }
  </script>
</body>
</html>
