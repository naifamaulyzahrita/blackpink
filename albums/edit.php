<?php
require_once __DIR__ . "/../connection.php";

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) { header("Location: view.php"); exit; }

// Ambil data jenis album untuk dropdown
$types_query = mysqli_query($conn, "SELECT * FROM albums_type ORDER BY album_type ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $album_type_id = (int) $_POST['album_type_id'];
    $title         = mysqli_real_escape_string($conn, $_POST['title']);
    $category      = mysqli_real_escape_string($conn, $_POST['category']);
    $release_date  = !empty($_POST['release_date']) ? "'" . mysqli_real_escape_string($conn, $_POST['release_date']) . "'" : "NULL";
    $stream_url    = !empty($_POST['stream_url']) ? "'" . mysqli_real_escape_string($conn, $_POST['stream_url']) . "'" : "NULL";
    $buy_url       = !empty($_POST['buy_url']) ? "'" . mysqli_real_escape_string($conn, $_POST['buy_url']) . "'" : "NULL";

    // Logika Upload Cover (Opsional saat edit)
    $coverOk = false;
    $ext = '';
    $cover_name = '';

    if (isset($_FILES['cover']) && $_FILES['cover']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (getimagesize($_FILES['cover']['tmp_name']) !== false && in_array($ext, $allowed) && $_FILES['cover']['size'] <= 2000000) {
            $coverOk = true;
            $cover_name = uniqid('cover_') . '.' . $ext;
        }
    }

    if (!$coverOk && isset($_FILES['cover']) && $_FILES['cover']['error'] !== UPLOAD_ERR_NO_FILE) {
        echo "<script>alert('Cover tidak valid. Gunakan JPG/PNG/GIF, maksimal 2 MB.'); history.back();</script>";
        exit;
    }

    // Tentukan path cover untuk query UPDATE
    if ($coverOk) {
        $cover_path_sql = "'assets/img/" . $cover_name . "'";
    } else {
        // Jika tidak ada cover baru, biarkan kolom cover_path tetap seperti semula
        $cover_path_sql = "cover_path"; 
    }

    $sql = "UPDATE albums SET 
            album_type_id = $album_type_id, 
            title = '$title', 
            release_date = $release_date, 
            category = '$category', 
            cover_path = $cover_path_sql, 
            stream_url = $stream_url, 
            buy_url = $buy_url 
            WHERE album_id = $id";

    if (mysqli_query($conn, $sql)) {
        if ($coverOk) {
            $dir = __DIR__ . "/../assets/img/";
            if (!is_dir($dir)) mkdir($dir, 0777, true);
            move_uploaded_file($_FILES['cover']['tmp_name'], $dir . $cover_name);
        }
        echo "<script>alert('Data album berhasil diupdate!'); window.location.href='view.php';</script>";
    } else {
        echo "<script>alert('Gagal update: " . mysqli_error($conn) . "'); history.back();</script>";
    }
    exit;
}

// Ambil data lama
$row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM albums WHERE album_id = $id"));
if (!$row) { header("Location: view.php"); exit; }
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Album</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        form { max-width: 500px; }
        label { display: block; margin-top: 10px; font-weight: bold; }
        input[type="text"], input[type="date"], select { width: 100%; padding: 8px; margin-top: 5px; box-sizing: border-box; }
        button { margin-top: 15px; padding: 10px 15px; background-color: #ffc107; color: black; border: none; cursor: pointer; font-weight: bold; }
        .back-link { display: inline-block; margin-top: 15px; text-decoration: none; color: #007bff; }
        .current-cover { margin-top: 5px; }
        .current-cover img { width: 60px; height: 60px; object-fit: cover; border-radius: 4px; }
    </style>
</head>
<body>
    <h2>Edit Data Album</h2>
    <form action="edit.php?id=<?php echo $id; ?>" method="POST" enctype="multipart/form-data">
        <label for="title">Judul Album:</label>
        <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($row['title'] ?? ''); ?>" required>
        
        <label for="album_type_id">Jenis Album:</label>
        <select id="album_type_id" name="album_type_id" required>
            <option value="">-- Pilih Jenis --</option>
            <?php while ($type = mysqli_fetch_assoc($types_query)): ?>
                <option value="<?php echo $type['album_type_id']; ?>" <?php echo ($type['album_type_id'] == $row['album_type_id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($type['album_type']); ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label for="category">Kategori:</label>
        <select id="category" name="category" required>
            <option value="group" <?php echo ($row['category'] == 'group') ? 'selected' : ''; ?>>Group Album</option>
            <option value="solo" <?php echo ($row['category'] == 'solo') ? 'selected' : ''; ?>>Solo Release</option>
            <option value="japanese" <?php echo ($row['category'] == 'japanese') ? 'selected' : ''; ?>>Japanese Release</option>
            <option value="collaboration" <?php echo ($row['category'] == 'collaboration') ? 'selected' : ''; ?>>Collaboration</option>
        </select>

        <label for="release_date">Tanggal Rilis:</label>
        <input type="date" id="release_date" name="release_date" value="<?php echo htmlspecialchars($row['release_date'] ?? ''); ?>">
        
        <label>Cover Saat Ini:</label>
        <div class="current-cover">
            <?php if (!empty($row['cover_path'])): ?>
                <img src="../<?php echo htmlspecialchars($row['cover_path']); ?>" alt="Cover Lama">
            <?php else: ?>
                <em>Tidak ada cover</em>
            <?php endif; ?>
        </div>

        <label for="cover">Ganti Cover (Opsional):</label>
        <input type="file" id="cover" name="cover" accept="image/*">

        <label for="stream_url">Stream URL:</label>
        <input type="text" id="stream_url" name="stream_url" value="<?php echo htmlspecialchars($row['stream_url'] ?? ''); ?>">

        <label for="buy_url">Buy URL:</label>
        <input type="text" id="buy_url" name="buy_url" value="<?php echo htmlspecialchars($row['buy_url'] ?? ''); ?>">
        
        <button type="submit">Update Data</button>
    </form>
    <a href="view.php" class="back-link">← Kembali ke Daftar Data</a>
</body>
</html>