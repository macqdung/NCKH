<?php
include_once 'MODEL/connect.php';

global $conn;

$password = '01062006';
$hash = password_hash($password, PASSWORD_DEFAULT);

$update_stmt = $conn->prepare("UPDATE users SET matkhau = ? WHERE tendangnhap = 'macquangdung'");
$update_stmt->bind_param("s", $hash);

if ($update_stmt->execute()) {
    echo "Password updated successfully for admin 'macquangdung'.\n";
    echo "New hash: $hash\n";
} else {
    echo "Error updating password: " . $conn->error . "\n";
}

$conn->close();
?>
