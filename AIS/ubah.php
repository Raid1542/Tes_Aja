<?php
session_start();
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_produk = $_POST['id_produk'];
    $nama_produk = mysqli_real_escape_string($koneksi, $_POST['nama_produk']);
    $ukuran = mysqli_real_escape_string($koneksi, $_POST['ukuran']);
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];

    // Query default
    $query = "UPDATE produk SET nama_produk='$nama_produk', ukuran='$ukuran', deskripsi='$deskripsi', harga='$harga', stok='$stok' WHERE id_produk='$id_produk'";

    if (!empty($_FILES['gambar']['name'])) {
        $gambar = basename($_FILES['gambar']['name']);
        $upload_dir = "uploads/";
        $upload_path = $upload_dir . $gambar;

        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $upload_path)) {
            $query = "UPDATE produk SET nama_produk='$nama_produk', ukuran='$ukuran', deskripsi='$deskripsi', harga='$harga', stok='$stok', gambar='$gambar' WHERE id_produk='$id_produk'";
        } else {
            $_SESSION['message'] = "Gagal mengunggah gambar.";
            $_SESSION['type'] = "danger";
            header('Location: produk.php');
            exit();
        }
    }

    if (mysqli_query($koneksi, $query)) {
        $_SESSION['message'] = "Produk berhasil diperbarui!";
        $_SESSION['type'] = "success";
    } else {
        $_SESSION['message'] = "Gagal memperbarui produk: " . mysqli_error($koneksi);
        $_SESSION['type'] = "danger";
    }
    header('Location: produk.php');
    exit();
}
?>
