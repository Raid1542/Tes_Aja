<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Kelola_Pesanan.xls");
include 'koneksi.php';
echo "<table border='1'>";
echo "<thead>
<tr>
<th>ID Resi</th>
<th>Nama Pembeli</th>
<th>Alamat</th>
<th>Nama Produk</th>
<th>Ukuran</th>
<th>Jumlah</th>
<th>Harga</th>
<th>Total Harga</th>
<th>Tanggal Pembelian</th>
</tr>
</thead>";
$query = mysqli_query($koneksi, "SELECT * FROM resi_pembelian");
while ($row = mysqli_fetch_assoc($query)) {
    echo "<tr>
    <td>{$row['id_resi']}</td>
    <td>{$row['username']}</td>
    <td>{$row['alamat']}</td>
    <td>{$row['nama_produk']}</td>
    <td>{$row['ukuran']}</td>
    <td>{$row['jumlah']}</td>
    <td>{$row['harga']}</td>
    <td>{$row['total_harga']}</td>
    <td>{$row['waktu']}</td>
    </tr>";
}
echo "</table>";
?>
