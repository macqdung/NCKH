<?php
include_once 'MODEL/connect.php';

global $conn;

// Check if admin account exists
$check_admin = $conn->prepare("SELECT ID_user, tendangnhap, role FROM users WHERE tendangnhap = ?");
$admin_username = 'macquangdung';
$check_admin->bind_param("s", $admin_username);
$check_admin->execute();
$result = $check_admin->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    echo "Admin account exists: ID=" . $user['ID_user'] . ", Username=" . $user['tendangnhap'] . ", Role=" . $user['role'] . "\n";
} else {
    echo "Admin account does not exist.\n";
}

$conn->close();
?>
