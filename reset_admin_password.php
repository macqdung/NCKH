<?php
include_once 'MODEL/connect.php';

global $conn;

$username = 'macquangdung';
$new_password = '01062006'; // Mật khẩu bạn muốn đặt
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
$role = 'admin';

// Kiểm tra xem tài khoản đã tồn tại chưa
$check_stmt = $conn->prepare("SELECT ID_user FROM users WHERE tendangnhap = ?");
$check_stmt->bind_param("s", $username);
$check_stmt->execute();
$result = $check_stmt->get_result();

if ($result->num_rows > 0) {
    // Nếu tài khoản tồn tại, cập nhật mật khẩu
    $update_stmt = $conn->prepare("UPDATE users SET matkhau = ? WHERE tendangnhap = ?");
    $update_stmt->bind_param("ss", $hashed_password, $username);
    if ($update_stmt->execute()) {
        echo "Mật khẩu cho tài khoản admin '{$username}' đã được cập nhật thành công.";
    } else {
        echo "Lỗi khi cập nhật mật khẩu: " . $conn->error;
    }
} else {
    // Nếu tài khoản không tồn tại, tạo tài khoản mới
    $insert_stmt = $conn->prepare("INSERT INTO users (tendangnhap, matkhau, role) VALUES (?, ?, ?)");
    $insert_stmt->bind_param("sss", $username, $hashed_password, $role);
    if ($insert_stmt->execute()) {
        echo "Tài khoản admin '{$username}' đã được tạo thành công với mật khẩu mặc định.";
    } else {
        echo "Lỗi khi tạo tài khoản admin: " . $conn->error;
    }
}

$conn->close();
?>