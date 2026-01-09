<?php
include_once 'MODEL/connect.php';

global $conn;

$password = '01062006';
$real_hash = password_hash($password, PASSWORD_DEFAULT);

echo "Real hash for '$password': $real_hash\n";

$update_stmt = $conn->prepare("UPDATE users SET matkhau = ? WHERE tendangnhap = 'macquangdung'");
$update_stmt->bind_param("s", $real_hash);

if ($update_stmt->execute()) {
    echo "Password updated successfully for admin 'macquangdung'.\n";
} else {
    echo "Error updating password: " . $conn->error . "\n";
}

$conn->close();
?>
