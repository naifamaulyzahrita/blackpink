<?php
require_once __DIR__ . "/../connection.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['album_id']) && isset($_POST['artist_id'])) {
    $album_id = (int) $_POST['album_id'];
    $artist_id = (int) $_POST['artist_id'];
    
    $sql = "DELETE FROM albums_artists WHERE album_id = $album_id AND artist_id = $artist_id";
    
    if (mysqli_query($conn, $sql)) {
        echo "success";
    } else {
        http_response_code(500);
        echo "Error: " . mysqli_error($conn);
    }
} else {
    http_response_code(400);
    echo "Data tidak lengkap";
}
mysqli_close($conn);
?>