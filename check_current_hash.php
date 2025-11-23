<?php
include_once 'MODEL/connect.php';

global $conn;

$check_stmt = $conn->prepare("SELECT matkhau FROM users WHERE tendangnhap = 'macquangdung'");
$check_stmt->execute();
$result = $check_stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    echo "Current hash for 'macquangdung': " . $user['matkhau'] . "\n";
} else {
    echo "Admin user not found.\n";
}

$conn->close();
?>
