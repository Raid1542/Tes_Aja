<?php
require('FPDF-master/fpdf.php');
include 'koneksi.php';

class PDF extends FPDF
{
    function Header()
    {
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 10, 'Kelola Pesanan', 0, 1, 'C');
        $this->Ln(10);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Halaman ' . $this->PageNo(), 0, 0, 'C');
    }

    function Table($data)
    {
        // Set header table
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(30, 10, 'ID Resi', 1, 0, 'C');
        $this->Cell(40, 10, 'Nama Pembeli', 1, 0, 'C');
        $this->Cell(40, 10, 'Nama Produk', 1, 0, 'C');
        $this->Cell(30, 10, 'Ukuran', 1, 0, 'C');
        $this->Cell(30, 10, 'Jumlah', 1, 0, 'C');
        $this->Cell(30, 10, 'Harga', 1, 0, 'C');
        $this->Cell(30, 10, 'Total Harga', 1, 0, 'C');
        $this->Cell(30, 10, 'Waktu', 1, 1, 'C');

        // Set body table
        $this->SetFont('Arial', '', 12);
        foreach ($data as $row) {
            $this->Cell(30, 10, $row['id_resi'], 1, 0, 'C');
            $this->Cell(40, 10, $row['username'], 1, 0, 'C');
            $this->Cell(40, 10, $row['nama_produk'], 1, 0, 'C');
            $this->Cell(30, 10, $row['ukuran'], 1, 0, 'C');
            $this->Cell(30, 10, $row['jumlah'], 1, 0, 'C');
            $this->Cell(30, 10, $row['harga'], 1, 0, 'C');
            $this->Cell(30, 10, $row['total_harga'], 1, 0, 'C');
            $this->Cell(30, 10, $row['waktu'], 1, 1, 'C');
        }
    }
}

$pdf = new PDF();
$pdf->AddPage();

// Query untuk mengambil data
$query = mysqli_query($koneksi, "SELECT * FROM resi_pembelian");
$data = [];
while ($row = mysqli_fetch_assoc($query)) {
    $data[] = $row; // Menyimpan data dalam array
}

// Menambahkan data ke dalam PDF dalam bentuk tabel
$pdf->Table($data);

// Menyimpan atau menampilkan PDF
$pdf->Output('I', 'kelola_pesanan.pdf');
exit;
?>
