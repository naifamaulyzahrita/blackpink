<?php
require_once __DIR__ . "/../connection.php";

if (isset($_POST['id'])) {
    $id = (int) $_POST['id'];
    
    $sql = "DELETE FROM artists WHERE artist_id = $id";
    
    if (mysqli_query($conn, $sql)) {
        echo "success";
    } else {
        http_response_code(500);
        echo "Error deleting record: " . mysqli_error($conn);
    }
} else {
    http_response_code(400);
    echo "ID tidak ditemukan";
}
mysqli_close($conn);
?>