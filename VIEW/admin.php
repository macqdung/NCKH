<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: dangnhap.php');
    exit();
}
include('../CONTROLLER/controladmin.php');
?>
<!doctype html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>管理者ホーム - Bakery Shop</title>
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
                            <a class="nav-link active" href="admin.php">
                                <i class="fas fa-tachometer-alt"></i> 管理者ホーム
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin_categories.php">
                                <i class="fas fa-tags"></i> カテゴリ管理
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin_books.php">
                                <i class="fas fa-box"></i> 商品管理
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
                                <i class="fas fa-ticket-alt"></i> バウチャー管理
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin_promotions.php">
                                <i class="fas fa-percent"></i> プロモーション管理
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin_loyalty.php">
                                <i class="fas fa-star"></i> ポイント管理
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
                    <h1 class="h2">管理者ホーム</h1>
                </div>

                <?php if (isset($_GET['message'])): ?>
                    <div class="alert alert-info" style="animation: slideInRight 1s ease-out; background: linear-gradient(45deg, #ff6b6b, #feca57); color: #fff; border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
                        <i class="fas fa-info-circle"></i> <?= htmlspecialchars($_GET['message']) ?>
                    </div>
                <?php endif; ?>

                <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading"><i class="fas fa-user-shield"></i> こんにちは、管理者！</h4>
                    <p>今日は何をしたいですか？ (Kyō wa nani o shitai desu ka? - What do you want to do today?)</p>
                    <hr>
                    <p class="mb-0"><i class="fas fa-arrow-left"></i> 左側のメニューを使用して管理機能にアクセスしてください。</p>
                </div>
