<?php
require_once __DIR__ . "/../connection.php";

$album_id = isset($_GET['album']) ? (int) $_GET['album'] : 0;
$artist_id = isset($_GET['artist']) ? (int) $_GET['artist'] : 0;

if ($album_id <= 0 || $artist_id <= 0) { header("Location: view.php"); exit; }

$albums_query = mysqli_query($conn, "SELECT album_id, title FROM albums ORDER BY title ASC");
$artists_query = mysqli_query($conn, "SELECT artist_id, name FROM artists ORDER BY name ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_album_id = (int) $_POST['album_id'];
    $new_artist_id = (int) $_POST['artist_id'];

    if ($new_album_id <= 0 || $new_artist_id <= 0) {
        echo "<script>alert('Album dan Artis wajib dipilih!'); history.back();</script>";
        exit;
    }

    // Hapus relasi lama, lalu insert yang baru (karena PK gabungan)
    mysqli_query($conn, "DELETE FROM albums_artists WHERE album_id = $album_id AND artist_id = $artist_id");
    
    $sql = "INSERT INTO albums_artists (album_id, artist_id) VALUES ($new_album_id, $new_artist_id)";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Relasi berhasil diupdate!'); window.location.href='view.php';</script>";
    } else {
        echo "<script>alert('Gagal: Relasi mungkin sudah ada. (" . mysqli_error($conn) . ")'); history.back();</script>";
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Relasi</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        form { max-width: 400px; }
        label { display: block; margin-top: 10px; font-weight: bold; }
        select { width: 100%; padding: 8px; margin-top: 5px; box-sizing: border-box; }
        button { margin-top: 15px; padding: 10px 15px; background-color: #ffc107; color: black; border: none; cursor: pointer; font-weight: bold; }
        .back-link { display: inline-block; margin-top: 15px; text-decoration: none; color: #007bff; }
    </style>
</head>
<body>
    <h2>Edit Relasi Album & Artis</h2>
    <form action="edit.php?album=<?php echo $album_id; ?>&artist=<?php echo $artist_id; ?>" method="POST">
        <label for="album_id">Pilih Album:</label>
        <select id="album_id" name="album_id" required>
            <?php while ($a = mysqli_fetch_assoc($albums_query)): ?>
                <option value="<?php echo $a['album_id']; ?>" <?php echo ($a['album_id'] == $album_id) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($a['title']); ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label for="artist_id">Pilih Artis:</label>
        <select id="artist_id" name="artist_id" required>
            <?php while ($ar = mysqli_fetch_assoc($artists_query)): ?>
                <option value="<?php echo $ar['artist_id']; ?>" <?php echo ($ar['artist_id'] == $artist_id) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($ar['name']); ?>
                </option>
            <?php endwhile; ?>
        </select>
        
        <button type="submit">Update Relasi</button>
    </form>
    <a href="view.php" class="back-link">← Kembali</a>
</body>
</html>