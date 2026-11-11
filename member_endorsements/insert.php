<?php
require_once __DIR__ . "/../connection.php";

// Ambil data member saja untuk dropdown
$members_query = mysqli_query($conn, "SELECT artist_id, name FROM artists WHERE artist_type = 'member' ORDER BY name ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $artist_id   = (int) $_POST['artist_id'];
    $brand_name  = mysqli_real_escape_string($conn, $_POST['brand_name']);
    $title       = !empty($_POST['title']) ? "'" . mysqli_real_escape_string($conn, $_POST['title']) . "'" : "NULL";

    if ($artist_id <= 0 || empty($brand_name)) {
        echo "<script>alert('Member dan Brand wajib diisi!'); history.back();</script>";
        exit;
    }

    $sql = "INSERT INTO member_endorsements (artist_id, brand_name, title) VALUES ($artist_id, '$brand_name', $title)";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Endorsement berhasil ditambahkan!'); window.location.href='view.php';</script>";
        exit;
    } else {
        echo "<script>alert('Gagal: " . mysqli_error($conn) . "'); history.back();</script>";
        exit;
    }
}
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Endorsement</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        form { max-width: 400px; }
        label { display: block; margin-top: 10px; font-weight: bold; }
        input[type="text"], select { width: 100%; padding: 8px; margin-top: 5px; box-sizing: border-box; }
        button { margin-top: 15px; padding: 10px 15px; background-color: #007bff; color: white; border: none; cursor: pointer; }
        .back-link { display: inline-block; margin-top: 15px; text-decoration: none; color: #007bff; }
    </style>
</head>
<body>
    <h2>Tambah Endorsement Baru</h2>
    <form action="insert.php" method="POST">
        <label for="artist_id">Pilih Member:</label>
        <select id="artist_id" name="artist_id" required>
            <option value="">-- Pilih Member --</option>
            <?php while ($m = mysqli_fetch_assoc($members_query)): ?>
                <option value="<?php echo $m['artist_id']; ?>"><?php echo htmlspecialchars($m['name']); ?></option>
            <?php endwhile; ?>
        </select>

        <label for="brand_name">Nama Brand:</label>
        <input type="text" id="brand_name" name="brand_name" placeholder="Contoh: Dior, Chanel" required>

        <label for="title">Title/Peran:</label>
        <input type="text" id="title" name="title" placeholder="Contoh: Global Ambassador">

        <button type="submit">Simpan Data</button>
    </form>
    <a href="view.php" class="back-link">← Kembali</a>
</body>
</html>