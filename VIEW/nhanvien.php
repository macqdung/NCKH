<?php
session_start();
include_once("../MODEL/modeladmin.php");
$admin = new data_admin();

// Chỉ cho tài khoản hieu đăng nhập vào trang nhân viên
if (!isset($_SESSION['user']) || $_SESSION['user'] !== 'hieu') {
    header("Location: dangnhap.php");
    exit;
}

// Xử lý thêm nhân viên
$message = '';
if (isset($_POST['add_staff'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $fullname = trim($_POST['fullname']);
    $sdt = trim($_POST['sdt']);
    $role = $_POST['role'];
    if ($admin->add_staff($username, $password, $fullname, $sdt, $role)) {
        $message = "Thêm nhân viên thành công!";
    } else {
        $message = "Thêm nhân viên thất bại (trùng username?)";
    }
}

// Xử lý xóa nhân viên
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    if ($admin->delete_staff(intval($_GET['delete']))) {
        $message = "Xóa nhân viên thành công!";
    } else {
        $message = "Xóa nhân viên thất bại!";
    }
}

// Xử lý phân quyền
if (isset($_POST['update_role'])) {
    $id = intval($_POST['user_id']);
    $role = $_POST['role'];
    if ($admin->update_staff_role($id, $role)) {
        $message = "Cập nhật quyền thành công!";
    } else {
        $message = "Cập nhật quyền thất bại!";
    }
}

$staffs = $admin->get_all_staff();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý nhân viên</title>
    <link rel="stylesheet" href="dinhdang.css">
</head>
<body>
    <h2>Quản lý nhân viên</h2>
    <?php if ($message): ?>
        <div class="alert"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <form method="post">
        <h3>Thêm nhân viên mới</h3>
        <input type="text" name="username" placeholder="Tên đăng nhập" required>
        <input type="password" name="password" placeholder="Mật khẩu" required>
        <input type="text" name="fullname" placeholder="Họ tên" required>
        <input type="text" name="sdt" placeholder="Số điện thoại" required>
        <select name="role">
            <option value="nhanvien">Nhân viên</option>
            <option value="admin">Admin</option>
        </select>
        <button type="submit" name="add_staff">Thêm nhân viên</button>
    </form>
    <h3>Danh sách nhân viên</h3>
    <table border="1" cellpadding="5">
        <tr>
            <th>ID</th><th>Tên đăng nhập</th><th>Họ tên</th><th>Quyền</th><th>Thao tác</th>
        </tr>
        <?php foreach ($staffs as $s): ?>
        <tr>
            <td><?= $s['ID_user'] ?></td>
            <td><?= htmlspecialchars($s['tendangnhap']) ?></td>
            <td><?= htmlspecialchars($s['hoten']) ?></td>
            <td>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="user_id" value="<?= $s['ID_user'] ?>">
                    <select name="role">
                        <option value="nhanvien" <?= $s['role']=='nhanvien'?'selected':'' ?>>Nhân viên</option>
                        <option value="admin" <?= $s['role']=='admin'?'selected':'' ?>>Admin</option>
                    </select>
                    <button type="submit" name="update_role">Cập nhật</button>
                </form>
            </td>
            <td>
                <?php if ($s['role'] != 'admin'): ?>
                <a href="?delete=<?= $s['ID_user'] ?>" onclick="return confirm('Xóa nhân viên này?')">Xóa</a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    <p><a href="admin.php">Quay lại trang admin</a></p>
</body>
</html>
