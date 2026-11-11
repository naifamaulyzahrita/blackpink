<?php
require_once __DIR__ . "/../connection.php";

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) { header("Location: view.php"); exit; }

// Ambil data tur untuk dropdown
$tours_query = mysqli_query($conn, "SELECT tour_id, tour_name FROM tours ORDER BY tour_name ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tour_id      = (int) $_POST['tour_id'];
    $event_name   = mysqli_real_escape_string($conn, $_POST['event_name']);
    $event_date   = mysqli_real_escape_string($conn, $_POST['event_date']);
    $ticket_status= mysqli_real_escape_string($conn, $_POST['ticket_status']);

    // Field opsional
    $venue        = !empty($_POST['venue']) ? "'" . mysqli_real_escape_string($conn, $_POST['venue']) . "'" : "NULL";
    $city         = !empty($_POST['city']) ? "'" . mysqli_real_escape_string($conn, $_POST['city']) . "'" : "NULL";
    $country      = !empty($_POST['country']) ? "'" . mysqli_real_escape_string($conn, $_POST['country']) . "'" : "NULL";
    $start_time   = !empty($_POST['start_time']) ? "'" . mysqli_real_escape_string($conn, $_POST['start_time']) . "'" : "NULL";
    $timezone     = !empty($_POST['timezone']) ? "'" . mysqli_real_escape_string($conn, $_POST['timezone']) . "'" : "NULL";
    $ticket_url   = !empty($_POST['ticket_url']) ? "'" . mysqli_real_escape_string($conn, $_POST['ticket_url']) . "'" : "NULL";
    $extra_info   = !empty($_POST['extra_info']) ? "'" . mysqli_real_escape_string($conn, $_POST['extra_info']) . "'" : "NULL";

    if ($tour_id <= 0 || empty($event_name) || empty($event_date)) {
        echo "<script>alert('Tur, Nama Event, dan Tanggal wajib diisi!'); history.back();</script>";
        exit;
    }

    $sql = "UPDATE events SET 
            tour_id = $tour_id, 
            event_name = '$event_name', 
            venue = $venue, 
            city = $city, 
            country = $country, 
            event_date = '$event_date', 
            start_time = $start_time, 
            timezone = $timezone, 
            ticket_status = '$ticket_status', 
            ticket_url = $ticket_url, 
            extra_info = $extra_info 
            WHERE event_id = $id";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Data event berhasil diupdate!'); window.location.href='view.php';</script>";
    } else {
        echo "<script>alert('Gagal update: " . mysqli_error($conn) . "'); history.back();</script>";
    }
    exit;
}

// Ambil data lama
$row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM events WHERE event_id = $id"));
if (!$row) { header("Location: view.php"); exit; }
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Event Konser</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        form { max-width: 600px; }
        label { display: block; margin-top: 10px; font-weight: bold; }
        input[type="text"], input[type="date"], input[type="time"], select, textarea { width: 100%; padding: 8px; margin-top: 5px; box-sizing: border-box; }
        .row { display: flex; gap: 10px; }
        .row > div { flex: 1; }
        button { margin-top: 15px; padding: 10px 15px; background-color: #ffc107; color: black; border: none; cursor: pointer; font-weight: bold; }
        .back-link { display: inline-block; margin-top: 15px; text-decoration: none; color: #007bff; }
    </style>
</head>
<body>
    <h2>Edit Event Konser</h2>
    <form action="edit.php?id=<?php echo $id; ?>" method="POST">
        <label for="tour_id">Pilih Tur:</label>
        <select id="tour_id" name="tour_id" required>
            <option value="">-- Pilih Tur --</option>
            <?php while ($tour = mysqli_fetch_assoc($tours_query)): ?>
                <option value="<?php echo $tour['tour_id']; ?>" <?php echo ($tour['tour_id'] == $row['tour_id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($tour['tour_name']); ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label for="event_name">Nama Event:</label>
        <input type="text" id="event_name" name="event_name" value="<?php echo htmlspecialchars($row['event_name'] ?? ''); ?>" required>
        
        <div class="row">
            <div>
                <label for="venue">Venue:</label>
                <input type="text" id="venue" name="venue" value="<?php echo htmlspecialchars($row['venue'] ?? ''); ?>">
            </div>
            <div>
                <label for="city">Kota:</label>
