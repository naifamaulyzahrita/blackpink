<?php
require_once __DIR__ . "/../connection.php";
$albums_query = mysqli_query($conn, "SELECT album_id, title FROM albums ORDER BY title ASC");
$artists_query = mysqli_query($conn, "SELECT artist_id, name FROM artists ORDER BY name ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $album_id = (int) $_POST['album_id'];
    $artist_id = (int) $_POST['artist_id'];

    if ($album_id <= 0 || $artist_id <= 0) {
        echo "<script>alert('Album dan Artis wajib dipilih!'); history.back();</script>";
        exit;
    }

    $sql = "INSERT INTO albums_artists (album_id, artist_id) VALUES ($album_id, $artist_id)";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Relasi berhasil ditambahkan!'); window.location.href='view.php';</script>";
        exit;
    } else {
        echo "<script>alert('Gagal: Relasi mungkin sudah ada. (" . mysqli_error($conn) . ")'); history.back();</script>";
        exit;
    }
}
mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Relasi Album-Artis</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        form { max-width: 400px; }
        label { display: block; margin-top: 10px; font-weight: bold; }
        select { width: 100%; padding: 8px; margin-top: 5px; box-sizing: border-box; }
        button { margin-top: 15px; padding: 10px 15px; background-color: #007bff; color: white; border: none; cursor: pointer; }
        .back-link { display: inline-block; margin-top: 15px; text-decoration: none; color: #007bff; }
    </style>
</head>
<body>
    <h2>Tambah Relasi Album & Artis</h2>
    <form action="insert.php" method="POST">
        <label for="album_id">Pilih Album:</label>
        <select id="album_id" name="album_id" required>
            <option value="">-- Pilih Album --</option>
            <?php while ($a = mysqli_fetch_assoc($albums_query)): ?>
                <option value="<?php echo $a['album_id']; ?>"><?php echo htmlspecialchars($a['title']); ?></option>
            <?php endwhile; ?>
        </select>

        <label for="artist_id">Pilih Artis:</label>
        <select id="artist_id" name="artist_id" required>
            <option value="">-- Pilih Artis --</option>
            <?php while ($ar = mysqli_fetch_assoc($artists_query)): ?>
                <option value="<?php echo $ar['artist_id']; ?>"><?php echo htmlspecialchars($ar['name']); ?></option>
            <?php endwhile; ?>
        </select>
        
        <button type="submit">Simpan Relasi</button>
    </form>
    <a href="view.php" class="back-link">← Kembali</a>
</body>
</html>