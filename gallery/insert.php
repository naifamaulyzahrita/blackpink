<?php
require_once __DIR__ . "/../connection.php";
$albums_query = mysqli_query($conn, "SELECT album_id, title FROM albums ORDER BY title ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $album_id = !empty($_POST['album_id']) ? (int) $_POST['album_id'] : null;
    
    // Validasi Upload Foto
    $fotoOk = false;
    $ext = '';
    $foto_name = '';
    if (isset($_FILES['file_path']) && $_FILES['file_path']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['file_path']['name'], PATHINFO_EXTENSION));
        if (getimagesize($_FILES['file_path']['tmp_name']) !== false && in_array($ext, ['jpg','jpeg','png','gif']) && $_FILES['file_path']['size'] <= 2000000) {
            $fotoOk = true;
            $foto_name = uniqid('gallery_') . '.' . $ext;
        }
    }
    if (!$fotoOk) {
        echo "<script>alert('Foto tidak valid. Gunakan JPG/PNG/GIF, maks 2 MB.'); history.back();</script>";
        exit;
    }

    $album_sql = $album_id ? $album_id : "NULL";
    $sql = "INSERT INTO gallery (title, file_path, media_type, album_id) VALUES ('$title', 'assets/img/$foto_name', 'image', $album_sql)";

    if (mysqli_query($conn, $sql)) {
        $dir = __DIR__ . "/../assets/img/";
        if (!is_dir($dir)) mkdir($dir, 0777, true);
        move_uploaded_file($_FILES['file_path']['tmp_name'], $dir . $foto_name);
        echo "<script>alert('Foto berhasil diupload!'); window.location.href='view.php';</script>";
        exit;
    } else {
        echo "<script>alert('Gagal: " . mysqli_error($conn) . "'); history.back();</script>";
        exit;
    }
}
mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Foto Galeri</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        form { max-width: 400px; }
        label { display: block; margin-top: 10px; font-weight: bold; }
        input[type="text"], select, input[type="file"] { width: 100%; padding: 8px; margin-top: 5px; box-sizing: border-box; }
        button { margin-top: 15px; padding: 10px 15px; background-color: #007bff; color: white; border: none; cursor: pointer; }
        .back-link { display: inline-block; margin-top: 15px; text-decoration: none; color: #007bff; }
    </style>
</head>
<body>
    <h2>Upload Foto Galeri</h2>
    <form action="insert.php" method="POST" enctype="multipart/form-data">
        <label for="title">Judul Foto:</label>
        <input type="text" id="title" name="title" placeholder="Contoh: BLACKPINK - JUMP" required>
        
        <label for="album_id">Album Terkait (Opsional):</label>
        <select id="album_id" name="album_id">
            <option value="">-- Tidak ada (General) --</option>
            <?php while ($a = mysqli_fetch_assoc($albums_query)): ?>
                <option value="<?php echo $a['album_id']; ?>"><?php echo htmlspecialchars($a['title']); ?></option>
            <?php endwhile; ?>
        </select>

        <label for="file_path">File Foto:</label>
        <input type="file" id="file_path" name="file_path" accept="image/*" required>
        
        <button type="submit">Upload</button>
    </form>
    <a href="view.php" class="back-link">← Kembali</a>
</body>
</html>