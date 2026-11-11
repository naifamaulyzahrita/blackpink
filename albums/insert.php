<?php
require_once __DIR__ . "/../connection.php";

// Ambil data albums_type untuk dropdown
$types_query = mysqli_query($conn, "SELECT * FROM albums_type ORDER BY album_type ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Bersihkan input teks
    $album_type_id = (int) $_POST['album_type_id'];
    $title         = mysqli_real_escape_string($conn, $_POST['title']);
    $category      = mysqli_real_escape_string($conn, $_POST['category']);
    $release_date  = !empty($_POST['release_date']) ? mysqli_real_escape_string($conn, $_POST['release_date']) : null;
    $stream_url    = mysqli_real_escape_string($conn, $_POST['stream_url']);
    $buy_url       = mysqli_real_escape_string($conn, $_POST['buy_url']);

    // 2. Validasi Input Wajib
    if (empty($title) || empty($category) || $album_type_id <= 0) {
        echo "<script>alert('Judul, Kategori, dan Jenis Album wajib diisi!'); history.back();</script>";
        exit;
    }

    // 3. Validasi Upload Cover (Meniru gaya dosen)
    $coverOk = false;
    $ext = '';
    $cover_name = '';

    if (isset($_FILES['cover']) && $_FILES['cover']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (
            getimagesize($_FILES['cover']['tmp_name']) !== false &&
            in_array($ext, $allowed) &&
            $_FILES['cover']['size'] <= 2000000 // Maksimal 2MB
        ) {
            $coverOk = true;
            $cover_name = uniqid('cover_') . '.' . $ext;
        }
    }

    if (!$coverOk && isset($_FILES['cover']) && $_FILES['cover']['error'] !== UPLOAD_ERR_NO_FILE) {
        echo "<script>alert('Cover tidak valid. Gunakan JPG/PNG/GIF, maksimal 2 MB.'); history.back();</script>";
        exit;
    }

    // Tentukan path cover (NULL jika tidak ada)
    $cover_path_sql = $coverOk ? "'assets/img/" . $cover_name . "'" : "NULL";
    $release_sql    = $release_date ? "'$release_date'" : "NULL";
    $stream_sql     = !empty($stream_url) ? "'$stream_url'" : "NULL";
    $buy_sql        = !empty($buy_url) ? "'$buy_url'" : "NULL";

    // 4. Query INSERT
    $sql = "INSERT INTO albums (album_type_id, title, release_date, category, cover_path, stream_url, buy_url) 
            VALUES ($album_type_id, '$title', $release_sql, '$category', $cover_path_sql, $stream_sql, $buy_sql)";

    // 5. Eksekusi
    if (mysqli_query($conn, $sql)) {
        // Pindahkan file cover ke folder assets/img/
        if ($coverOk) {
            $dir = __DIR__ . "/../assets/img/";
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }
            move_uploaded_file($_FILES['cover']['tmp_name'], $dir . $cover_name);
        }
        
        echo "<script>alert('Data album berhasil ditambahkan!'); window.location.href='view.php';</script>";
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
    <title>Tambah Album</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        form { max-width: 500px; }
        label { display: block; margin-top: 10px; font-weight: bold; }
        input[type="text"], input[type="date"], select { width: 100%; padding: 8px; margin-top: 5px; box-sizing: border-box; }
        button { margin-top: 15px; padding: 10px 15px; background-color: #007bff; color: white; border: none; cursor: pointer; }
        .back-link { display: inline-block; margin-top: 15px; text-decoration: none; color: #007bff; }
    </style>
</head>
<body>

    <h2>Tambah Album Baru</h2>
    
    <form action="insert.php" method="POST" enctype="multipart/form-data">
        
        <label for="title">Judul Album:</label>
        <input type="text" id="title" name="title" placeholder="Contoh: BORN PINK, Ruby, APT." required>
        
        <label for="album_type_id">Jenis Album:</label>
        <select id="album_type_id" name="album_type_id" required>
            <option value="">-- Pilih Jenis --</option>
            <?php while ($type = mysqli_fetch_assoc($types_query)): ?>
                <option value="<?php echo $type['album_type_id']; ?>">
                    <?php echo htmlspecialchars($type['album_type']); ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label for="category">Kategori:</label>
        <select id="category" name="category" required>
            <option value="group">Group Album</option>
            <option value="solo">Solo Release</option>
            <option value="japanese">Japanese Release</option>
            <option value="collaboration">Collaboration</option>
        </select>

        <label for="release_date">Tanggal Rilis:</label>
        <input type="date" id="release_date" name="release_date">
        
        <label for="cover">Cover Album (Opsional):</label>
        <input type="file" id="cover" name="cover" accept="image/*">

        <label for="stream_url">Stream URL (Spotify/YouTube):</label>
        <input type="text" id="stream_url" name="stream_url" placeholder="https://open.spotify.com/...">

        <label for="buy_url">Buy URL (Shop):</label>
        <input type="text" id="buy_url" name="buy_url" placeholder="https://shop.blackpinkmusic.com/...">
        
        <button type="submit">Simpan Data</button>
    </form>

    <a href="view.php" class="back-link">← Kembali ke Daftar Data</a>

</body>
</html>