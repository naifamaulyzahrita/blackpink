<?php
require_once __DIR__ . "/../connection.php";

$sql = "SELECT subscriber_registrations.registration_id, subscriber_registrations.email, 
               subscriber_registrations.full_name, subscriber_registrations.registered_at 
        FROM subscriber_registrations 
        ORDER BY subscriber_registrations.registered_at DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Subscriber - BLACKPINK</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 70%; margin-top: 20px; font-size: 14px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .btn { padding: 6px 12px; text-decoration: none; color: white; border-radius: 4px; font-size: 13px; border: none; cursor: pointer; display: inline-block; margin-right: 5px; }
        .btn-add { background-color: #28a745; }
        .btn-edit { background-color: #ffc107; color: black; }
        .btn-delete { background-color: #dc3545; }
    </style>
</head>
<body>
    <h2>Data Subscriber (Tour Registry)</h2>
    <a href="insert.php" class="btn btn-add">+ Tambah Subscriber</a>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Email</th>
                <th>Nama Lengkap</th>
                <th>Tanggal Daftar</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr id="row-<?php echo $row['registration_id']; ?>">
                        <td><?php echo htmlspecialchars($row['registration_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['email'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['full_name'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($row['registered_at'] ?? ''); ?></td>
                        <td>
                            <a href="edit.php?id=<?php echo $row['registration_id']; ?>" class="btn btn-edit">Edit</a>
                            <button onclick="hapusData(<?php echo $row['registration_id']; ?>)" class="btn btn-delete">Hapus</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5" style="text-align: center;">Belum ada subscriber.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <script>
    function hapusData(id) {
        if (confirm('Yakin ingin menghapus subscriber ini?')) {
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