<?php
session_start();
include 'koneksi.php';

// Periksa apakah email disimpan di sesi
if (!isset($_SESSION['email_reset'])) {
    header("Location: resetsandi.php");
    exit();
}

$email = $_SESSION['email_reset'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPassword = $_POST['newPassword'];
    $confirmPassword = $_POST['confirmPassword'];

    // Validasi password
    if ($newPassword !== $confirmPassword) {
        $error = "Password dan konfirmasi password tidak cocok.";
    } elseif (strlen($newPassword) < 6) {
        $error = "Password harus memiliki minimal 6 karakter.";
    } else {
        // Perbarui password di database
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $koneksi->prepare("UPDATE users SET password = ? WHERE email = ?");
        if (!$stmt) {
            die("Query gagal: " . $koneksi->error);
        }

        $stmt->bind_param("ss", $hashedPassword, $email);
        if ($stmt->execute()) {
            echo "<script>
            alert('password berhasil diperbarui');
            window.location.href = 'menu_utama.php';
            </script>";
        } else {
            $error = "Gagal memperbarui password.";
        }

        $stmt->close();
    }
}

$koneksi->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sandi Baru</title>
    <link rel="stylesheet" href="sandibaru.css">
</head>
<body>
    <div class="wrapper">
        <span class="bg-animate"></span>
        <h2>Masukkan Sandi Baru</h2>
        <div class="form-box login">
            <form id="passwordForm" method="POST" action="">
                <div class="input-box">
                    <input type="password" id="newPassword" name="newPassword" placeholder="Masukkan Password Baru"
                        required>
                    <input type="password" id="confirmPassword" name="confirmPassword"
                        placeholder="Konfirmasi Password Baru" required>
                    <button type="submit" class="btn">Buat Sandi Baru</button>
                    <?php if (isset($error)): ?>
                        <p class="message" style="color: #f44336;"><?= $error ?></p>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>
</body>
</html>