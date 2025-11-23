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
    if (isset($_POST['update_loyalty_rules'])) {
        $points_per_purchase = $_POST['points_per_purchase'];
        $points_per_vnd = $_POST['points_per_vnd'];
        $redemption_rate = $_POST['redemption_rate'];
        $min_points_redemption = $_POST['min_points_redemption'];

        if ($admin->update_loyalty_rules($points_per_purchase, $points_per_vnd, $redemption_rate, $min_points_redemption)) {
            $message = 'ロイヤリティルールが更新されました。';
        } else {
            $message = 'ロイヤリティルールの更新に失敗しました。';
        }
    } elseif (isset($_POST['update_user_points'])) {
        $user_id = $_POST['user_id'];
        $points = $_POST['points'];

        if ($admin->update_user_loyalty_points($user_id, $points)) {
            $message = 'ユーザーポイントが更新されました。';
        } else {
            $message = 'ユーザーポイントの更新に失敗しました。';
        }
    }
}

// Get data
$loyalty_rules = $admin->get_loyalty_rules();
$user_points = $admin->get_all_user_loyalty_points();
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ロイヤリティ管理 - 管理者パネル</title>
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
                            <a class="nav-link" href="admin_promotions.php">
                                <i class="fas fa-percent"></i> プロモーション管理
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="admin_loyalty.php">
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
                    <h1 class="h2">ロイヤリティ管理</h1>
                </div>

                <?php if ($message): ?>
                    <div class="alert alert-info"><?php echo $message; ?></div>
                <?php endif; ?>

                <!-- Loyalty Rules -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>ロイヤリティルール設定</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="points_per_purchase" class="form-label">購入ごとのポイント</label>
                                        <input type="number" class="form-control" id="points_per_purchase" name="points_per_purchase" value="<?php echo $loyalty_rules['points_per_purchase'] ?? 10; ?>" required>
                                        <small class="form-text text-muted">各注文で獲得する固定ポイント</small>
                                    </div>
                                    <div class="mb-3">
                                        <label for="points_per_vnd" class="form-label">1円あたりのポイント</label>
                                        <input type="number" step="0.01" class="form-control" id="points_per_vnd" name="points_per_vnd" value="<?php echo $loyalty_rules['points_per_vnd'] ?? 0.01; ?>" required>
                                        <small class="form-text text-muted">1VNDごとに獲得するポイント</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="redemption_rate" class="form-label">ポイント交換レート</label>
                                        <input type="number" step="0.01" class="form-control" id="redemption_rate" name="redemption_rate" value="<?php echo $loyalty_rules['redemption_rate'] ?? 100; ?>" required>
                                        <small class="form-text text-muted">1ポイントあたりの円価値</small>
                                    </div>
                                    <div class="mb-3">
                                        <label for="min_points_redemption" class="form-label">最小交換ポイント</label>
                                        <input type="number" class="form-control" id="min_points_redemption" name="min_points_redemption" value="<?php echo $loyalty_rules['min_points_redemption'] ?? 100; ?>" required>
                                        <small class="form-text text-muted">交換に必要な最小ポイント数</small>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" name="update_loyalty_rules" class="btn btn-primary">ルールを更新</button>
                        </form>
                    </div>
                </div>

                <!-- User Points Management -->
                <div class="card">
                    <div class="card-header">
                        <h5>ユーザーポイント管理</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ユーザーID</th>
                                        <th>ユーザー名</th>
                                        <th>現在のポイント</th>
                                        <th>操作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($user_points as $user): ?>
                                        <tr>
                                            <td><?php echo $user['ID_user']; ?></td>
                                            <td><?php echo htmlspecialchars($user['tendangnhap']); ?></td>
                                            <td><?php echo number_format($user['loyalty_points']); ?> ポイント</td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary" onclick="editUserPoints(<?php echo $user['ID_user']; ?>, <?php echo $user['loyalty_points']; ?>, '<?php echo htmlspecialchars($user['tendangnhap']); ?>')">
                                                    <i class="fas fa-edit"></i> ポイント調整
                                                </button>
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

    <!-- Edit User Points Modal -->
    <div class="modal fade" id="editUserPointsModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">ユーザーポイント調整</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="user_id" id="edit_user_id">
                        <div class="mb-3">
                            <label for="edit_username" class="form-label">ユーザー名</label>
                            <input type="text" class="form-control" id="edit_username" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="edit_points" class="form-label">ポイント数</label>
                            <input type="number" class="form-control" id="edit_points" name="points" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">キャンセル</button>
                        <button type="submit" name="update_user_points" class="btn btn-primary">更新</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editUserPoints(userId, currentPoints, username) {
            document.getElementById('edit_user_id').value = userId;
            document.getElementById('edit_username').value = username;
            document.getElementById('edit_points').value = currentPoints;
            new bootstrap.Modal(document.getElementById('editUserPointsModal')).show();
        }
    </script>
</body>
</html>
