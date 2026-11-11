<?php
require_once __DIR__ . "/../connection.php";

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    header("Location: view.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Ambil data teks
    $name          = mysqli_real_escape_string($conn, $_POST['name']);
    $artist_type   = mysqli_real_escape_string($conn, $_POST['artist_type']);
    $full_name     = !empty($_POST['full_name']) ? "'" . mysqli_real_escape_string($conn, $_POST['full_name']) . "'" : "NULL";
    $birth_date    = !empty($_POST['birth_date']) ? "'" . mysqli_real_escape_string($conn, $_POST['birth_date']) . "'" : "NULL";
    $birth_country = !empty($_POST['birth_country']) ? "'" . mysqli_real_escape_string($conn, $_POST['birth_country']) . "'" : "NULL";
    $position      = !empty($_POST['position']) ? "'" . mysqli_real_escape_string($conn, $_POST['position']) . "'" : "NULL";
    $highlight     = !empty($_POST['highlight']) ? "'" . mysqli_real_escape_string($conn, $_POST['highlight']) . "'" : "NULL";

    // 2. Logika Upload Foto (Opsional saat edit)
    $fotoOk = false;
    $ext = '';
    $foto_name = '';

    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (
            getimagesize($_FILES['photo']['tmp_name']) !== false &&
            in_array($ext, $allowed) &&
            $_FILES['photo']['size'] <= 2000000
        ) {
            $fotoOk = true;
            $foto_name = uniqid('artist_') . '.' . $ext;
        }
    }

    // Jika user coba upload tapi gagal validasi
    if (!$fotoOk && isset($_FILES['photo']) && $_FILES['photo']['error'] !== UPLOAD_ERR_NO_FILE) {
        echo "<script>alert('Foto tidak valid. Gunakan JPG/PNG/GIF, maksimal 2 MB.'); history.back();</script>";
        exit;
    }

    // Tentukan path foto untuk query UPDATE
    if ($fotoOk) {
        $photo_path_sql = "'assets/img/" . $foto_name . "'";
    } else {
        // Jika tidak ada foto baru, biarkan kolom photo_path tetap seperti semula
        $photo_path_sql = "photo_path"; 
    }

    // 3. Query UPDATE
    $sql = "UPDATE artists SET 
            name = '$name', 
            artist_type = '$artist_type', 
            full_name = $full_name, 
            birth_date = $birth_date, 
            birth_country = $birth_country, 
            position = $position, 
            highlight = $highlight, 
            photo_path = $photo_path_sql 
            WHERE artist_id = $id";

    if (mysqli_query($conn, $sql)) {
        // Pindahkan file jika ada foto baru
        if ($fotoOk) {
            $dir = __DIR__ . "/../assets/img/";
            if (!is_dir($dir)) mkdir($dir, 0777, true);
            move_uploaded_file($_FILES['photo']['tmp_name'], $dir . $foto_name);
        }
        
        echo "<script>alert('Data artis berhasil diupdate!'); window.location.href='view.php';</script>";
    } else {
        echo "<script>alert('Gagal update: " . mysqli_error($conn) . "'); history.back();</script>";
    }
    exit;
}

// Ambil data lama
$result = mysqli_query($conn, "SELECT * FROM artists WHERE artist_id = $id");
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
    <title>Edit Artis</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        form { max-width: 500px; }
        label { display: block; margin-top: 10px; font-weight: bold; }
        input[type="text"], input[type="date"], select, textarea { width: 100%; padding: 8px; margin-top: 5px; box-sizing: border-box; }
        button { margin-top: 15px; padding: 10px 15px; background-color: #ffc107; color: black; border: none; cursor: pointer; font-weight: bold; }
        .back-link { display: inline-block; margin-top: 15px; text-decoration: none; color: #007bff; }
        .current-photo { margin-top: 5px; }
        .current-photo img { width: 60px; height: 60px; object-fit: cover; border-radius: 50%; }
    </style>
</head>
<body>

    <h2>Edit Data Artis</h2>
    
    <form action="edit.php?id=<?php echo $id; ?>" method="POST" enctype="multipart/form-data">
        <label for="name">Nama Panggilan:</label>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($row['name'] ?? ''); ?>" required>
        
        <label for="artist_type">Tipe Artis:</label>
        <select id="artist_type" name="artist_type" required>
            <option value="group" <?php echo ($row['artist_type'] == 'group') ? 'selected' : ''; ?>>Group</option>
            <option value="member" <?php echo ($row['artist_type'] == 'member') ? 'selected' : ''; ?>>Member</option>
            <option value="guest" <?php echo ($row['artist_type'] == 'guest') ? 'selected' : ''; ?>>Guest Artist</option>
        </select>

        <label for="full_name">Nama Lengkap:</label>
        <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($row['full_name'] ?? ''); ?>">
        
        <label for="birth_date">Tanggal Lahir:</label>
        <input type="date" id="birth_date" name="birth_date" value="<?php echo htmlspecialchars($row['birth_date'] ?? ''); ?>">
        
        <label for="birth_country">Negara Asal:</label>
        <input type="text" id="birth_country" name="birth_country" value="<?php echo htmlspecialchars($row['birth_country'] ?? ''); ?>">
        
        <label for="position">Posisi di Grup:</label>
        <input type="text" id="position" name="position" value="<?php echo htmlspecialchars($row['position'] ?? ''); ?>">
        
        <label for="highlight">Highlight/Info Tambahan:</label>
        <textarea id="highlight" name="highlight" rows="3"><?php echo htmlspecialchars($row['highlight'] ?? ''); ?></textarea>
        
        <label>Foto Saat Ini:</label>
        <div class="current-photo">
            <?php if (!empty($row['photo_path'])): ?>
                <img src="../<?php echo htmlspecialchars($row['photo_path']); ?>" alt="Foto Lama">
            <?php else: ?>
                <em>Tidak ada foto</em>
            <?php endif; ?>
        </div>

        <label for="photo">Ganti Foto (Opsional):</label>
        <input type="file" id="photo" name="photo" accept="image/*">
        
        <button type="submit">Update Data</button>
    </form>

    <a href="view.php" class="back-link">← Kembali ke Daftar Data</a>

</body>
</html>