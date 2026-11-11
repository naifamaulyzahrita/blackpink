<?php
require_once __DIR__ . "/../connection.php";

// 1. Ambil ID dari URL
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    header("Location: view.php");
    exit;
}

// 2. Proses Update jika method POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $album_type = mysqli_real_escape_string($conn, $_POST['album_type']);
    
    $sql = "UPDATE albums_type SET album_type = '$album_type' WHERE album_type_id = $id";
    
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Data berhasil diupdate!'); window.location.href='view.php';</script>";
    } else {
        echo "<script>alert('Gagal update: " . mysqli_error($conn) . "'); history.back();</script>";
    }
    exit;
}

// 3. Ambil data lama untuk ditampilkan di form
$result = mysqli_query($conn, "SELECT * FROM albums_type WHERE album_type_id = $id");
$row = mysqli_fetch_assoc($result);

if (!$row) {
    header("Location: view.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Jenis Album</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        form { max-width: 400px; }
        label { display: block; margin-top: 10px; font-weight: bold; }
        input[type="text"] { width: 100%; padding: 8px; margin-top: 5px; box-sizing: border-box; }
        button { margin-top: 15px; padding: 10px 15px; background-color: #ffc107; color: black; border: none; cursor: pointer; font-weight: bold; }
        .back-link { display: inline-block; margin-top: 15px; text-decoration: none; color: #007bff; }
    </style>
</head>
<body>
    <h2>Edit Jenis Album</h2>
    <form action="edit.php?id=<?php echo $id; ?>" method="POST">
        <label for="album_type">Nama Jenis Album:</label>
        <input type="text" id="album_type" name="album_type" value="<?php echo htmlspecialchars($row['album_type']); ?>" required>
        
        <button type="submit">Update Data</button>
    </form>
    <a href="view.php" class="back-link">← Kembali ke Daftar Data</a>
</body>
</html>