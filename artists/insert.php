<?php
require_once __DIR__ . "/../connection.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Ambil dan bersihkan data teks
    $name          = mysqli_real_escape_string($conn, $_POST['name']);
    $artist_type   = mysqli_real_escape_string($conn, $_POST['artist_type']);
    $full_name     = mysqli_real_escape_string($conn, $_POST['full_name']);
    $birth_date    = mysqli_real_escape_string($conn, $_POST['birth_date']);
    $birth_country = mysqli_real_escape_string($conn, $_POST['birth_country']);
    $position      = mysqli_real_escape_string($conn, $_POST['position']);
    $highlight     = mysqli_real_escape_string($conn, $_POST['highlight']);

    // 2. Validasi Foto (Meniru gaya insertnilai.php)
    $fotoOk = false;
    $ext = '';
    $foto_name = '';

    // Cek apakah ada file yang diupload dan tidak error
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        
        // Validasi: harus gambar asli, ekstensi diizinkan, ukuran <= 2MB
        if (
            getimagesize($_FILES['photo']['tmp_name']) !== false &&
            in_array($ext, $allowed) &&
            $_FILES['photo']['size'] <= 2000000
        ) {
            $fotoOk = true;
            // Buat nama file unik agar tidak bentrok dengan file lain
            $foto_name = uniqid('artist_') . '.' . $ext;
        }
    }

    // Jika user mencoba upload file tapi gagal validasi (bukan karena tidak pilih file)
    if (!$fotoOk && isset($_FILES['photo']) && $_FILES['photo']['error'] !== UPLOAD_ERR_NO_FILE) {
        echo "<script>alert('Foto tidak valid. Gunakan JPG/PNG/GIF, maksimal 2 MB.');history.back();</script>";
        exit;
    }

    // Tentukan path foto (NULL jika tidak ada foto)
    $photo_path_sql = $fotoOk ? "'assets/img/" . $foto_name . "'" : "NULL";

    // 3. Query INSERT
    $sql = "INSERT INTO artists (name, artist_type, full_name, birth_date, birth_country, position, highlight, photo_path) 
            VALUES ('$name', '$artist_type', '$full_name', '$birth_date', '$birth_country', '$position', '$highlight', $photo_path_sql)";

    // 4. Eksekusi Query
    if (mysqli_query($conn, $sql)) {
        // 5. Pindahkan file ke folder assets/img/ jika ada
        if ($fotoOk) {
            $dir = __DIR__ . "/../assets/img/";
            // Buat folder otomatis jika belum ada
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }
            move_uploaded_file($_FILES['photo']['tmp_name'], $dir . $foto_name);
        }
        
        echo "<script>alert('Data artis berhasil ditambahkan!');window.location.href='view.php';</script>";
        exit;
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
}
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Artis</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        form { max-width: 500px; }
        label { display: block; margin-top: 10px; font-weight: bold; }
        input[type="text"], input[type="date"], select, textarea { 
            width: 100%; padding: 8px; margin-top: 5px; box-sizing: border-box; 
        }
        button { margin-top: 15px; padding: 10px 15px; background-color: #007bff; color: white; border: none; cursor: pointer; }
        .back-link { display: inline-block; margin-top: 15px; text-decoration: none; color: #007bff; }
    </style>
</head>
<body>

    <h2>Tambah Artis Baru</h2>
    
    <!-- enctype="multipart/form-data" WAJIB ada untuk upload foto -->
    <form action="insert.php" method="POST" enctype="multipart/form-data">
        
        <label for="name">Nama Panggilan:</label>
        <input type="text" id="name" name="name" placeholder="Contoh: JENNIE, LISA, Bruno Mars" required>
        
        <label for="artist_type">Tipe Artis:</label>
        <select id="artist_type" name="artist_type" required>
            <option value="group">Group (BLACKPINK)</option>
            <option value="member">Member (JISOO, JENNIE, ROSÉ, LISA)</option>
            <option value="guest">Guest Artist (Kolaborasi)</option>
        </select>

        <label for="full_name">Nama Lengkap:</label>
        <input type="text" id="full_name" name="full_name" placeholder="Contoh: Jennie Kim">
        
        <label for="birth_date">Tanggal Lahir:</label>
        <input type="date" id="birth_date" name="birth_date">
        
        <label for="birth_country">Negara Asal:</label>
        <input type="text" id="birth_country" name="birth_country" placeholder="Contoh: South Korea">
        
        <label for="position">Posisi di Grup:</label>
        <input type="text" id="position" name="position" placeholder="Contoh: Main Rapper">
        
        <label for="highlight">Highlight/Info Tambahan:</label>
        <textarea id="highlight" name="highlight" rows="3" placeholder="Contoh: First member to debut solo"></textarea>
        
        <label for="photo">Foto Profil (Opsional):</label>
        <input type="file" id="photo" name="photo" accept="image/*">
        
        <button type="submit">Simpan Data</button>
    </form>

    <a href="view.php" class="back-link">← Kembali ke Daftar Data</a>

</body>
</html>