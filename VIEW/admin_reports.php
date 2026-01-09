<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: dangnhap.php');
    exit();
}
include_once('../MODEL/connect.php');
include_once('../MODEL/modeladmin.php');
$admin = new data_admin();

// Get report data
$total_revenue = $admin->get_total_revenue();
$products_sold = $admin->get_products_sold();
$order_stats = $admin->get_order_stats();
?>

<!DOCTYPE html>
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
                    <h1 class="h2">レポートと統計</h1>
                </div>

                <!-- Summary Cards -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card text-white bg-success">
                            <div class="card-body">
                                <h5 class="card-title">総売上</h5>
                                <h3><?php echo number_format($total_revenue); ?> 円</h3>
                                <p class="card-text">完了した注文の合計</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-white bg-primary">
                            <div class="card-body">
                                <h5 class="card-title">販売された商品数</h5>
                                <h3><?php echo number_format($products_sold); ?></h3>
                                <p class="card-text">完了した注文の商品合計</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-white bg-warning">
                            <div class="card-body">
                                <h5 class="card-title">平均注文金額</h5>
                                <h3><?php echo $products_sold > 0 ? number_format($total_revenue / $products_sold) : 0; ?> 円</h3>
                                <p class="card-text">商品ごとの平均金額</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Status Chart -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>注文ステータス分布</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="orderStatusChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>注文ステータス詳細</h5>
                            </div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    <?php foreach ($order_stats as $status => $count): ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <?php
                                            $status_labels = [
                                                'chờ xác nhận' => '確認待ち',
                                                'đang vận chuyển' => '配送中',
                                                'đã giao hàng thành công' => '配送完了',
                                                'đã hủy' => 'キャンセル'
                                            ];
                                            $status_colors = [
                                                'chờ xác nhận' => 'warning',
                                                'đang vận chuyển' => 'info',
                                                'đã giao hàng thành công' => 'success',
                                                'đã hủy' => 'danger'
                                            ];
                                            ?>
                                            <span class="badge bg-<?php echo $status_colors[$status]; ?> rounded-pill"><?php echo $status_labels[$status]; ?></span>
                                            <span class="badge bg-secondary rounded-pill"><?php echo $count; ?>件</span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Reports Section -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5>追加レポート</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="d-grid">
                                            <button class="btn btn-outline-primary" onclick="generateReport('sales')">
                                                <i class="fas fa-chart-line"></i> 売上レポート
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="d-grid">
                                            <button class="btn btn-outline-primary" onclick="generateReport('products')">
                                                <i class="fas fa-box"></i> 商品レポート
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="d-grid">
                                            <button class="btn btn-outline-primary" onclick="generateReport('customers')">
                                                <i class="fas fa-users"></i> 顧客レポート
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Order Status Chart
        const ctx = document.getElementById('orderStatusChart').getContext('2d');
        const orderStatusChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['確認待ち', '配送中', '配送完了', 'キャンセル'],
                datasets: [{
                    data: [
                        <?php echo $order_stats['chờ xác nhận'] ?? 0; ?>,
                        <?php echo $order_stats['đang vận chuyển'] ?? 0; ?>,
                        <?php echo $order_stats['đã giao hàng thành công'] ?? 0; ?>,
                        <?php echo $order_stats['đã hủy'] ?? 0; ?>
                    ],
                    backgroundColor: [
                        '#ffc107',
                        '#0dcaf0',
                        '#198754',
                        '#dc3545'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });

        function generateReport(type) {
            alert('レポート生成機能は開発中です。タイプ: ' + type);
            // In a real implementation, this would generate and download a report
        }
    </script>
</body>
</html>
