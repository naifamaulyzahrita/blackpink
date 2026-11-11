<?php
require_once __DIR__ . "/../connection.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $album_type = mysqli_real_escape_string($conn, $_POST['album_type']);

    if (empty($album_type)) {
        echo "<script>alert('Jenis album tidak boleh kosong!'); history.back();</script>";
        exit;
    }

    $sql = "INSERT INTO albums_type (album_type) VALUES ('$album_type')";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Data jenis album berhasil ditambahkan!'); window.location.href='view.php';</script>";
        exit;
    } else {
        echo "<script>alert('Gagal menambahkan data: " . mysqli_error($conn) . "'); history.back();</script>";
        exit;
    }
}
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Jenis Album</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        form { max-width: 400px; }
        label { display: block; margin-top: 10px; font-weight: bold; }
        input[type="text"] { width: 100%; padding: 8px; margin-top: 5px; box-sizing: border-box; }
        button { margin-top: 15px; padding: 10px 15px; background-color: #007bff; color: white; border: none; cursor: pointer; }
        .back-link { display: inline-block; margin-top: 15px; text-decoration: none; color: #007bff; }
    </style>
</head>
<body>

    <h2>Tambah Jenis Album Baru</h2>
    
    <form action="insert.php" method="POST">
        <label for="album_type">Nama Jenis Album:</label>
        <input type="text" id="album_type" name="album_type" placeholder="Contoh: Studio Album, EP, Single Album" required>
        
        <button type="submit">Simpan Data</button>
    </form>

    <a href="view.php" class="back-link">← Kembali ke Daftar Data</a>

</body>
</html>