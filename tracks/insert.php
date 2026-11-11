<?php
require_once __DIR__ . "/../connection.php";

// Ambil data albums untuk dropdown
$albums_query = mysqli_query($conn, "SELECT album_id, title FROM albums ORDER BY title ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Bersihkan input
    $album_id     = (int) $_POST['album_id'];
    $track_number = (int) $_POST['track_number'];
    $title        = mysqli_real_escape_string($conn, $_POST['title']);
    $is_top_track = isset($_POST['is_top_track']) ? 1 : 0; // Checkbox

    // 2. Validasi Input Wajib
    if ($album_id <= 0 || $track_number <= 0 || empty($title)) {
        echo "<script>alert('Album, Nomor Track, dan Judul wajib diisi!'); history.back();</script>";
        exit;
    }

    // 3. Validasi Upload Audio (Opsional)
    $audioOk = false;
    $audio_name = '';

    if (isset($_FILES['audio']) && $_FILES['audio']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['audio']['name'], PATHINFO_EXTENSION));
        $allowed = ['mp3', 'wav', 'ogg'];
        
        // Cek ekstensi dan ukuran (maks 5MB)
        if (in_array($ext, $allowed) && $_FILES['audio']['size'] <= 5000000) {
            $audioOk = true;
            $audio_name = uniqid('track_') . '.' . $ext;
        }
    }

    if (!$audioOk && isset($_FILES['audio']) && $_FILES['audio']['error'] !== UPLOAD_ERR_NO_FILE) {
        echo "<script>alert('File audio tidak valid. Gunakan MP3/WAV, maksimal 5 MB.'); history.back();</script>";
        exit;
    }

    // Tentukan path audio (NULL jika tidak ada)
    $audio_path_sql = $audioOk ? "'assets/audio/" . $audio_name . "'" : "NULL";

    // 4. Query INSERT
    $sql = "INSERT INTO tracks (album_id, track_number, title, audio_path, is_top_track) 
            VALUES ($album_id, $track_number, '$title', $audio_path_sql, $is_top_track)";

    // 5. Eksekusi
    if (mysqli_query($conn, $sql)) {
        // Pindahkan file audio jika ada
        if ($audioOk) {
            $dir = __DIR__ . "/../assets/audio/";
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }
            move_uploaded_file($_FILES['audio']['tmp_name'], $dir . $audio_name);
        }
        
        echo "<script>alert('Lagu berhasil ditambahkan!'); window.location.href='view.php';</script>";
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
    <title>Tambah Lagu</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        form { max-width: 500px; }
        label { display: block; margin-top: 10px; font-weight: bold; }
        input[type="text"], input[type="number"], select { width: 100%; padding: 8px; margin-top: 5px; box-sizing: border-box; }
        .checkbox-group { margin-top: 10px; }
        button { margin-top: 15px; padding: 10px 15px; background-color: #007bff; color: white; border: none; cursor: pointer; }
        .back-link { display: inline-block; margin-top: 15px; text-decoration: none; color: #007bff; }
    </style>
</head>
<body>

    <h2>Tambah Lagu Baru</h2>
    
    <form action="insert.php" method="POST" enctype="multipart/form-data">
        
        <label for="album_id">Pilih Album:</label>
        <select id="album_id" name="album_id" required>
            <option value="">-- Pilih Album --</option>
            <?php while ($album = mysqli_fetch_assoc($albums_query)): ?>
                <option value="<?php echo $album['album_id']; ?>">
                    <?php echo htmlspecialchars($album['title']); ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label for="track_number">Nomor Track:</label>
        <input type="number" id="track_number" name="track_number" min="1" required>
        
        <label for="title">Judul Lagu:</label>
        <input type="text" id="title" name="title" placeholder="Contoh: How You Like That" required>
        
        <label for="audio">File Audio (MP3/WAV - Opsional):</label>
        <input type="file" id="audio" name="audio" accept=".mp3,.wav,.ogg">

        <div class="checkbox-group">
            <label style="display: inline-flex; align-items: center; font-weight: normal;">
                <input type="checkbox" name="is_top_track" value="1" style="width: auto; margin-right: 8px;"> 
                Jadikan Top Track (Lagu Unggulan)
            </label>
        </div>
        
        <button type="submit">Simpan Data</button>
    </form>

    <a href="view.php" class="back-link">← Kembali ke Daftar Data</a>

</body>
</html>