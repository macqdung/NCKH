<?php
include_once 'MODEL/connect.php';

global $conn;

$username = 'macquangdung';
$password = '01062006';
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Check if admin already exists
$check_stmt = $conn->prepare("SELECT ID_user FROM users WHERE tendangnhap = ?");
$check_stmt->bind_param("s", $username);
$check_stmt->execute();
$result = $check_stmt->get_result();

if ($result->num_rows > 0) {
    echo "Admin account already exists.\n";
} else {
    // Insert new admin
    $insert_stmt = $conn->prepare("INSERT INTO users (tendangnhap, matkhau, role) VALUES (?, ?, 'admin')");
    $insert_stmt->bind_param("ss", $username, $hashed_password);
    
    if ($insert_stmt->execute()) {
        echo "New admin account created successfully.\n";
    } else {
        echo "Error creating admin account: " . $conn->error . "\n";
    }
}

$conn->close();
?>
