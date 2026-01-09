<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: dangnhap.php");
    exit;
}
if (!isset($_GET['order_id']) || !is_numeric($_GET['order_id'])) {
    echo "Thiếu mã đơn hàng!";
    exit;
}
$order_id = intval($_GET['order_id']);
include('../MODEL/modelmh.php');
include('../MODEL/modeldanhgia.php');
$get_data = new data_muahang();
$review_data = new data_danhgia();
$order = $get_data->select_order_by_id($order_id);
if (!$order) {
    echo "Không tìm thấy hóa đơn!";
    exit;
}
$username = $_SESSION['user'];
$product_id = $order['ID_sanpham'];
$review = $review_data->getReviewByProductAndUser($product_id, $username);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hóa đơn - Đơn hàng #<?= htmlspecialchars($order['id']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .details { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .total { font-weight: bold; }
        .product-img { width: 80px; height: 80px; object-fit: cover; border-radius: 8px; }
        .review { margin-top: 30px; border-top: 1px solid #ccc; padding-top: 15px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Bakery Shop</h1>
        <p>Hóa đơn mua hàng</p>
    </div>
    <div class="details">
        <p><strong>ID Đơn hàng:</strong> <?= htmlspecialchars($order['id']) ?></p>
        <p><strong>Ngày đặt:</strong> <?= htmlspecialchars($order['created_at'] ?? '') ?></p>
        <p><strong>Ngày giao thành công:</strong> <?= htmlspecialchars($order['delivered_at'] ?? '') ?></p>
    </div>
    <table>
        <thead>
            <tr>
                <th>Ảnh</th>
                <th>Sản phẩm</th>
                <th>Số lượng</th>
                <th>Đơn giá</th>
                <th>Tổng tiền</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><img src="../media/<?= htmlspecialchars($order['hinhanh']) ?>" class="product-img" alt="<?= htmlspecialchars($order['tensanpham']) ?>"></td>
                <td><?= htmlspecialchars($order['tensanpham']) ?></td>
                <td><?= htmlspecialchars($order['soluong']) ?></td>
                <td><?= number_format($order['dongia'], 0, ',', '.') ?> 円</td>
                <td><?= number_format($order['tongtien'], 0, ',', '.') ?> 円</td>
            </tr>
        </tbody>
    </table>
    <div class="review">
        <h4>Đánh giá của khách hàng</h4>
        <?php if ($review): ?>
            <div>
                <strong>Điểm: </strong> <?= $review['rating'] ?>/5<br>
                <strong>Nhận xét:</strong> <?= htmlspecialchars($review['comment']) ?><br>
                <small><i>Ngày đánh giá: <?= htmlspecialchars($review['created_at']) ?></i></small>
            </div>
        <?php else: ?>
            <span>Chưa có đánh giá cho sản phẩm này.</span>
        <?php endif; ?>
    </div>
    <p class="total" style="text-align: right; margin-top: 20px;">Cảm ơn quý khách đã mua hàng!</p>
    <div class="text-center mt-4">
        <a href="lichsumuahang.php" class="btn btn-secondary">Quay lại lịch sử mua hàng</a>
        <button onclick="window.print()" class="btn btn-primary">In hóa đơn</button>
    </div>
</body>
</html>
