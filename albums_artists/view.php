<?php
require_once __DIR__ . "/../connection.php";

$sql = "SELECT albums_artists.album_id, albums_artists.artist_id, 
               albums.title AS album_title, artists.name AS artist_name 
        FROM albums_artists 
        JOIN albums ON albums_artists.album_id = albums.album_id 
        JOIN artists ON albums_artists.artist_id = artists.artist_id 
        ORDER BY albums.title, artists.name";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Relasi Album & Artis</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 60%; margin-top: 20px; font-size: 14px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .btn { padding: 6px 12px; text-decoration: none; color: white; border-radius: 4px; font-size: 13px; border: none; cursor: pointer; display: inline-block; margin-right: 5px; }
        .btn-add { background-color: #28a745; }
        .btn-edit { background-color: #ffc107; color: black; }
        .btn-delete { background-color: #dc3545; }
    </style>
</head>
<body>
    <h2>Relasi Album & Artis</h2>
    <a href="insert.php" class="btn btn-add">+ Tambah Relasi</a>
    <table>
        <thead>
            <tr>
                <th>Album</th>
                <th>Artis</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr id="row-<?php echo $row['album_id']; ?>-<?php echo $row['artist_id']; ?>">
                        <td><?php echo htmlspecialchars($row['album_title'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['artist_name'] ?? ''); ?></td>
                        <td>
                            <a href="edit.php?album=<?php echo $row['album_id']; ?>&artist=<?php echo $row['artist_id']; ?>" class="btn btn-edit">Edit</a>
                            <button onclick="hapusData(<?php echo $row['album_id']; ?>, <?php echo $row['artist_id']; ?>)" class="btn btn-delete">Hapus</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="3" style="text-align: center;">Belum ada relasi.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <script>
    function hapusData(albumId, artistId) {
        if (confirm('Yakin ingin menghapus relasi ini?')) {
            const formData = new FormData();
            formData.append('album_id', albumId);
            formData.append('artist_id', artistId);
            fetch('delete.php', { method: 'POST', body: formData })
            .then(response => {
                if (response.ok) {
                    alert('Data berhasil dihapus!');
                    document.getElementById('row-' + albumId + '-' + artistId).remove();
                } else { alert('Gagal menghapus data.'); }
            });
        }
    }
    </script>
</body>
</html>
<?php mysqli_close($conn); ?>