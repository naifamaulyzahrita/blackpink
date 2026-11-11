<?php
require_once __DIR__ . "/../connection.php";

$sql = "SELECT albums.album_id, albums.album_type_id, albums.title, 
               albums.release_date, albums.category, albums.cover_path, 
               albums.stream_url, albums.buy_url, 
               albums_type.album_type 
        FROM albums 
        JOIN albums_type ON albums.album_type_id = albums_type.album_type_id 
        ORDER BY albums.album_id DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Album - BLACKPINK</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; font-size: 13px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .btn { padding: 6px 12px; text-decoration: none; color: white; border-radius: 4px; font-size: 13px; border: none; cursor: pointer; display: inline-block; margin-right: 5px; }
        .btn-add { background-color: #28a745; }
        .btn-edit { background-color: #ffc107; color: black; }
        .btn-delete { background-color: #dc3545; }
        img { width: 40px; height: 40px; object-fit: cover; border-radius: 4px; }
    </style>
</head>
<body>
    <h2>Daftar Album (Discography)</h2>
    <a href="insert.php" class="btn btn-add">+ Tambah Album</a>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Judul Album</th>
                <th>Jenis</th>
                <th>Kategori</th>
                <th>Rilis</th>
                <th>Cover</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr id="row-<?php echo $row['album_id']; ?>">
                        <td><?php echo htmlspecialchars($row['album_id']); ?></td>
                        <td><strong><?php echo htmlspecialchars($row['title'] ?? ''); ?></strong></td>
                        <td><?php echo htmlspecialchars($row['album_type'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['category'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['release_date'] ?? '-'); ?></td>
                        <td>
                            <?php if (!empty($row['cover_path'])): ?>
                                <img src="../<?php echo htmlspecialchars($row['cover_path']); ?>" alt="Cover">
                            <?php else: ?>
                                <em>-</em>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="edit.php?id=<?php echo $row['album_id']; ?>" class="btn btn-edit">Edit</a>
                            <button onclick="hapusData(<?php echo $row['album_id']; ?>)" class="btn btn-delete">Hapus</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="7" style="text-align: center;">Belum ada data album.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <script>
    function hapusData(id) {
        if (confirm('Yakin ingin menghapus album ini?')) {
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