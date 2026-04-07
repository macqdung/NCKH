<?php
session_start();
include_once('../MODEL/modelmh.php');

if (!isset($_SESSION['user'])) {
    header("Location: dangnhap.php");
    exit;
}

$id_user = isset($_SESSION['ID_user']) ? $_SESSION['ID_user'] : 0;
$data_mh = new data_muahang();
$orders = $data_mh->select_muahang_by_user($id_user);

// Group orders by ID because select_muahang_by_user returns one row per item
$grouped_orders = [];
foreach ($orders as $row) {
    $order_id = $row['id'];
    if (!isset($grouped_orders[$order_id])) {
        $grouped_orders[$order_id] = [
            'info' => [
                'id' => $row['id'],
                'created_at' => $row['created_at'],
                'tongtien' => $row['tongtien'],
                'trangthai' => $row['trangthai'],
                'payment_method' => isset($row['payment_method']) ? $row['payment_method'] : 'COD',
                'payment_status' => isset($row['payment_status']) ? $row['payment_status'] : 'pending'
            ],
            'items' => []
        ];
    }
    $grouped_orders[$order_id]['items'][] = $row;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lịch sử mua hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="luoi.css">
    <link rel="stylesheet" type="text/css" href="dinhdang.css">
    <link rel="stylesheet" type="text/css" href="dinhdangmenu.css">
    <style>
        body { background-color: #ffffff; color: #000000; font-family: 'Helvetica Neue', Arial, sans-serif; }
        .order-card { border: 1px solid #ddd; margin-bottom: 20px; border-radius: 8px; overflow: hidden; }
        .order-header { background-color: #f8f9fa; padding: 10px 15px; border-bottom: 1px solid #ddd; display: flex; justify-content: space-between; align-items: center; }
        .order-body { padding: 15px; }
        .product-item { display: flex; margin-bottom: 10px; border-bottom: 1px solid #eee; padding-bottom: 10px; }
        .product-item:last-child { border-bottom: none; }
        .product-img { width: 80px; height: 80px; object-fit: cover; margin-right: 15px; border-radius: 5px; }
        .badge-status { font-size: 0.9em; }
    </style>
</head>
<body>
    <?php include('menu.php'); ?>
    <div class="container my-5">
        <h2 class="mb-4 text-center">Lịch sử mua hàng</h2>
        <?php if (isset($_GET['message'])): ?>
            <div class="alert alert-info"><?php echo htmlspecialchars($_GET['message']); ?></div>
        <?php endif; ?>
        <?php if (empty($grouped_orders)): ?>
            <div class="alert alert-warning text-center">Bạn chưa có đơn hàng nào. <a href="mqd.php">Mua sắm ngay</a></div>
        <?php else: ?>
            <?php foreach ($grouped_orders as $order_id => $order): ?>
                <div class="order-card shadow-sm">
                    <div class="order-header">
                        <div>
                            <strong>Đơn hàng #<?php echo $order_id; ?></strong>
                            <span class="text-muted ms-2" style="font-size: 0.9em;">
                                <?php echo date('d/m/Y H:i', strtotime($order['info']['created_at'])); ?>
                            </span>
                        </div>
                        <div>
                            <?php 
                                $status_class = 'bg-secondary';
                                if ($order['info']['trangthai'] == 'đã giao hàng thành công') $status_class = 'bg-success';
                                elseif ($order['info']['trangthai'] == 'đã hủy') $status_class = 'bg-danger';
                                elseif ($order['info']['trangthai'] == 'chờ xác nhận') $status_class = 'bg-warning text-dark';
                            ?>
                            <span class="badge <?php echo $status_class; ?> badge-status">
                                <?php echo ucfirst($order['info']['trangthai']); ?>
                            </span>
                        </div>
                    </div>
                    <div class="order-body">
                        <!-- Danh sách sản phẩm -->
                        <?php foreach ($order['items'] as $item): ?>
                            <div class="product-item">
                                <img src="../media/<?php echo $item['hinhanh']; ?>" alt="<?php echo $item['tensanpham']; ?>" class="product-img">
                                <div>
                                    <h6 class="mb-1"><?php echo $item['tensanpham']; ?></h6>
                                    <p class="mb-0 text-muted">
                                        <?php echo number_format($item['dongia'], 0, ',', '.'); ?> đ x <?php echo $item['soluong']; ?>
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <hr>
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Phương thức thanh toán:</strong> 
                                    <?php 
                                        $pm = $order['info']['payment_method'];
                                        echo ($pm == 'bank' || $pm == 'chuyenkhoan') ? 'Chuyển khoản ngân hàng' : 'Thanh toán khi nhận hàng (COD)';
                                    ?>
                                </p>
                                <p class="mb-1"><strong>Trạng thái thanh toán:</strong> 
                                    <?php 
                                        $ps = $order['info']['payment_status'];
                                        if ($ps == 'da thanh toan' || $ps == 'paid') echo '<span class="text-success fw-bold">Đã thanh toán</span>';
                                        elseif ($ps == 'that bai') echo '<span class="text-danger fw-bold">Thất bại/Hủy</span>';
                                        else echo '<span class="text-warning fw-bold">Chờ thanh toán</span>';
                                    ?>
                                </p>
                            </div>
                            <div class="col-md-6 text-end">
                                <h5 class="text-danger mb-3">Tổng tiền: <?php echo number_format($order['info']['tongtien'], 0, ',', '.'); ?> đ</h5>
                                <?php 
                                // Logic hiển thị nút thanh toán lại
                                if (($order['info']['payment_method'] == 'bank' || $order['info']['payment_method'] == 'chuyenkhoan') 
                                    && ($order['info']['payment_status'] == 'pending' || $order['info']['payment_status'] == 'chua thanh toan')
                                    && $order['info']['trangthai'] != 'đã hủy'): 
                                ?>
                                    <a href="thanhtoan.php?order_id=<?php echo $order_id; ?>" class="btn btn-primary">
                                        <i class="fas fa-qrcode"></i> Thanh toán ngay
                                    </a>
                                <?php endif; ?>
                                <?php if ($order['info']['trangthai'] == 'chờ xác nhận'): ?>
                                    <a href="huy_don_hang.php?id=<?php echo $order_id; ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Bạn có chắc muốn hủy đơn hàng này?');">Hủy đơn</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        <div class="text-center mt-4"><a href="mqd.php" class="btn btn-secondary">Quay lại trang chủ</a></div>
    </div>
    <?php include('footer.php'); ?>
</body>
</html>