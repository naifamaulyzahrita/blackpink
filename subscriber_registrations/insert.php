<?php
require_once __DIR__ . "/../connection.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email     = mysqli_real_escape_string($conn, $_POST['email']);
    $full_name = !empty($_POST['full_name']) ? "'" . mysqli_real_escape_string($conn, $_POST['full_name']) . "'" : "NULL";

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Email tidak valid atau kosong!'); history.back();</script>";
        exit;
    }

    $sql = "INSERT INTO subscriber_registrations (email, full_name) VALUES ('$email', $full_name)";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Berhasil mendaftar sebagai subscriber!'); window.location.href='view.php';</script>";
        exit;
    } else {
        // Jika email sudah ada (karena UNIQUE constraint)
        echo "<script>alert('Gagal: Email mungkin sudah terdaftar. (" . mysqli_error($conn) . ")'); history.back();</script>";
        exit;
    }
}
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Subscriber</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        form { max-width: 400px; }
        label { display: block; margin-top: 10px; font-weight: bold; }
        input[type="email"], input[type="text"] { width: 100%; padding: 8px; margin-top: 5px; box-sizing: border-box; }
        button { margin-top: 15px; padding: 10px 15px; background-color: #007bff; color: white; border: none; cursor: pointer; }
        .back-link { display: inline-block; margin-top: 15px; text-decoration: none; color: #007bff; }
    </style>
</head>
<body>
    <h2>Daftar Tour Registry</h2>
    <form action="insert.php" method="POST">
        <label for="email">Alamat Email:</label>
        <input type="email" id="email" name="email" placeholder="blink@blackpink.com" required>

        <label for="full_name">Nama Lengkap (Opsional):</label>
        <input type="text" id="full_name" name="full_name" placeholder="Nama Anda">

        <button type="submit">Daftar Sekarang</button>
    </form>
    <a href="view.php" class="back-link">← Kembali</a>
</body>
</html>