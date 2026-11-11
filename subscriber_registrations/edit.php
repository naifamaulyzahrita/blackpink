<?php
require_once __DIR__ . "/../connection.php";

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    header("Location: view.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email     = mysqli_real_escape_string($conn, $_POST['email']);
    $full_name = !empty($_POST['full_name']) ? "'" . mysqli_real_escape_string($conn, $_POST['full_name']) . "'" : "NULL";

    if (empty($email)) {
        echo "<script>alert('Email wajib diisi!'); history.back();</script>";
        exit;
    }

    $sql = "UPDATE subscriber_registrations SET 
            email = '$email', 
            full_name = $full_name 
            WHERE registration_id = $id";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Data subscriber berhasil diupdate!'); window.location.href='view.php';</script>";
    } else {
        echo "<script>alert('Gagal update: " . mysqli_error($conn) . "'); history.back();</script>";
    }
    exit;
}

$result = mysqli_query($conn, "SELECT * FROM subscriber_registrations WHERE registration_id = $id");
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
    <title>Edit Subscriber</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        form { max-width: 400px; }
        label { display: block; margin-top: 10px; font-weight: bold; }
        input[type="email"], input[type="text"] { width: 100%; padding: 8px; margin-top: 5px; box-sizing: border-box; }
        button { margin-top: 15px; padding: 10px 15px; background-color: #ffc107; color: black; border: none; cursor: pointer; font-weight: bold; }
        .back-link { display: inline-block; margin-top: 15px; text-decoration: none; color: #007bff; }
    </style>
</head>
<body>

    <h2>Edit Data Subscriber</h2>
    
    <form action="edit.php?id=<?php echo $id; ?>" method="POST">
        <label for="email">Alamat Email:</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($row['email'] ?? ''); ?>" required>
        
        <label for="full_name">Nama Lengkap:</label>
        <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($row['full_name'] ?? ''); ?>">
        
        <button type="submit">Update Data</button>
    </form>

    <a href="view.php" class="back-link">← Kembali ke Daftar Data</a>

</body>
</html>