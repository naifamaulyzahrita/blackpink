<?php
require_once __DIR__ . "/../connection.php";

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    header("Location: view.php");
    exit;
}

// Ambil data member untuk dropdown
$members_query = mysqli_query($conn, "SELECT artist_id, name FROM artists WHERE artist_type = 'member' ORDER BY name ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $artist_id  = (int) $_POST['artist_id'];
    $brand_name = mysqli_real_escape_string($conn, $_POST['brand_name']);
    $title      = !empty($_POST['title']) ? "'" . mysqli_real_escape_string($conn, $_POST['title']) . "'" : "NULL";

    if ($artist_id <= 0 || empty($brand_name)) {
        echo "<script>alert('Member dan Brand wajib diisi!'); history.back();</script>";
        exit;
    }

    $sql = "UPDATE member_endorsements SET 
            artist_id = $artist_id, 
            brand_name = '$brand_name', 
            title = $title 
            WHERE endorsement_id = $id";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Data endorsement berhasil diupdate!'); window.location.href='view.php';</script>";
    } else {
        echo "<script>alert('Gagal update: " . mysqli_error($conn) . "'); history.back();</script>";
    }
    exit;
}

// Ambil data lama
$result = mysqli_query($conn, "SELECT * FROM member_endorsements WHERE endorsement_id = $id");
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
    <title>Edit Endorsement</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        form { max-width: 400px; }
        label { display: block; margin-top: 10px; font-weight: bold; }
        input[type="text"], select { width: 100%; padding: 8px; margin-top: 5px; box-sizing: border-box; }
        button { margin-top: 15px; padding: 10px 15px; background-color: #ffc107; color: black; border: none; cursor: pointer; font-weight: bold; }
        .back-link { display: inline-block; margin-top: 15px; text-decoration: none; color: #007bff; }
    </style>
</head>
<body>

    <h2>Edit Data Endorsement</h2>
    
    <form action="edit.php?id=<?php echo $id; ?>" method="POST">
        <label for="artist_id">Pilih Member:</label>
        <select id="artist_id" name="artist_id" required>
            <option value="">-- Pilih Member --</option>
            <?php while ($m = mysqli_fetch_assoc($members_query)): ?>
                <!-- Cek apakah member ini yang sedang dipilih -->
                <option value="<?php echo $m['artist_id']; ?>" <?php echo ($m['artist_id'] == $row['artist_id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($m['name']); ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label for="brand_name">Nama Brand:</label>
        <input type="text" id="brand_name" name="brand_name" value="<?php echo htmlspecialchars($row['brand_name'] ?? ''); ?>" required>

        <label for="title">Title/Peran:</label>
        <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($row['title'] ?? ''); ?>">

        <button type="submit">Update Data</button>
    </form>

    <a href="view.php" class="back-link">← Kembali ke Daftar Data</a>

</body>
</html>