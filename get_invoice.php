<?php
// get_invoice.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_GET['order_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing order_id']);
    exit;
}
$order_id = intval($_GET['order_id']);
include('MODEL/modelmh.php');
include('MODEL/modeldanhgia.php');
$get_data = new data_muahang();
$review_data = new data_danhgia();
// Debug lỗi SQL hoặc exception
try {
    $order = $get_data->select_order_by_id($order_id);
    if (!$order) {
        http_response_code(404);
        echo json_encode(['error' => 'Order not found']);
        exit;
    }

    // Lấy đánh giá của user cho sản phẩm này (nếu có)
    $username = isset($_SESSION['user']) ? $_SESSION['user'] : $order['ID_user'];
    $product_id = $order['ID_sanpham'];
    $review = $review_data->getReviewByProductAndUser($product_id, $username);

    // Chuẩn bị dữ liệu hóa đơn
    $invoice = [
        'id' => $order['ID'],
        'tensanpham' => $order['tensanpham'],
        'hinhanh' => $order['hinhanh'],
        'soluong' => $order['soluong'],
        'dongia' => $order['dongia'],
        'tongtien' => $order['tongtien'],
        'trangthai' => $order['trangthai'],
        'ngay_dat' => isset($order['created_at']) ? $order['created_at'] : '',
        'ngay_giao' => isset($order['delivered_at']) ? $order['delivered_at'] : '',
        'review' => $review
    ];

    header('Content-Type: application/json');
    echo json_encode($invoice);
} catch (Throwable $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'EXCEPTION', 'message' => $e->getMessage()]);
    exit;
}

// Lấy đánh giá của user cho sản phẩm này (nếu có)
$username = isset($_SESSION['user']) ? $_SESSION['user'] : $order['ID_user'];
$product_id = $order['ID_sanpham'];
$review = $review_data->getReviewByProductAndUser($product_id, $username);

// Chuẩn bị dữ liệu hóa đơn
$invoice = [
    'id' => $order['ID'],
    'tensanpham' => $order['tensanpham'],
    'hinhanh' => $order['hinhanh'],
    'soluong' => $order['soluong'],
    'dongia' => $order['dongia'],
    'tongtien' => $order['tongtien'],
    'trangthai' => $order['trangthai'],
    'ngay_dat' => isset($order['created_at']) ? $order['created_at'] : '',
    'ngay_giao' => isset($order['delivered_at']) ? $order['delivered_at'] : '',
    'review' => $review
];

header('Content-Type: application/json');
echo json_encode($invoice);
