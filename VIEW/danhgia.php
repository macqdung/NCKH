<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include_once '../MODEL/modeldanhgia.php';
include_once '../MODEL/modelmqd1.php'; // To get product details for the form

if (!isset($reviews)) {
    $reviewModel = new data_danhgia();
    $productModel = new data_mqd1(); // Instantiate product model
    $reviews = $reviewModel->getReviews();
}

$order_id_for_review = isset($_GET['order_id']) ? intval($_GET['order_id']) : null;
$product_id_for_review = isset($_GET['product_id']) ? intval($_GET['product_id']) : null;
$product_to_review = null;
if ($product_id_for_review) {
    $product_to_review = $productModel->getProductById($product_id_for_review);
}
if (!isset($errors)) {
    $errors = [];
}

if (!isset($success)) {
    $success = '';
}

if (!isset($comment)) {
    $comment = '';
}
?>
<!doctype html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>レビュー</title>
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
        .review-card {
            animation: fadeInUp 0.8s ease-out;
        }
        .review-card:nth-child(1) { animation-delay: 0.1s; }
        .review-card:nth-child(2) { animation-delay: 0.2s; }
        .review-card:nth-child(3) { animation-delay: 0.3s; }
        .review-card:nth-child(4) { animation-delay: 0.4s; }
        .review-card:nth-child(5) { animation-delay: 0.5s; }
        .review-card:nth-child(n+6) { animation-delay: 0.6s; }
    </style>
</head>
<body>
    <?php include('menu.php'); ?>

    <div class="container mt-5">
        <h2 class="text-center mb-4"><i class="fas fa-star"></i> レビュー</h2>

        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="fas fa-info-circle fa-3x text-info mb-3"></i>
                        <h5 class="card-title">商品評価について</h5>
                        <p class="card-text">商品を評価するには、購入後、注文履歴で評価してください。</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-primary text-white text-center">
                <h5><i class="fas fa-comments"></i> 過去の評価</h5>
            </div>
            <div class="card-body">
                <?php if (empty($reviews)): ?>
                    <div class="text-center">
                        <i class="fas fa-comment-slash fa-5x text-muted mb-3"></i>
                        <p class="fs-4 text-muted">まだ評価がありません。</p>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($reviews as $review): ?>
                            <div class="col-md-6 mb-4 review-card">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <img src="../media/<?= htmlspecialchars($review['hinhanh'] ?? 'default.jpg') ?>" alt="<?= htmlspecialchars($review['tensanpham']) ?>" width="60" height="60" class="rounded-circle me-3 product-img">
                                            <div>
                                                <h6 class="mb-0"><?= htmlspecialchars($review['tensanpham']) ?></h6>
                                                <small class="text-muted"><strong><?php echo htmlspecialchars($review['user']); ?></strong> による評価 - <?php echo htmlspecialchars($review['created_at']); ?></small>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mb-2">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="fas fa-star <?= ($i <= $review['rating']) ? 'text-warning' : 'text-muted' ?> me-1"></i>
                                            <?php endfor; ?>
                                            <span class="ms-2 badge bg-light text-dark"><?= $review['rating'] ?>/5</span>
                                        </div>
                                        <p class="mt-2"><?php echo nl2br(htmlspecialchars($review['comment'])); ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
    <?php include('footer.php'); ?>
</body>
</html>
