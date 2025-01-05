<?php
// Memulai session
session_start();

// Menyambungkan ke dalam database
include 'koneksi.php';

// Pastikan pengguna sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Menggunakan username dari session
$username = $_SESSION['username'];

// Proses pembayaran dan pembuatan resi pembelian
if (isset($_POST['bayar'])) {
    if (isset($_POST['selected_items'])) {
        // Ambil item yang dipilih
        $selectedItems = $_POST['selected_items'];
        $totalBayar = 0;

        // Buat ID resi acak
        $idResi = "RES" . strtoupper(bin2hex(random_bytes(4)));
        
        foreach ($selectedItems as $itemId) {
            // Ambil data item dari keranjang
            $queryItem = mysqli_query($koneksi, "SELECT * FROM keranjang WHERE id_produk = '$itemId' AND username = '$username'");
            $item = mysqli_fetch_assoc($queryItem);

            if ($item) {
                $jumlah = $item['jumlah'];
                $harga = $item['harga'];
                $totalHargaItem = $jumlah * $harga;
                $totalBayar += $totalHargaItem;

                // Masukkan item ke tabel resi_pembelian
                mysqli_query($koneksi, "INSERT INTO resi_pembelian (id_resi, username, id_produk, jumlah, total_harga) VALUES ('$idResi', '$username', '{$item['id_produk']}', '$jumlah', '$totalHargaItem')");

                // Hapus item dari keranjang
                mysqli_query($koneksi, "DELETE FROM keranjang WHERE id_produk = '{$item['id_produk']}' AND username = '$username'");
            }
        }

        // Redirect ke halaman resi dengan ID resi
        echo "<script>
            alert('Pembayaran berhasil! ID Resi Anda: $idResi');
            window.location.href = 'resi.php?id_resi=$idResi';
        </script>";
    } else {
        echo "<script>
            alert('Silakan pilih produk yang ingin dibayar.');
            window.location.href = 'keranjang.php';
        </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AStore</title>
    <!-- CSS link -->
    <link rel="stylesheet" href="keranjang.css">
</head>
<body>
    <div class="shopping-cart">
        <h1>Keranjang Anda</h1>
        <form method="POST" action="">
            <?php if (count($cartItems) > 0): ?>
                <form method="POST" action="">
                    <label>
                        <input type="checkbox" id="select_all" onchange="selectAllItems(this)">
                        Pilih Semua
                    </label>
                    <?php foreach ($cartItems as $item): ?>
                        <div class="cart-item">
                            <img src="uploads/<?php echo $item['gambar']; ?>" alt="<?php echo $item['nama_produk']; ?>">
                            <div class="item-details">
                                <h2><?php echo $item['nama_produk']; ?></h2>
                                <p>Harga per unit: Rp <?php echo number_format($item['harga'], 0, ',', '.'); ?></p>
                                <label for="jumlah_<?php echo $item['id_produk']; ?>">Jumlah:</label>
                                <input 
                                    type="number" 
                                    id="jumlah_<?php echo $item['id_produk']; ?>" 
                                    name="jumlah[<?php echo $item['id_produk']; ?>]" 
                                    value="<?php echo $item['jumlah']; ?>" 
                                    min="1" 
                                    max="<?php echo $item['stok_tersedia']; ?>" 
                                    onchange="updateHarga(<?php echo $item['id_produk']; ?>, <?php echo $item['harga']; ?>)"
                                />
                            </div>
                            <div class="price" id="total_harga_<?php echo $item['id_produk']; ?>" data-harga-per-unit="<?php echo $item['harga']; ?>">
                                Rp <?php echo number_format($item['harga'] * $item['jumlah'], 0, ',', '.'); ?>
                            </div>
                            <input type="checkbox" name="selected_items[]" value="<?php echo $item['id_produk']; ?>" class="select-item" onchange="updateSubtotal()">
                        </div>
                    <?php endforeach; ?>
                    <div class="summary">
                        <p class="subtotal">Total: <span class="price" id="total_semua">Rp 0</span></p>
                        <button type="submit" class="proceed-to-buy" name="bayar">Bayar</button>
                    </div>
                </form>
            <?php else: ?>
                <p>Keranjang Anda kosong.</p>
            <?php endif; ?>
        </form>
    </div>

    <script>
        function selectAllItems(selectAllCheckbox) {
            const allCheckboxes = document.querySelectorAll('.select-item');
            allCheckboxes.forEach(function(checkbox) {
                checkbox.checked = selectAllCheckbox.checked;
            });
            updateSubtotal();
        }

        function updateSubtotal() {
            let totalKeseluruhan = 0;
            document.querySelectorAll('.select-item:checked').forEach(function(checkbox) {
                const itemId = checkbox.value;
                const hargaPerUnit = parseInt(document.querySelector('#total_harga_' + itemId).dataset.hargaPerUnit);
                const jumlah = parseInt(document.querySelector('input[name="jumlah[' + itemId + ']"').value);
                totalKeseluruhan += hargaPerUnit * jumlah;
            });
            document.getElementById('total_semua').innerText = 'Rp ' + totalKeseluruhan.toLocaleString('id-ID');
        }
    </script>
</body>
</html>
