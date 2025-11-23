<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin'])) {
    header("Location: dangnhap.php");
    exit;
}

$admin_username = $_SESSION['admin'];

// Include controller để lấy dữ liệu báo cáo
include_once('../CONTROLLER/controladmin.php');

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>管理者ホームページ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .dashboard-card {
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 20px rgba(0,0,0,.1);
        }
        .card-icon {
            font-size: 2.5rem;
            opacity: 0.6;
        }
    </style>
</head>
<body>
    <div class="container my-5">
        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
            <h1 class="h2">管理者ホームページ</h1>
            <div>
                <span class="navbar-text me-3">
                    こんにちは、<strong><?php echo htmlspecialchars($admin_username); ?></strong>さん！
                </span>
                <a href="dangxuat.php" class="btn btn-outline-danger">
                    <i class="fas fa-sign-out-alt"></i> ログアウト
                </a>
            </div>
        </div>

        <div class="alert alert-primary" role="alert">
            <h4 class="alert-heading"><i class="fas fa-info-circle"></i> お知らせ</h4>
            <p>ここからウェブサイトのさまざまな側面を管理できます。以下から管理したいセクションを選択してください。</p>
            <hr>
            <p class="mb-0">ご不明な点がございましたら、システムサポートにお問い合わせください。</p>
        </div>

        <h3 class="mt-5 mb-3">管理メニュー</h3>
        <div class="row">
            <!-- Quản lý sản phẩm -->
            <div class="col-md-4 mb-4">
                <div class="card dashboard-card h-100">
                    <div class="card-body text-center">
                        <div class="card-icon mb-3"><i class="fas fa-box-open"></i></div>
                        <h5 class="card-title">製品管理</h5>
                        <p class="card-text">製品の追加、編集、削除、在庫と価格の更新を行います。</p>
                        <a href="quanly_sanpham.php" class="btn btn-primary">移動</a>
                    </div>
                </div>
            </div>

            <!-- Quản lý đơn hàng -->
            <div class="col-md-4 mb-4">
                <div class="card dashboard-card h-100">
                    <div class="card-body text-center">
                        <div class="card-icon mb-3"><i class="fas fa-receipt"></i></div>
                        <h5 class="card-title">注文管理</h5>
                        <p class="card-text">顧客の注文を確認し、ステータスを更新します。</p>
                        <a href="quanly_donhang.php" class="btn btn-primary">移動</a>
                    </div>
                </div>
            </div>

            <!-- Quản lý Voucher -->
            <div class="col-md-4 mb-4">
                <div class="card dashboard-card h-100">
                    <div class="card-body text-center">
                        <div class="card-icon mb-3"><i class="fas fa-tags"></i></div>
                        <h5 class="card-title">バウチャー管理</h5>
                        <p class="card-text">新しいバウチャーを作成し、既存のものを管理します。</p>
                        <a href="quanly_voucher.php" class="btn btn-primary">移動</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>