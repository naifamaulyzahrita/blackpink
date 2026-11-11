<?php
require_once __DIR__ . "/../connection.php";

$sql = "SELECT events.event_id, events.tour_id, events.event_name, 
               events.venue, events.city, events.country, 
               events.event_date, events.start_time, events.timezone, 
               events.ticket_status, events.ticket_url, events.extra_info, 
               tours.tour_name 
        FROM events 
        JOIN tours ON events.tour_id = tours.tour_id 
        ORDER BY events.event_date ASC, events.start_time ASC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Event - BLACKPINK</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; font-size: 13px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .btn { padding: 6px 12px; text-decoration: none; color: white; border-radius: 4px; font-size: 13px; border: none; cursor: pointer; display: inline-block; margin-right: 5px; }
        .btn-add { background-color: #28a745; }
        .btn-edit { background-color: #ffc107; color: black; }
        .btn-delete { background-color: #dc3545; }
        .status { padding: 4px 8px; border-radius: 4px; font-size: 12px; color: white; font-weight: bold; }
        .status-available { background-color: #28a745; }
        .status-onsale { background-color: #17a2b8; }
        .status-presale { background-color: #ffc107; color: black; }
        .status-registration { background-color: #6c757d; }
        .status-soldout { background-color: #dc3545; }
    </style>
</head>
<body>
    <h2>Jadwal Konser (Events)</h2>
    <a href="insert.php" class="btn btn-add">+ Tambah Event</a>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama Tur</th>
                <th>Nama Event</th>
                <th>Lokasi</th>
                <th>Tanggal & Waktu</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): 
                    $statusClass = 'status-available';
                    $status = $row['ticket_status'] ?? '';
                    if ($status === 'ON SALE') $statusClass = 'status-onsale';
                    elseif ($status === 'PRE-SALE') $statusClass = 'status-presale';
                    elseif ($status === 'REGISTRATION') $statusClass = 'status-registration';
                    elseif ($status === 'SOLD OUT') $statusClass = 'status-soldout';
                ?>
                    <tr id="row-<?php echo $row['event_id']; ?>">
                        <td><?php echo htmlspecialchars($row['event_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['tour_name'] ?? ''); ?></td>
                        <td><strong><?php echo htmlspecialchars($row['event_name'] ?? ''); ?></strong></td>
                        <td>
                            <?php echo htmlspecialchars($row['venue'] ?? ''); ?><br>
                            <small><?php echo htmlspecialchars($row['city'] ?? ''); ?>, <?php echo htmlspecialchars($row['country'] ?? ''); ?></small>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($row['event_date'] ?? ''); ?><br>
                            <small><?php echo htmlspecialchars($row['start_time'] ?? ''); ?> <?php echo htmlspecialchars($row['timezone'] ?? ''); ?></small>
                        </td>
                        <td><span class="status <?php echo $statusClass; ?>"><?php echo $status; ?></span></td>
                        <td>
                            <a href="edit.php?id=<?php echo $row['event_id']; ?>" class="btn btn-edit">Edit</a>
                            <button onclick="hapusData(<?php echo $row['event_id']; ?>)" class="btn btn-delete">Hapus</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="7" style="text-align: center;">Belum ada data event.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <script>
    function hapusData(id) {
        if (confirm('Yakin ingin menghapus event ini?')) {
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