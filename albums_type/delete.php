<?php
require_once __DIR__ . "/../connection.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = (int) $_POST['id'];
    
    $sql = "DELETE FROM albums_type WHERE album_type_id = $id";
    
    if (mysqli_query($conn, $sql)) {
        echo "success"; // Respon untuk AJAX / JavaScript
    } else {
        http_response_code(500);
        echo "Error deleting record: " . mysqli_error($conn);
    }
}
mysqli_close($conn);
?>