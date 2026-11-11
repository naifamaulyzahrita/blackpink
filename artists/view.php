<?php
require_once __DIR__ . "/../connection.php";

$sql = "SELECT artists.artist_id, artists.name, artists.artist_type, 
               artists.full_name, artists.birth_date, artists.birth_country, 
               artists.position, artists.highlight, artists.photo_path 
        FROM artists 
        ORDER BY artists.artist_id ASC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Artis - BLACKPINK</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; font-size: 13px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .btn { padding: 6px 12px; text-decoration: none; color: white; border-radius: 4px; font-size: 13px; border: none; cursor: pointer; display: inline-block; margin-right: 5px; }
        .btn-add { background-color: #28a745; }
        .btn-edit { background-color: #ffc107; color: black; }
        .btn-delete { background-color: #dc3545; }
        img { width: 40px; height: 40px; object-fit: cover; border-radius: 50%; }
    </style>
</head>
<body>
    <h2>Daftar Artis (Artists)</h2>
    <a href="insert.php" class="btn btn-add">+ Tambah Artis</a>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama Panggilan</th>
                <th>Tipe</th>
                <th>Nama Lengkap</th>
                <th>Tanggal Lahir</th>
                <th>Negara</th>
                <th>Posisi</th>
                <th>Highlight</th>
                <th>Foto</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr id="row-<?php echo $row['artist_id']; ?>">
                        <td><?php echo htmlspecialchars($row['artist_id']); ?></td>
                        <td><strong><?php echo htmlspecialchars($row['name'] ?? ''); ?></strong></td>
                        <td><?php echo htmlspecialchars($row['artist_type'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['full_name'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['birth_date'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($row['birth_country'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($row['position'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($row['highlight'] ?? '-'); ?></td>
                        <td>
                            <?php if (!empty($row['photo_path'])): ?>
                                <img src="../<?php echo htmlspecialchars($row['photo_path']); ?>" alt="Foto">
                            <?php else: ?>
                                <em>Tidak ada</em>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="edit.php?id=<?php echo $row['artist_id']; ?>" class="btn btn-edit">Edit</a>
                            <button onclick="hapusData(<?php echo $row['artist_id']; ?>)" class="btn btn-delete">Hapus</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="10" style="text-align: center;">Belum ada data artis.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <script>
    function hapusData(id) {
        if (confirm('Yakin ingin menghapus artis ini?')) {
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