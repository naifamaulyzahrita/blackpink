<?php
require_once __DIR__ . "/../connection.php";

// Ambil data tours untuk dropdown
$tours_query = mysqli_query($conn, "SELECT tour_id, tour_name FROM tours ORDER BY tour_name ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Bersihkan input
    $tour_id      = (int) $_POST['tour_id'];
    $event_name   = mysqli_real_escape_string($conn, $_POST['event_name']);
    $event_date   = mysqli_real_escape_string($conn, $_POST['event_date']);
    $ticket_status= mysqli_real_escape_string($conn, $_POST['ticket_status']);

    // Field opsional (bisa NULL)
    $venue        = !empty($_POST['venue']) ? "'" . mysqli_real_escape_string($conn, $_POST['venue']) . "'" : "NULL";
    $city         = !empty($_POST['city']) ? "'" . mysqli_real_escape_string($conn, $_POST['city']) . "'" : "NULL";
    $country      = !empty($_POST['country']) ? "'" . mysqli_real_escape_string($conn, $_POST['country']) . "'" : "NULL";
    $start_time   = !empty($_POST['start_time']) ? "'" . mysqli_real_escape_string($conn, $_POST['start_time']) . "'" : "NULL";
    $timezone     = !empty($_POST['timezone']) ? "'" . mysqli_real_escape_string($conn, $_POST['timezone']) . "'" : "NULL";
    $ticket_url   = !empty($_POST['ticket_url']) ? "'" . mysqli_real_escape_string($conn, $_POST['ticket_url']) . "'" : "NULL";
    $extra_info   = !empty($_POST['extra_info']) ? "'" . mysqli_real_escape_string($conn, $_POST['extra_info']) . "'" : "NULL";

    // 2. Validasi Input Wajib
    if ($tour_id <= 0 || empty($event_name) || empty($event_date) || empty($ticket_status)) {
        echo "<script>alert('Tur, Nama Event, Tanggal, dan Status Tiket wajib diisi!'); history.back();</script>";
        exit;
    }

    // 3. Query INSERT
    $sql = "INSERT INTO events (tour_id, event_name, venue, city, country, event_date, start_time, timezone, ticket_status, ticket_url, extra_info) 
            VALUES ($tour_id, '$event_name', $venue, $city, $country, '$event_date', $start_time, $timezone, '$ticket_status', $ticket_url, $extra_info)";

    // 4. Eksekusi
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Event berhasil ditambahkan!'); window.location.href='view.php';</script>";
        exit;
    } else {
        echo "<script>alert('Gagal menambahkan data: " . mysqli_error($conn) . "'); history.back();</script>";
        exit;
    }
}
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Event Konser</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        form { max-width: 600px; }
        label { display: block; margin-top: 10px; font-weight: bold; }
        input[type="text"], input[type="date"], input[type="time"], select, textarea { width: 100%; padding: 8px; margin-top: 5px; box-sizing: border-box; }
        .row { display: flex; gap: 10px; }
        .row > div { flex: 1; }
        button { margin-top: 15px; padding: 10px 15px; background-color: #007bff; color: white; border: none; cursor: pointer; }
        .back-link { display: inline-block; margin-top: 15px; text-decoration: none; color: #007bff; }
    </style>
</head>
<body>

    <h2>Tambah Event Konser Baru</h2>
    
    <form action="insert.php" method="POST">
        
        <label for="tour_id">Pilih Tur:</label>
        <select id="tour_id" name="tour_id" required>
            <option value="">-- Pilih Tur --</option>
            <?php while ($tour = mysqli_fetch_assoc($tours_query)): ?>
                <option value="<?php echo $tour['tour_id']; ?>">
                    <?php echo htmlspecialchars($tour['tour_name']); ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label for="event_name">Nama Event:</label>
        <input type="text" id="event_name" name="event_name" placeholder="Contoh: Tokyo Dome / Night 1" required>
        
        <div class="row">
            <div>
                <label for="venue">Venue:</label>
                <input type="text" id="venue" name="venue" placeholder="Contoh: Tokyo Dome">
            </div>
            <div>
                <label for="city">Kota:</label>
                <input type="text" id="city" name="city" placeholder="Contoh: Tokyo">
            </div>
            <div>
                <label for="country">Negara:</label>
                <input type="text" id="country" name="country" placeholder="Contoh: Japan">
            </div>
        </div>

        <div class="row">
            <div>
                <label for="event_date">Tanggal Event:</label>
                <input type="date" id="event_date" name="event_date" required>
            </div>
            <div>
                <label for="start_time">Jam Mulai:</label>
                <input type="time" id="start_time" name="start_time">
            </div>
            <div>
                <label for="timezone">Timezone:</label>
                <input type="text" id="timezone" name="timezone" placeholder="Contoh: JST, KST, PDT">
            </div>
        </div>

        <label for="ticket_status">Status Tiket:</label>
        <select id="ticket_status" name="ticket_status" required>
            <option value="AVAILABLE">AVAILABLE</option>
            <option value="ON SALE">ON SALE</option>
            <option value="PRE-SALE">PRE-SALE</option>
            <option value="REGISTRATION">REGISTRATION</option>
            <option value="SOLD OUT">SOLD OUT</option>
        </select>

        <label for="ticket_url">Link Tiket (URL):</label>
        <input type="text" id="ticket_url" name="ticket_url" placeholder="https://...">

        <label for="extra_info">Info Tambahan:</label>
        <textarea id="extra_info" name="extra_info" rows="3" placeholder="Contoh: VIP Soundcheck packages available..."></textarea>
        
        <button type="submit">Simpan Data</button>
    </form>

    <a href="view.php" class="back-link">← Kembali ke Daftar Data</a>

</body>
</html>