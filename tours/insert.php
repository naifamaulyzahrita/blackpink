<?php
require_once __DIR__ . "/../connection.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Bersihkan input
    $tour_name  = mysqli_real_escape_string($conn, $_POST['tour_name']);
    $start_date = !empty($_POST['start_date']) ? mysqli_real_escape_string($conn, $_POST['start_date']) : null;
    $end_date   = !empty($_POST['end_date']) ? mysqli_real_escape_string($conn, $_POST['end_date']) : null;

    // 2. Validasi sederhana
    if (empty($tour_name)) {
        echo "<script>alert('Nama tur tidak boleh kosong!'); history.back();</script>";
        exit;
    }

    // 3. Siapkan query INSERT
    // Jika tanggal kosong, kita set jadi NULL di database
    $start_sql = $start_date ? "'$start_date'" : "NULL";
    $end_sql   = $end_date ? "'$end_date'" : "NULL";

    $sql = "INSERT INTO tours (tour_name, start_date, end_date) 
            VALUES ('$tour_name', $start_sql, $end_sql)";

    // 4. Eksekusi query
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Data tur berhasil ditambahkan!'); window.location.href='view.php';</script>";
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
    <title>Tambah Tur</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        form { max-width: 500px; }
        label { display: block; margin-top: 10px; font-weight: bold; }
        input[type="text"], input[type="date"] { width: 100%; padding: 8px; margin-top: 5px; box-sizing: border-box; }
        button { margin-top: 15px; padding: 10px 15px; background-color: #007bff; color: white; border: none; cursor: pointer; }
        .back-link { display: inline-block; margin-top: 15px; text-decoration: none; color: #007bff; }
    </style>
</head>
<body>

    <h2>Tambah Tur Baru</h2>
    
    <form action="insert.php" method="POST">
        <label for="tour_name">Nama Tur:</label>
        <input type="text" id="tour_name" name="tour_name" placeholder="Contoh: BORN PINK WORLD TOUR 2027" required>
        
        <label for="start_date">Tanggal Mulai:</label>
        <input type="date" id="start_date" name="start_date">
        
        <label for="end_date">Tanggal Selesai:</label>
        <input type="date" id="end_date" name="end_date">
        
        <button type="submit">Simpan Data</button>
    </form>

    <a href="view.php" class="back-link">← Kembali ke Daftar Data</a>

</body>
</html>