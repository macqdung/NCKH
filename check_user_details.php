<?php
include_once 'MODEL/connect.php';

global $conn;

$username = 'macquangdung';
$stmt = $conn->prepare("SELECT ID_user, tendangnhap, matkhau, role FROM users WHERE tendangnhap = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    echo "User details:\n";
    echo "ID: " . $user['ID_user'] . "\n";
    echo "Username: " . $user['tendangnhap'] . "\n";
    echo "Password hash: " . $user['matkhau'] . "\n";
    echo "Role: " . $user['role'] . "\n";
    
    // Check if password matches
    $password = '0106620006';
    if (password_verify($password, $user['matkhau'])) {
        echo "Password verification: SUCCESS\n";
    } else {
        echo "Password verification: FAILED\n";
    }
} else {
    echo "User not found.\n";
}

$conn->close();
?>
