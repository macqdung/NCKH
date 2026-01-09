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
<?php include('head.php'); ?>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include('head_nav.php'); ?>

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
            </main>
            