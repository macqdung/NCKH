<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: /SANPHAMMOI/VIEW/dangnhap.php");
    exit;
}
include('../MODEL/modelmh.php');
include('../MODEL/modeldangnhap.php');
include_once(__DIR__ . '/../MODEL/modeladmin.php');
$get_data = new data_muahang();
$userModel = new data_user_login();
$admin_model = new data_admin();
$user = $userModel->get_user_by_username($_SESSION['user']);
if (!$user) {
    header("Location: /SANPHAMMOI/VIEW/dangnhap.php?error=invalid_user");
    exit;
}
$id_user = $user['ID_user'];
$orders = $get_data->select_muahang_by_user($id_user);
$message = isset($_GET['message']) ? $_GET['message'] : '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cancel_order'])) {
    $id = intval($_POST['id']);
    $cancel = $get_data->cancel_order($id);
    if ($cancel) {
        header("Location: lichsumuahang.php?message=注文が正常にキャンセルされました！");
        exit;
    } else {
        $message = '注文キャンセルに失敗しました！（確認待ちのステータスのみキャンセル可能です）';
    }
    // Reload orders
    $orders = $get_data->select_muahang_by_user($id_user);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['request_return'])) {
    $order_id = intval($_POST['order_id']);
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);
    $reason = trim($_POST['reason']);
    if (!empty($reason)) {
        $insert = $admin_model->insert_return($order_id, $id_user, $product_id, $quantity, $reason);
        if ($insert) {
            $message = '返品・交換リクエストが正常に送信されました！管理者がすぐに処理します。';
        } else {
            $message = '返品・交換リクエストの送信に失敗しました。もう一度お試しください。';
        }
    } else {
        $message = '返品・交換理由を入力してください。';
    }
    // Reload orders
    $orders = $get_data->select_muahang_by_user($id_user);
}
?>
<!doctype html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>購入履歴</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="luoi.css">
    <link rel="stylesheet" type="text/css" href="dinhdang.css">
    <link rel="stylesheet" type="text/css" href="dinhdangmenu.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Noto Sans JP', sans-serif;
        }
        .navbar {
            background: rgba(255, 255, 255, 0.9) !important;
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 30px;
            margin-top: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            animation: fadeInUp 0.8s ease-out;
        }
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.15);
        }
        .table {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        .table thead th {
            background: linear-gradient(45deg, #007bff, #0056b3);
            color: white;
            border: none;
            font-weight: 600;
        }
        .table tbody tr {
            animation: fadeIn 0.6s ease-out forwards;
            opacity: 0;
        }
        .table tbody tr:nth-child(1) { animation-delay: 0.1s; }
        .table tbody tr:nth-child(2) { animation-delay: 0.2s; }
        .table tbody tr:nth-child(3) { animation-delay: 0.3s; }
        .table tbody tr:nth-child(4) { animation-delay: 0.4s; }
        .table tbody tr:nth-child(5) { animation-delay: 0.5s; }
        .table tbody tr:nth-child(n+6) { animation-delay: 0.6s; }
        @keyframes fadeIn {
            to { opacity: 1; }
        }
        .table tbody tr:hover {
            background-color: rgba(0, 123, 255, 0.1);
            transform: scale(1.02);
            transition: all 0.3s ease;
        }
        .btn {
            border-radius: 25px;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        .badge {
            font-size: 0.8em;
            padding: 0.5em 0.8em;
            border-radius: 20px;
        }
        .modal-content {
            border-radius: 15px;
            border: none;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
        }
        .alert {
            border-radius: 10px;
            animation: slideIn 0.5s ease-out;
        }
        @keyframes slideIn {
            from {
                transform: translateX(-100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        .product-img {
            border-radius: 10px;
            transition: transform 0.3s ease;
        }
        .product-img:hover {
            transform: scale(1.1);
        }
    </style>
</head>
<body>
    <?php include('menu.php'); ?>

    <div class="container mt-5">
        <h2 class="text-center mb-4"><i class="fas fa-history"></i> 購入履歴</h2>
        <?php if ($message): ?>
            <div class="alert alert-info text-center"><i class="fas fa-info-circle"></i> <?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="fas fa-star fa-3x text-warning mb-3"></i>
                        <h5 class="card-title">ポイント</h5>
                        <p class="card-text fs-4">あなたは <strong class="text-primary"><?= $admin_model->get_user_points($id_user) ?> ポイント</strong> を持っています</p>
                        <p class="text-muted">1ポイント = 1000円</p>
                    </div>
                </div>
            </div>
        </div>

        <?php if (empty($orders)): ?>
            <div class="text-center">
                <i class="fas fa-shopping-cart fa-5x text-muted mb-3"></i>
                <p class="fs-4">まだ注文がありません。</p>
                <a href="mqd1.php" class="btn btn-primary btn-lg"><i class="fas fa-eye"></i> 商品を見る</a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th><i class="fas fa-hashtag"></i> 注文ID</th>
                            <th><i class="fas fa-box"></i> 商品</th>
                            <th><i class="fas fa-sort-numeric-up"></i> 数量</th>
                            <th><i class="fas fa-yen-sign"></i> 単価</th>
                            <th><i class="fas fa-calculator"></i> 合計金額</th>
                            <th><i class="fas fa-info-circle"></i> ステータス</th>
                            <th><i class="fas fa-cogs"></i> 操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><strong><?= $order['id'] ?></strong></td>
                            <td>
                                <img src="../media/<?= $order['hinhanh'] ?>" width="60" height="60" class="me-3 product-img" alt="<?= htmlspecialchars($order['tensanpham']) ?>">
                                <strong><?= htmlspecialchars($order['tensanpham']) ?></strong>
                            </td>
                            <td><span class="badge bg-light text-dark fs-6"><?= $order['soluong'] ?></span></td>
                            <td><strong class="text-success"><?= number_format($order['dongia'], 0, ',', '.') ?>円</strong></td>
                            <td><strong class="text-primary fs-5"><?= number_format($order['tongtien'], 0, ',', '.') ?>円</strong></td>
                            <td>
                                <span class="badge <?= $order['trangthai'] == 'chờ xác nhận' ? 'bg-warning' : ($order['trangthai'] == 'đang vận chuyển' ? 'bg-info' : ($order['trangthai'] == 'đã giao hàng thành công' ? 'bg-success' : 'bg-secondary')) ?>">
                                    <i class="fas fa-circle"></i> <?= htmlspecialchars($order['trangthai'] == 'chờ xác nhận' ? '確認待ち' : ($order['trangthai'] == 'đang vận chuyển' ? '配送中' : ($order['trangthai'] == 'đã giao hàng thành công' ? '配送完了' : $order['trangthai']))) ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($order['trangthai'] == 'chờ xác nhận'): ?>
                                    <form method="post" style="display:inline;" onsubmit="return confirm('この注文をキャンセルしますか？');">
                                        <input type="hidden" name="id" value="<?= $order['id'] ?>">
                                        <button type="submit" name="cancel_order" class="btn btn-sm btn-danger"><i class="fas fa-times"></i> キャンセル</button>
                                    </form>
                                <?php elseif ($order['trangthai'] == 'đã giao hàng thành công'): ?>
                                    <a href="hoadon.php?order_id=<?= $order['id'] ?>" class="btn btn-sm btn-primary me-2" target="_blank"><i class="fas fa-file-invoice"></i> 請求書</a>
                                    <button type="button" class="btn btn-sm btn-warning me-2" data-bs-toggle="modal" data-bs-target="#reviewModal<?= $order['id'] ?>"><i class="fas fa-star"></i> 評価</button>
                                    <?php
                                    $eligible_for_return = false;
                                    if (!empty($order['delivered_at'])) {
                                        $delivery_date = new DateTime($order['delivered_at']);
                                        $seven_days_later = clone $delivery_date;
                                        $seven_days_later->add(new DateInterval('P7D'));
                                        $now = new DateTime();
                                        if ($seven_days_later > $now) {
                                            $eligible_for_return = true;
                                        }
                                    }
                                    if ($eligible_for_return): ?>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#returnModal<?= $order['id'] ?>"><i class="fas fa-exchange-alt"></i> 返品リクエスト</button>
                                    <?php else: ?>
                                        <span class="text-muted small d-block"><i class="fas fa-clock"></i> 返品期限切れ (7日)</span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-muted"><i class="fas fa-ban"></i> 操作不可</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- Return Modals -->
    <?php foreach ($orders as $order): ?>
        <?php
        $eligible_for_return_modal = false;
        if ($order['trangthai'] == 'đã giao hàng thành công' && !empty($order['delivered_at'])) {
            $delivery_date = new DateTime($order['delivered_at']);
            $seven_days_later = clone $delivery_date;
            $seven_days_later->add(new DateInterval('P7D'));
            $now = new DateTime();
            if ($seven_days_later > $now) {
                $eligible_for_return_modal = true;
            }
        }
        ?>
        <?php if ($eligible_for_return_modal): ?>
        <div class="modal fade" id="returnModal<?= $order['id'] ?>" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-light">
                        <h5 class="modal-title"><i class="fas fa-exchange-alt"></i> 返品・交換リクエスト - 注文 <?= $order['id'] ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="post">
                        <div class="modal-body">
                            <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                            <input type="hidden" name="product_id" value="<?= $order['ID_sanpham'] ?>">
                            <input type="hidden" name="quantity" value="<?= $order['soluong'] ?>">
                            <div class="mb-3">
                                <label for="reason_<?= $order['id'] ?>" class="form-label"><i class="fas fa-comment"></i> 返品・交換理由</label>
                                <textarea class="form-control" id="reason_<?= $order['id'] ?>" name="reason" rows="4" required placeholder="理由を説明してください（破損、誤商品など）..."></textarea>
                            </div>
                            <p class="text-muted small"><i class="fas fa-box"></i> 商品: <?= htmlspecialchars($order['tensanpham']) ?> (数量: <?= $order['soluong'] ?>)</p>
                            <p class="text-muted small"><i class="fas fa-calendar"></i> 配送日: <?= date('Y/m/d', strtotime($order['delivered_at'])) ?></p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times"></i> キャンセル</button>
                            <button type="submit" name="request_return" class="btn btn-primary" onclick="return confirm('返品・交換リクエストを送信しますか？');"><i class="fas fa-paper-plane"></i> リクエスト送信</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <?php endif; ?>
    <?php endforeach; ?>

    <!-- Review Modals -->
    <?php foreach ($orders as $order): ?>
    <div class="modal fade" id="reviewModal<?= $order['id'] ?>" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title"><i class="fas fa-star"></i> 評価 - 注文 <?= $order['id'] ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="post" action="../CONTROLLER/controldanhgia.php">
                    <div class="modal-body">
                        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                        <input type="hidden" name="product_id" value="<?= $order['ID_sanpham'] ?>">
                        <div class="text-center mb-3">
                            <h6>商品: <?= htmlspecialchars($order['tensanpham']) ?></h6>
                            <img src="../media/<?= $order['hinhanh'] ?>" width="100" height="100" class="img-thumbnail" alt="<?= htmlspecialchars($order['tensanpham']) ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">評価 (1-5)</label>
                            <div class="d-flex justify-content-center star-rating" id="starRating<?= $order['id'] ?>">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <label class="star mx-1" data-value="<?= $i ?>">
                                        <i class="fas fa-star text-muted"></i>
                                        <input type="radio" name="rating" value="<?= $i ?>" id="star<?= $order['id'] ?><?= $i ?>" style="display: none;" required>
                                    </label>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="comment<?= $order['id'] ?>" class="form-label">コメント</label>
                            <textarea class="form-control" id="comment<?= $order['id'] ?>" name="comment" rows="4" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times"></i> キャンセル</button>
                        <button type="submit" class="btn btn-warning"><i class="fas fa-paper-plane"></i> 評価送信</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endforeach; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function printInvoice(orderId) {
            fetch(`../get_invoice.php?order_id=${orderId}`)
                .then(response => response.json())
                .then(order => {
                    if (order.error) {
                        alert('請求書が見つかりません！');
                        return;
                    }
                    const invoiceWindow = window.open('', '_blank', 'width=800,height=700');
                    invoiceWindow.document.write(`
                        <html>
                        <head>
                            <title>請求書 - 注文 ${orderId}</title>
                            <style>
                                body { font-family: 'Noto Sans JP', sans-serif; margin: 20px; }
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
                                <p>購入請求書</p>
                            </div>
                            <div class="details">
                                <p><strong>注文ID:</strong> ${orderId}</p>
                                <p><strong>注文日:</strong> ${order.ngay_dat ? order.ngay_dat : '未更新'}</p>
                                <p><strong>配送完了日:</strong> ${order.ngay_giao ? order.ngay_giao : '未更新'}</p>
                                <p><strong>印刷日:</strong> ${new Date().toLocaleDateString('ja-JP')}</p>
                            </div>
                            <table>
                                <thead>
                                    <tr>
                                        <th>画像</th>
                                        <th>商品</th>
                                        <th>数量</th>
                                        <th>単価</th>
                                        <th>合計金額</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><img src="../media/${order.hinhanh}" class="product-img" alt="${order.tensanpham}"></td>
                                        <td>${order.tensanpham}</td>
                                        <td>${order.soluong}</td>
                                        <td>${Number(order.dongia).toLocaleString('ja-JP')}円</td>
                                        <td>${Number(order.tongtien).toLocaleString('ja-JP')}円</td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="review">
                                <h4>お客様の評価</h4>
                                ${
                                    order.review
                                    ? `<div>
                                        <strong>評価: </strong> ${order.review.rating}/5<br>
                                        <strong>コメント:</strong> ${order.review.comment}<br>
                                        <small><i>評価日: ${order.review.created_at}</i></small>
                                    </div>`
                                    : '<span>この商品の評価はまだありません。</span>'
                                }
                            </div>
                            <p class="total" style="text-align: right; margin-top: 20px;">ご購入ありがとうございます！</p>
                        </body>
                        </html>
                    `);
                    invoiceWindow.document.close();
                    invoiceWindow.print();
                })
                .catch(() => {
                    alert('請求書の取得中にエラーが発生しました！');
                });
        }

        // Star rating functionality for review modals
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.star-rating').forEach(function(ratingContainer) {
                const stars = ratingContainer.querySelectorAll('.star');
                const ratingInputs = ratingContainer.querySelectorAll('input[name="rating"]');

                stars.forEach(star => {
                    star.addEventListener('click', function() {
                        const value = parseInt(this.dataset.value);
                        const starIcon = this.querySelector('i');

                        // Update visual state
                        stars.forEach((s, index) => {
                            const sIcon = s.querySelector('i');
                            if (index < value) {
                                sIcon.classList.remove('text-muted');
                                sIcon.classList.add('text-warning');
                            } else {
                                sIcon.classList.remove('text-warning');
                                sIcon.classList.add('text-muted');
                            }
                        });

                        // Set the radio button
                        ratingInputs.forEach(input => {
                            if (parseInt(input.value) === value) {
                                input.checked = true;
                            }
                        });
                    });

                    // Optional: Hover effect
                    star.addEventListener('mouseover', function() {
                        const value = parseInt(this.dataset.value);
                        stars.forEach((s, index) => {
                            const sIcon = s.querySelector('i');
                            if (index < value) {
                                sIcon.classList.add('text-warning');
                                sIcon.classList.remove('text-muted');
                            } else {
                                sIcon.classList.add('text-muted');
                                sIcon.classList.remove('text-warning');
                            }
                        });
                    });

                    // Reset on mouseout
                    ratingContainer.addEventListener('mouseleave', function() {
                        const selectedValue = ratingContainer.querySelector('input[name="rating"]:checked');
                        const currentRating = selectedValue ? parseInt(selectedValue.value) : 0;
                        stars.forEach((s, index) => {
                            const sIcon = s.querySelector('i');
                            if (index < currentRating) {
                                sIcon.classList.remove('text-muted');
                                sIcon.classList.add('text-warning');
                            } else {
                                sIcon.classList.remove('text-warning');
                                sIcon.classList.add('text-muted');
                            }
                        });
                    });
                });
            });
        });
    </script>
    <?php include('footer.php'); ?>
</body>
</html>
