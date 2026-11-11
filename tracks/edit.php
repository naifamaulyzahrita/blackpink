<?php
require_once __DIR__ . "/../connection.php";

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) { header("Location: view.php"); exit; }

// Ambil data album untuk dropdown
$albums_query = mysqli_query($conn, "SELECT album_id, title FROM albums ORDER BY title ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $album_id     = (int) $_POST['album_id'];
    $track_number = (int) $_POST['track_number'];
    $title        = mysqli_real_escape_string($conn, $_POST['title']);
    $is_top_track = isset($_POST['is_top_track']) ? 1 : 0;

    // Logika Upload Audio (Opsional saat edit)
    $audioOk = false;
    $ext = '';
    $audio_name = '';

    if (isset($_FILES['audio']) && $_FILES['audio']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['audio']['name'], PATHINFO_EXTENSION));
        $allowed = ['mp3', 'wav', 'ogg', 'm4a'];
        
        if (in_array($ext, $allowed) && $_FILES['audio']['size'] <= 10000000) { // Maksimal 10MB
            $audioOk = true;
            $audio_name = uniqid('track_') . '.' . $ext;
        }
    }

    if (!$audioOk && isset($_FILES['audio']) && $_FILES['audio']['error'] !== UPLOAD_ERR_NO_FILE) {
        echo "<script>alert('File audio tidak valid. Gunakan MP3/WAV/OGG/M4A, maksimal 10 MB.'); history.back();</script>";
        exit;
    }

    // Tentukan path audio untuk query UPDATE
    if ($audioOk) {
        $audio_path_sql = "'media/" . $audio_name . "'";
    } else {
        // Jika tidak ada audio baru, biarkan kolom audio_path tetap seperti semula
        $audio_path_sql = "audio_path"; 
    }

    $sql = "UPDATE tracks SET 
            album_id = $album_id, 
            track_number = $track_number, 
            title = '$title', 
            audio_path = $audio_path_sql, 
            is_top_track = $is_top_track 
            WHERE track_id = $id";

    if (mysqli_query($conn, $sql)) {
        if ($audioOk) {
            $dir = __DIR__ . "/../media/";
            if (!is_dir($dir)) mkdir($dir, 0777, true);
            move_uploaded_file($_FILES['audio']['tmp_name'], $dir . $audio_name);
        }
        echo "<script>alert('Data lagu berhasil diupdate!'); window.location.href='view.php';</script>";
    } else {
        echo "<script>alert('Gagal update: " . mysqli_error($conn) . "'); history.back();</script>";
    }
    exit;
}

// Ambil data lama
$row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tracks WHERE track_id = $id"));
if (!$row) { header("Location: view.php"); exit; }
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Lagu</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        form { max-width: 500px; }
        label { display: block; margin-top: 10px; font-weight: bold; }
        input[type="text"], input[type="number"], select { width: 100%; padding: 8px; margin-top: 5px; box-sizing: border-box; }
        button { margin-top: 15px; padding: 10px 15px; background-color: #ffc107; color: black; border: none; cursor: pointer; font-weight: bold; }
        .back-link { display: inline-block; margin-top: 15px; text-decoration: none; color: #007bff; }
        .checkbox-label { display: inline-block; margin-top: 10px; font-weight: bold; }
    </style>
</head>
<body>
    <h2>Edit Data Lagu</h2>
    <form action="edit.php?id=<?php echo $id; ?>" method="POST" enctype="multipart/form-data">
        <label for="album_id">Pilih Album:</label>
        <select id="album_id" name="album_id" required>
            <option value="">-- Pilih Album --</option>
            <?php while ($album = mysqli_fetch_assoc($albums_query)): ?>
                <option value="<?php echo $album['album_id']; ?>" <?php echo ($album['album_id'] == $row['album_id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($album['title']); ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label for="track_number">Nomor Track:</label>
        <input type="number" id="track_number" name="track_number" value="<?php echo htmlspecialchars($row['track_number'] ?? ''); ?>" required min="1">
        
        <label for="title">Judul Lagu:</label>
        <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($row['title'] ?? ''); ?>" required>

        <label>File Audio Saat Ini:</label>
        <div>
            <?php if (!empty($row['audio_path'])): ?>
                <em><?php echo htmlspecialchars($row['audio_path']); ?></em>
            <?php else: ?>
                <em>Tidak ada file audio</em>
            <?php endif; ?>
        </div>

        <label for="audio">Ganti File Audio (Opsional):</label>
        <input type="file" id="audio" name="audio" accept="audio/*">

        <label class="checkbox-label">
            <input type="checkbox" name="is_top_track" value="1" <?php echo ($row['is_top_track'] == 1) ? 'checked' : ''; ?>>
            Lagu Unggulan (Top Track)
        </label>
        
        <button type="submit">Update Data</button>
    </form>
    <a href="view.php" class="back-link">← Kembali ke Daftar Data</a>
</body>
</html>