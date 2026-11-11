<?php
require_once __DIR__ . "/../connection.php";

if (isset($_POST['id'])) {
    $id = (int) $_POST['id'];
    
    // Hapus file fisik dari folder (opsional tapi bagus)
    $res = mysqli_query($conn, "SELECT file_path FROM gallery WHERE media_id = $id");
    if ($row = mysqli_fetch_assoc($res)) {
        $path = __DIR__ . "/../" . $row['file_path'];
        if (file_exists($path)) unlink($path);
    }

    $sql = "DELETE FROM gallery WHERE media_id = $id";
    
    if (mysqli_query($conn, $sql)) {
        echo "success";
    } else {
        http_response_code(500);
        echo "Error: " . mysqli_error($conn);
    }
} else {
    http_response_code(400);
    echo "ID tidak ditemukan";
}
mysqli_close($conn);
?>