<?php
require_once __DIR__ . "/../connection.php";

$sql = "SELECT tracks.track_id, tracks.album_id, tracks.track_number, 
               tracks.title, tracks.audio_path, tracks.is_top_track, 
               albums.title AS album_title 
        FROM tracks 
        JOIN albums ON tracks.album_id = albums.album_id 
        ORDER BY albums.title, tracks.track_number ASC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Lagu - BLACKPINK</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; font-size: 13px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .btn { padding: 6px 12px; text-decoration: none; color: white; border-radius: 4px; font-size: 13px; border: none; cursor: pointer; display: inline-block; margin-right: 5px; }
        .btn-add { background-color: #28a745; }
        .btn-edit { background-color: #ffc107; color: black; }
        .btn-delete { background-color: #dc3545; }
        .top-track { color: #ffc107; font-weight: bold; }
    </style>
</head>
<body>
    <h2>Daftar Lagu (Tracks)</h2>
    <a href="insert.php" class="btn btn-add">+ Tambah Lagu</a>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Album</th>
                <th>No.</th>
                <th>Judul Lagu</th>
                <th>File Audio</th>
                <th>Top Track</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr id="row-<?php echo $row['track_id']; ?>">
                        <td><?php echo htmlspecialchars($row['track_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['album_title'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['track_number'] ?? ''); ?></td>
                        <td><strong><?php echo htmlspecialchars($row['title'] ?? ''); ?></strong></td>
                        <td><?php echo !empty($row['audio_path']) ? htmlspecialchars($row['audio_path']) : '<em>-</em>'; ?></td>
                        <td><?php echo ($row['is_top_track'] == 1) ? '<span class="top-track">★ Ya</span>' : 'Tidak'; ?></td>
                        <td>
                            <a href="edit.php?id=<?php echo $row['track_id']; ?>" class="btn btn-edit">Edit</a>
                            <button onclick="hapusData(<?php echo $row['track_id']; ?>)" class="btn btn-delete">Hapus</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="7" style="text-align: center;">Belum ada data lagu.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <script>
    function hapusData(id) {
        if (confirm('Yakin ingin menghapus lagu ini?')) {
            const formData = new FormData();
            formData.append('id', id);
            fetch('delete.php', { method: 'POST', body: formData })
            .then(response => {
                if (response.ok) {
                    alert('Data berhasil dihapus!');
                    document.getElementById('row-' + id).remove();
                } else { alert('Gagal menghapus data.'); }
            });
        }
    }
    </script>
</body>
</html>
<?php mysqli_close($conn); ?>