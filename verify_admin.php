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
    echo "Admin details:\n";
    echo "ID: " . $user['ID_user'] . "\n";
    echo "Username: " . $user['tendangnhap'] . "\n";
    echo "Role: " . $user['role'] . "\n";
    echo "Password hash: " . $user['matkhau'] . "\n";
    
    // Test password verification
    $test_password = '01062006';
    if (password_verify($test_password, $user['matkhau'])) {
        echo "Password '01062006' is correct.\n";
    } else {
        echo "Password '01062006' is incorrect.\n";
    }
} else {
    echo "Admin account not found.\n";
}

$conn->close();
?>
