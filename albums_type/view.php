<?php
require_once __DIR__ . "/../connection.php";

$sql = "SELECT albums_type.album_type_id, albums_type.album_type 
        FROM albums_type 
        ORDER BY albums_type.album_type_id ASC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Jenis Album - BLACKPINK</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 60%; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .btn { padding: 6px 12px; text-decoration: none; color: white; border-radius: 4px; font-size: 13px; border: none; cursor: pointer; display: inline-block; margin-right: 5px; }
        .btn-add { background-color: #28a745; }
        .btn-edit { background-color: #ffc107; color: black; }
        .btn-delete { background-color: #dc3545; }
    </style>
</head>
<body>
    <h2>Daftar Jenis Album (Albums Type)</h2>
    <a href="insert.php" class="btn btn-add">+ Tambah Jenis Album</a>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Jenis Album</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr id="row-<?php echo $row['album_type_id']; ?>">
                        <td><?php echo htmlspecialchars($row['album_type_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['album_type']); ?></td>
                        <td>
                            <a href="edit.php?id=<?php echo $row['album_type_id']; ?>" class="btn btn-edit">Edit</a>
                            <button onclick="hapusData(<?php echo $row['album_type_id']; ?>)" class="btn btn-delete">Hapus</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="3" style="text-align: center;">Belum ada data.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <script>
    function hapusData(id) {
        if (confirm('Yakin ingin menghapus data ini?')) {
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