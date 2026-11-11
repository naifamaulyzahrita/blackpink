<?php
require_once __DIR__ . "/../connection.php";

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) { header("Location: view.php"); exit; }

$albums_query = mysqli_query($conn, "SELECT album_id, title FROM albums ORDER BY title ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $album_id = !empty($_POST['album_id']) ? (int) $_POST['album_id'] : "NULL";

    $fotoOk = false;
    $foto_name = '';
    if (isset($_FILES['file_path']) && $_FILES['file_path']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['file_path']['name'], PATHINFO_EXTENSION));
        if (getimagesize($_FILES['file_path']['tmp_name']) !== false && in_array($ext, ['jpg','jpeg','png','gif']) && $_FILES['file_path']['size'] <= 2000000) {
            $fotoOk = true;
            $foto_name = uniqid('gallery_') . '.' . $ext;
        }
    }

    if (!$fotoOk && isset($_FILES['file_path']) && $_FILES['file_path']['error'] !== UPLOAD_ERR_NO_FILE) {
        echo "<script>alert('Foto tidak valid.'); history.back();</script>";
        exit;
    }

    $path_sql = $fotoOk ? "'media/" . $foto_name . "'" : "file_path";
    $album_sql = is_int($album_id) ? $album_id : "NULL";

    $sql = "UPDATE gallery SET title='$title', file_path=$path_sql, album_id=$album_sql WHERE media_id=$id";

    if (mysqli_query($conn, $sql)) {
        if ($fotoOk) {
            $dir = __DIR__ . "/../media/";
            if (!is_dir($dir)) mkdir($dir, 0777, true);
            move_uploaded_file($_FILES['file_path']['tmp_name'], $dir . $foto_name);
        }
        echo "<script>alert('Foto berhasil diupdate!'); window.location.href='view.php';</script>";
    } else {
        echo "<script>alert('Gagal: " . mysqli_error($conn) . "'); history.back();</script>";
    }
    exit;
}

$row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM gallery WHERE media_id = $id"));
if (!$row) { header("Location: view.php"); exit; }
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Galeri</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        form { max-width: 400px; }
        label { display: block; margin-top: 10px; font-weight: bold; }
        input[type="text"], select, input[type="file"] { width: 100%; padding: 8px; margin-top: 5px; box-sizing: border-box; }
        button { margin-top: 15px; padding: 10px 15px; background-color: #ffc107; color: black; border: none; cursor: pointer; font-weight: bold; }
        .back-link { display: inline-block; margin-top: 15px; text-decoration: none; color: #007bff; }
        .current-photo img { width: 80px; height: 80px; object-fit: cover; margin-top: 5px; border-radius: 4px; }
    </style>
</head>
<body>
    <h2>Edit Foto Galeri</h2>
    <form action="edit.php?id=<?php echo $id; ?>" method="POST" enctype="multipart/form-data">
        <label for="title">Judul Foto:</label>
        <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($row['title'] ?? ''); ?>" required>
        
        <label for="album_id">Album Terkait:</label>
        <select id="album_id" name="album_id">
            <option value="">-- Tidak ada (General) --</option>
            <?php while ($a = mysqli_fetch_assoc($albums_query)): ?>
                <option value="<?php echo $a['album_id']; ?>" <?php echo ($a['album_id'] == $row['album_id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($a['title']); ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label>Foto Saat Ini:</label>
        <div class="current-photo">
            <img src="../<?php echo htmlspecialchars($row['file_path'] ?? ''); ?>" alt="Current">
        </div>

        <label for="file_path">Ganti Foto (Opsional):</label>
        <input type="file" id="file_path" name="file_path" accept="image/*">
        
        <button type="submit">Update</button>
    </form>
    <a href="view.php"