<?php
require_once __DIR__ . "/../connection.php";

// Data dikirim lewat POST dari JavaScript (Fetch)
if (isset($_POST['id'])) {
    $id = (int) $_POST['id']; // Casting ke integer biar aman
    
    $sql = "DELETE FROM tours WHERE tour_id = $id";
    
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