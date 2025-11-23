<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: dangnhap.php');
    exit();
}
include_once('../MODEL/connect.php');
include_once('../MODEL/modeladmin.php');
$admin = new data_admin();

// Handle form submissions
$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_order_status'])) {
        $order_id = $_POST['order_id'];
        $status = $_POST['status'];

        if ($admin->update_order_status($order_id, $status)) {
            $message = '注文ステータスが更新されました。';
        } else {
            $message = '注文ステータスの更新に失敗しました。';
        }
    }
}

// Get data
$orders = $admin->select_all_orders();
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>注文管理 - 管理者パネル</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="luoi.css">
    <link rel="stylesheet" type="text/css" href="dinhdang.css">
    <link rel="stylesheet" type="text/css" href="dinhdangmenu.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Arial', sans-serif;
        }
        .sidebar {
            background: linear-gradient(180deg, #ff9a9e 0%, #fecfef 50%, #fecfef 100%);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            animation: slideInLeft 1s ease-out;
        }
        .sidebar .nav-link {
            color: #333;
            transition: all 0.3s ease;
            border-radius: 10px;
            margin: 5px 0;
        }
        .sidebar .nav-link:hover {
            background: rgba(255,255,255,0.3);
            transform: translateX(10px);
            color: #fff;
        }
        .sidebar .nav-link.active {
            background: rgba(255,255,255,0.5);
            color: #333;
        }
        main {
            animation: fadeIn 1.5s ease-in;
        }
        .alert-success {
            background: linear-gradient(45deg, #56ab2f, #a8e6cf);
            border: none;
            color: #fff;
            animation: bounceIn 1s ease-out;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        .alert-heading {
            color: #fff;
            font-weight: bold;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes slideInLeft {
            from { transform: translateX(-100%); }
            to { transform: translateX(0); }
        }
        @keyframes bounceIn {
            0% { transform: scale(0.3); opacity: 0; }
            50% { transform: scale(1.05); }
            70% { transform: scale(0.9); }
            100% { transform: scale(1); opacity: 1; }
        }
        h1.h2 {
            color: #fff;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
            animation: textGlow 2s ease-in-out infinite alternate;
        }
        @keyframes textGlow {
            from { text-shadow: 2px 2px 4px rgba(0,0,0,0.5); }
            to { text-shadow: 2px 2px 4px rgba(255,255,255,0.8); }
        }
        @keyframes slideInRight {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @keyframes zoomIn {
            from { transform: scale(0.5); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-2 d-none d-md-block bg-light sidebar">
                <div class="sidebar-sticky">
                    <h5 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                        <span>管理者メニュー</span>
                    </h5>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="admin.php">
                                <i class="fas fa-tachometer-alt"></i> ダッシュボード
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin_categories.php">
                                <i class="fas fa-tags"></i> カテゴリ管理
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin_books.php">
                                <i class="fas fa-book"></i> 本管理
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin_staff.php">
                                <i class="fas fa-users"></i> スタッフ管理
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="admin_orders.php">
                                <i class="fas fa-shopping-cart"></i> 注文管理
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin_vouchers.php">
                                <i class="fas fa-ticket-alt"></i> クーポン管理
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin_promotions.php">
                                <i class="fas fa-percent"></i> プロモーション管理
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin_loyalty.php">
                                <i class="fas fa-star"></i> ロイヤリティ管理
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin_reports.php">
                                <i class="fas fa-chart-bar"></i> レポート
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin_returns.php">
                                <i class="fas fa-undo"></i> 返品管理
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="dangnhap.php">
                                <i class="fas fa-sign-out-alt"></i> ログアウト
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-10 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">注文管理</h1>
                </div>

                <?php if ($message): ?>
                    <div class="alert alert-info"><?php echo $message; ?></div>
                <?php endif; ?>

                <!-- Orders List -->
                <div class="card">
                    <div class="card-header">
                        <h5>注文一覧</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>注文ID</th>
                                        <th>ユーザー</th>
                                        <th>商品</th>
                                        <th>数量</th>
                                        <th>合計金額</th>
                                        <th>ステータス</th>
                                        <th>操作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orders as $order): ?>
                                        <tr>
                                            <td><?php echo $order['ID']; ?></td>
                                            <td><?php echo htmlspecialchars($order['tendangnhap']); ?></td>
                                            <td><?php echo htmlspecialchars($order['tensanpham']); ?></td>
                                            <td><?php echo $order['soluong']; ?></td>
                                            <td><?php echo number_format($order['tongtien']); ?> 円</td>
                                            <td>
                                                <span class="badge bg-<?php
                                                    echo $order['trangthai'] == 'chờ xác nhận' ? 'warning' :
                                                         ($order['trangthai'] == 'đang vận chuyển' ? 'info' :
                                                          ($order['trangthai'] == 'đã giao hàng thành công' ? 'success' : 'danger'));
                                                ?>">
                                                    <?php
                                                    echo $order['trangthai'] == 'chờ xác nhận' ? '確認待ち' :
                                                         ($order['trangthai'] == 'đang vận chuyển' ? '配送中' :
                                                          ($order['trangthai'] == 'đã giao hàng thành công' ? '配送完了' : 'キャンセル'));
                                                    ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($order['trangthai'] == 'chờ xác nhận'): ?>
                                                    <button class="btn btn-sm btn-outline-primary me-2" onclick="updateOrderStatus(<?php echo $order['ID']; ?>, 'đang vận chuyển')">
                                                        <i class="fas fa-shipping-fast"></i> 配送開始
                                                    </button>
                                                <?php elseif ($order['trangthai'] == 'đang vận chuyển'): ?>
                                                    <button class="btn btn-sm btn-outline-success me-2" onclick="updateOrderStatus(<?php echo $order['ID']; ?>, 'đã giao hàng thành công')">
                                                        <i class="fas fa-check"></i> 配送完了
                                                    </button>
                                                <?php endif; ?>
                                                <?php if ($order['trangthai'] != 'đã hủy' && $order['trangthai'] != 'đã giao hàng thành công'): ?>
                                                    <button class="btn btn-sm btn-outline-danger" onclick="updateOrderStatus(<?php echo $order['ID']; ?>, 'đã hủy')">
                                                        <i class="fas fa-times"></i> キャンセル
                                                    </button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Update Status Modal -->
    <div class="modal fade" id="updateStatusModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">注文ステータス更新</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="order_id" id="order_id">
                        <input type="hidden" name="status" id="status">
                        <p id="statusMessage"></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">キャンセル</button>
                        <button type="submit" name="update_order_status" class="btn btn-primary">更新</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function updateOrderStatus(orderId, status) {
            document.getElementById('order_id').value = orderId;
            document.getElementById('status').value = status;
            const statusMessages = {
                'đang vận chuyển': 'この注文を配送中にしますか？',
                'đã giao hàng thành công': 'この注文を配送完了にしますか？',
                'đã hủy': 'この注文をキャンセルしますか？'
            };
            document.getElementById('statusMessage').textContent = statusMessages[status] || 'ステータスを更新しますか？';
            new bootstrap.Modal(document.getElementById('updateStatusModal')).show();
        }
    </script>
</body>
</html>
