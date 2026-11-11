<?php
require_once __DIR__ . "/../connection.php";

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    header("Location: view.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password_input = $_POST['password'];

    if (empty($username) || empty($email)) {
        echo "<script>alert('Username dan Email wajib diisi!'); history.back();</script>";
        exit;
    }

    // Cek apakah user mengisi password baru atau tidak
    if (!empty($password_input)) {
        $hashed_password = password_hash($password_input, PASSWORD_DEFAULT);
        $sql = "UPDATE admin SET username='$username', email='$email', password='$hashed_password' WHERE admin_id=$id";
    } else {
        // Jika password kosong, jangan update kolom password
        $sql = "UPDATE admin SET username='$username', email='$email' WHERE admin_id=$id";
    }

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Data admin berhasil diupdate!'); window.location.href='view.php';</script>";
    } else {
        echo "<script>alert('Gagal update: Username/Email mungkin sudah dipakai. (" . mysqli_error($conn) . ")'); history.back();</script>";
    }
    exit;
}

$result = mysqli_query($conn, "SELECT * FROM admin WHERE admin_id = $id");
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
    <title>Edit Admin</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        form { max-width: 400px; }
        label { display: block; margin-top: 10px; font-weight: bold; }
        input[type="text"], input[type="email"], input[type="password"] { width: 100%; padding: 8px; margin-top: 5px; box-sizing: border-box; }
        button { margin-top: 15px; padding: 10px 15px; background-color: #ffc107; color: black; border: none; cursor: pointer; font-weight: bold; }
        .back-link { display: inline-block; margin-top: 15px; text-decoration: none; color: #007bff; }
        .hint { font-size: 12px; color: #666; font-weight: normal; margin-top: 2px; display: block;}
    </style>
</head>
<body>

    <h2>Edit Data Admin</h2>
    
    <form action="edit.php?id=<?php echo $id; ?>" method="POST">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($row['username'] ?? ''); ?>" required>
        
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($row['email'] ?? ''); ?>" required>
        
        <label for="password">Password Baru:</label>
        <input type="password" id="password" name="password">
        <span class="hint">* Kosongkan jika tidak ingin mengubah password.</span>
        
        <button type="submit">Update Data</button>
    </form>

    <a href="view.php" class="back-link">← Kembali ke Daftar Data</a>

</body>
</html>