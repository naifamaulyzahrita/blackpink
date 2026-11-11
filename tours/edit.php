<?php
require_once __DIR__ . "/../connection.php";

// 1. Ambil ID dari URL
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    header("Location: view.php");
    exit;
}

// 2. Proses Update jika form di-submit (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tour_name  = mysqli_real_escape_string($conn, $_POST['tour_name']);
    $start_date = !empty($_POST['start_date']) ? "'" . mysqli_real_escape_string($conn, $_POST['start_date']) . "'" : "NULL";
    $end_date   = !empty($_POST['end_date']) ? "'" . mysqli_real_escape_string($conn, $_POST['end_date']) . "'" : "NULL";

    $sql = "UPDATE tours SET 
            tour_name = '$tour_name', 
            start_date = $start_date, 
            end_date = $end_date 
            WHERE tour_id = $id";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Data tur berhasil diupdate!'); window.location.href='view.php';</script>";
    } else {
        echo "<script>alert('Gagal update: " . mysqli_error($conn) . "'); history.back();</script>";
    }
    exit;
}

// 3. Ambil data lama untuk ditampilkan di form
$result = mysqli_query($conn, "SELECT * FROM tours WHERE tour_id = $id");
$row = mysqli_fetch_assoc($result);

if (!$row) {
    header("Location: view.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Tur</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        form { max-width: 500px; }
        label { display: block; margin-top: 10px; font-weight: bold; }
        input[type="text"], input[type="date"] { width: 100%; padding: 8px; margin-top: 5px; box-sizing: border-box; }
        button { margin-top: 15px; padding: 10px 15px; background-color: #ffc107; color: black; border: none; cursor: pointer; font-weight: bold; }
        .back-link { display: inline-block; margin-top: 15px; text-decoration: none; color: #007bff; }
    </style>
</head>
<body>

    <h2>Edit Data Tur</h2>
    
    <form action="edit.php?id=<?php echo $id; ?>" method="POST">
        <label for="tour_name">Nama Tur:</label>
        <input type="text" id="tour_name" name="tour_name" value="<?php echo htmlspecialchars($row['tour_name'] ?? ''); ?>" required>
        
        <label for="start_date">Tanggal Mulai:</label>
        <input type="date" id="start_date" name="start_date" value="<?php echo htmlspecialchars($row['start_date'] ?? ''); ?>">
        
        <label for="end_date">Tanggal Selesai:</label>
        <input type="date" id="end_date" name="end_date" value="<?php echo htmlspecialchars($row['end_date'] ?? ''); ?>">
        
        <button type="submit">Update Data</button>
    </form>

    <a href="view.php" class="back-link">← Kembali ke Daftar Data</a>

</body>
</html>