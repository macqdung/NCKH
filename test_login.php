<?php
include_once 'MODEL/connect.php';
include_once 'MODEL/modeldangnhap.php';

$user_login = new data_user_login();
$user = $user_login->get_user_by_username('macquangdung');

if ($user) {
    echo "User found: " . $user['tendangnhap'] . "\n";
    echo "Password hash: " . $user['matkhau'] . "\n";
    echo "Role: " . $user['role'] . "\n";

    $password = '01062006';
    if (password_verify($password, $user['matkhau'])) {
        echo "Password verification successful.\n";
    } else {
        echo "Password verification failed.\n";
    }
} else {
    echo "User not found.\n";
}
?>
