<?php
session_start();
include_once '../MODEL/modeldangnhap.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tendangnhap = trim($_POST['tendangnhap']);
    $matkhau = trim($_POST['matkhau']);

    if (!empty($tendangnhap) && !empty($matkhau)) {
        $user_login = new data_user_login();
        $user = $user_login->get_user_by_username($tendangnhap);

        if ($user) {
            if (password_verify($matkhau, $user['matkhau'])) {
                $_SESSION['user'] = $user['tendangnhap'];
                $_SESSION['ID_user'] = $user['ID_user']; // Sửa 'user_id' thành 'ID_user' cho nhất quán
                $_SESSION['role'] = $user['role'];

                // Redirect based on role
                if ($user['role'] == 'admin') {
                    $_SESSION['admin'] = $user['tendangnhap']; // Thêm session cho admin
                    header("Location: ../VIEW/admin.php"); // Chuyển hướng admin đến trang quản trị
                    exit();
                } elseif ($user['role'] == 'nhanvien') {
                    header("Location: ../VIEW/nhanvien.php");
                    exit();
                } else {
                    header("Location: ../VIEW/mqd.php");
                    exit();
                }
            } else {
                header("Location: ../VIEW/dangnhap.php?error=パスワードが間違っています");
                exit();
            }
        } else {
            header("Location: ../VIEW/dangnhap.php?error=invalid_user");
            exit();
        }
    } else {
        header("Location: ../VIEW/dangnhap.php?error=すべてのフィールドを入力してください");
        exit();
    }
} else {
    header("Location: ../VIEW/dangnhap.php");
    exit();
}
?>
