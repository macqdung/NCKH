<?php
session_start();
include_once("../MODEL/modeladmin.php");
$admin = new data_admin();

// Chỉ cho phép nhân viên truy cập
if (!isset($_SESSION['user']) || ($_SESSION['role'] ?? '') !== 'nhanvien') {
    header("Location: dangnhap.php");
    exit;
}

// Xử lý cập nhật trạng thái đơn hàng
$message = '';
if (isset($_POST['update_status'])) {
    $order_id = intval($_POST['order_id']);
    $status = $_POST['status'];
    if ($admin->update_order_status($order_id, $status)) {
        $message = "Cập nhật trạng thái thành công!";
    } else {
        $message = "Cập nhật trạng thái thất bại!";
    }
}

$orders = $admin->select_all_orders();
$statuses = ['chờ xác nhận', 'đang vận chuyển', 'đã giao hàng thành công', 'đã hủy'];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý đơn hàng (Nhân viên)</title>
    <link rel="stylesheet" href="dinhdang.css">
</head>
<body>
    <h2>Quản lý đơn hàng</h2>
    <?php if ($message): ?>
        <div class="alert"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <table border="1" cellpadding="5">
        <tr>
            <th>ID</th><th>Khách hàng</th><th>Sản phẩm</th><th>Số lượng</th><th>Tổng tiền</th><th>Trạng thái</th><th>Cập nhật</th>
        </tr>
        <?php foreach ($orders as $o): ?>
        <tr>
            <td><?= $o['ID'] ?></td>
            <td><?= htmlspecialchars($o['tendangnhap'] ?? '') ?></td>
            <td><?= htmlspecialchars($o['tensanpham'] ?? '') ?></td>
            <td><?= $o['soluong'] ?></td>
            <td><?= number_format($o['tongtien'], 0, ',', '.') ?> 円</td>
            <td>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="order_id" value="<?= $o['ID'] ?>">
                    <select name="status">
                        <?php foreach ($statuses as $st): ?>
                        <option value="<?= $st ?>" <?= $o['trangthai']==$st?'selected':'' ?>><?= ucfirst($st) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" name="update_status">Lưu</button>
                </form>
            </td>
            <td>
                <!-- Có thể thêm nút xem chi tiết hoặc xóa đơn nếu cần -->
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    <p><a href="mqd.php">Quay lại trang chính</a></p>
</body>
</html>
