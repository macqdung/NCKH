<?php
include 'MODEL/connect.php';
global $conn;

$sql = "CREATE TABLE IF NOT EXISTS users (
    ID_user INT AUTO_INCREMENT PRIMARY KEY,
    tendangnhap VARCHAR(255) UNIQUE NOT NULL,
    matkhau VARCHAR(255) NOT NULL,
    role VARCHAR(50) DEFAULT NULL
)";

if ($conn->query($sql) === TRUE) {
    echo "Users table created successfully";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
?>
