<?php
include 'MODEL/connect.php';
global $conn;

$result = $conn->query('SHOW TABLES LIKE "user_points"');
if ($result->num_rows > 0) {
    echo 'Table exists';
} else {
    echo 'Table does not exist';
}

$conn->close();
?>
