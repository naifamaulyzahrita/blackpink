<?php
require_once __DIR__ . "/../connection.php";

$sql = "SELECT gallery.media_id, gallery.title, gallery.file_path, 
               gallery.media_type, gallery.album_id, gallery.uploaded_at, 
               albums.title AS album_title 
        FROM gallery 
        LEFT JOIN albums ON gallery.album_id = albums.album_id 
        ORDER BY gallery.uploaded_at DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Galeri - BLACKPINK</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 80%; margin-top: 20px; font-size: 14px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .btn { padding: 6px 12px; text-decoration: none; color: white; border-radius: 4px; font-size: 13px; border: none; cursor: pointer; display: inline-block; margin-right: 5px; }
        .btn-add { background-color: #28a745; }
        .btn-edit { background-color: #ffc107; color: black; }
        .btn-delete { background-color: #dc3545; }
        img { width: 60px; height: 60px; object-fit: cover; border-radius: 4px; }
    </style>
</head>
<body>
    <h2>Galeri Foto</h2>
    <a href="insert.php" class="btn btn-add">+ Tambah Foto</a>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Judul</th>
                <th>Album</th>
                <th>Foto</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr id="row-<?php echo $row['media_id']; ?>">
                        <td><?php echo htmlspecialchars($row['media_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['title'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['album_title'] ?? 'General'); ?></td>
                        <td><img src="../<?php echo htmlspecialchars($row['file_path'] ?? ''); ?>" alt="Foto"></td>
                        <td>
                            <a href="edit.php?id=<?php echo $row['media_id']; ?>" class="btn btn-edit">Edit</a>
                            <button onclick="hapusData(<?php echo $row['media_id']; ?>)" class="btn btn-delete">Hapus</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5" style="text-align: center;">Belum ada foto.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <script>
    function hapusData(id) {
        if (confirm('Yakin ingin menghapus foto ini?')) {
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