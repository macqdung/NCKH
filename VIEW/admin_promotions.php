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
    if (isset($_POST['insert_promotion'])) {
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);
        $discount_type = $_POST['discount_type'];
        $discount_value = $_POST['discount_value'];
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        $applicable_products = json_encode($_POST['applicable_products'] ?? []);
        $status = $_POST['status'];

        if ($admin->insert_promotion($name, $description, $discount_type, $discount_value, $start_date, $end_date, $applicable_products, $status)) {
            $message = 'プロモーションが追加されました。';
        } else {
            $message = 'プロモーションの追加に失敗しました。';
        }
    } elseif (isset($_POST['update_promotion'])) {
        $id = $_POST['promotion_id'];
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);
        $discount_type = $_POST['discount_type'];
        $discount_value = $_POST['discount_value'];
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        $applicable_products = json_encode($_POST['applicable_products'] ?? []);
        $status = $_POST['status'];

        if ($admin->update_promotion($id, $name, $description, $discount_type, $discount_value, $start_date, $end_date, $applicable_products, $status)) {
            $message = 'プロモーションが更新されました。';
        } else {
            $message = 'プロモーションの更新に失敗しました。';
        }
    } elseif (isset($_POST['delete_promotion'])) {
        $id = $_POST['promotion_id'];
        if ($admin->delete_promotion($id)) {
            $message = 'プロモーションが削除されました。';
        } else {
            $message = 'プロモーションの削除に失敗しました。';
        }
    }
}

// Get data
$promotions = $admin->get_all_promotions();
$books = $admin->get_all_books();
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>プロモーション管理 - 管理者パネル</title>
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
                            <a class="nav-link" href="admin_orders.php">
                                <i class="fas fa-shopping-cart"></i> 注文管理
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin_vouchers.php">
                                <i class="fas fa-ticket-alt"></i> クーポン管理
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="admin_promotions.php">
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
                    <h1 class="h2">プロモーション管理</h1>
                </div>

                <?php if ($message): ?>
                    <div class="alert alert-info"><?php echo $message; ?></div>
                <?php endif; ?>

                <!-- Add Promotion Form -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>新しいプロモーションを追加</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" id="promotionForm">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">プロモーション名 *</label>
                                        <input type="text" class="form-control" id="name" name="name" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="description" class="form-label">説明</label>
                                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="discount_type" class="form-label">割引タイプ</label>
                                        <select class="form-control" id="discount_type" name="discount_type">
                                            <option value="percentage">パーセント</option>
                                            <option value="fixed">固定金額</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="discount_value" class="form-label">割引値 *</label>
                                        <input type="number" step="0.01" class="form-control" id="discount_value" name="discount_value" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="start_date" class="form-label">開始日 *</label>
                                        <input type="date" class="form-control" id="start_date" name="start_date" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="end_date" class="form-label">終了日 *</label>
                                        <input type="date" class="form-control" id="end_date" name="end_date" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="status" class="form-label">ステータス</label>
                                        <select class="form-control" id="status" name="status">
                                            <option value="active">アクティブ</option>
                                            <option value="inactive">非アクティブ</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">適用商品</label>
                                <div class="border p-3" style="max-height: 200px; overflow-y: auto;">
                                    <?php foreach ($books as $book): ?>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="applicable_products[]" value="<?php echo $book['ID_sanpham']; ?>" id="product_<?php echo $book['ID_sanpham']; ?>">
                                            <label class="form-check-label" for="product_<?php echo $book['ID_sanpham']; ?>">
                                                <?php echo htmlspecialchars($book['tensanpham']); ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <button type="submit" name="insert_promotion" class="btn btn-primary">プロモーションを追加</button>
                        </form>
                    </div>
                </div>

                <!-- Promotions List -->
                <div class="card">
                    <div class="card-header">
                        <h5>プロモーション一覧</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>名前</th>
                                        <th>割引タイプ</th>
                                        <th>割引値</th>
                                        <th>期間</th>
                                        <th>ステータス</th>
                                        <th>操作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($promotions as $promotion): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($promotion['name']); ?></td>
                                            <td><?php echo $promotion['discount_type'] == 'percentage' ? 'パーセント' : '固定金額'; ?></td>
                                            <td><?php echo $promotion['discount_type'] == 'percentage' ? $promotion['discount_value'] . '%' : number_format($promotion['discount_value']) . ' 円'; ?></td>
                                            <td><?php echo date('Y-m-d', strtotime($promotion['start_date'])) . ' - ' . date('Y-m-d', strtotime($promotion['end_date'])); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo $promotion['status'] == 'active' ? 'success' : 'secondary'; ?>">
                                                    <?php echo $promotion['status'] == 'active' ? 'アクティブ' : '非アクティブ'; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary me-2" onclick="editPromotion(<?php echo $promotion['id']; ?>)">
                                                    <i class="fas fa-edit"></i> 編集
                                                </button>
                                                <form method="POST" class="d-inline" onsubmit="return confirm('このプロモーションを削除しますか？')">
                                                    <input type="hidden" name="promotion_id" value="<?php echo $promotion['id']; ?>">
                                                    <button type="submit" name="delete_promotion" class="btn btn-sm btn-outline-danger">
                                                        <i class="fas fa-trash"></i> 削除
                                                    </button>
                                                </form>
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

    <!-- Edit Promotion Modal -->
    <div class="modal fade" id="editPromotionModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">プロモーション編集</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="promotion_id" id="edit_promotion_id">
                        <!-- Form fields similar to add form -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_name" class="form-label">プロモーション名 *</label>
                                    <input type="text" class="form-control" id="edit_name" name="name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_description" class="form-label">説明</label>
                                    <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_discount_type" class="form-label">割引タイプ</label>
                                    <select class="form-control" id="edit_discount_type" name="discount_type">
                                        <option value="percentage">パーセント</option>
                                        <option value="fixed">固定金額</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_discount_value" class="form-label">割引値 *</label>
                                    <input type="number" step="0.01" class="form-control" id="edit_discount_value" name="discount_value" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_start_date" class="form-label">開始日 *</label>
                                    <input type="date" class="form-control" id="edit_start_date" name="start_date" required>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_end_date" class="form-label">終了日 *</label>
                                    <input type="date" class="form-control" id="edit_end_date" name="end_date" required>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_status" class="form-label">ステータス</label>
                                    <select class="form-control" id="edit_status" name="status">
                                        <option value="active">アクティブ</option>
                                        <option value="inactive">非アクティブ</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">適用商品</label>
                            <div class="border p-3" style="max-height: 200px; overflow-y: auto;">
                                <?php foreach ($books as $book): ?>
                                    <div class="form-check">
                                        <input class="form-check-input edit-product-checkbox" type="checkbox" name="applicable_products[]" value="<?php echo $book['ID_sanpham']; ?>" id="edit_product_<?php echo $book['ID_sanpham']; ?>">
                                        <label class="form-check-label" for="edit_product_<?php echo $book['ID_sanpham']; ?>">
                                            <?php echo htmlspecialchars($book['tensanpham']); ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">キャンセル</button>
                        <button type="submit" name="update_promotion" class="btn btn-primary">更新</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editPromotion(promotionId) {
            // In a real implementation, you'd fetch promotion data via AJAX
            // For now, just open the modal
            document.getElementById('edit_promotion_id').value = promotionId;
            new bootstrap.Modal(document.getElementById('editPromotionModal')).show();
        }
    </script>
</body>
</html>
