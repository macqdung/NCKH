<?php
session_start();
include_once('../MODEL/modelgiohang.php');
include_once('../MODEL/modelmqd1.php'); // Assuming this model has product details functions
include_once('../MODEL/modelmh.php');

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'add':
        $productId = $_POST['id_sanpham'] ?? null;
        $quantity = intval($_POST['soluong'] ?? 1);
        if ($productId && $quantity > 0) {
            $model = new data_mqd1();
            $product = $model->select_sanpham_id($productId);
            if (!empty($product)) {
                $stock = $product[0]['soluong'];
                if ($quantity <= $stock) {
                    GioHang::addItem($productId, $quantity);
                    $message = "Thêm vào giỏ hàng thành công";
                } else {
                    $message = "Số lượng yêu cầu vượt quá tồn kho (còn $stock)";
                }
            } else {
                $message = "Sản phẩm không tồn tại";
            }
        } else {
            $message = "Thông tin sản phẩm không hợp lệ";
        }
        header("Location: ../VIEW/mqd1.php?message=" . urlencode($message));
        exit;
        break;

    case 'toggle_select':
        $productId = $_POST['productId'] ?? null;
        $selected = isset($_POST['selected']) ? (bool) $_POST['selected'] : false;
        if ($productId) {
            GioHang::toggleSelect($productId, $selected);
        }
        header("Location: ../VIEW/giohang.php");
        exit;
        break;

    case 'remove':
        $productId = $_GET['id_sanpham'] ?? null;
        if ($productId) {
            GioHang::removeItem($productId);
        }
        header("Location: ../VIEW/giohang.php");
        exit;
        break;

    case 'delete_selected':
        GioHang::deleteSelected();
        header("Location: ../VIEW/giohang.php?message=" . urlencode("Đã xóa các sản phẩm được chọn"));
        exit;
        break;

    case 'update':
        if (isset($_POST['quantities']) && is_array($_POST['quantities'])) {
            foreach ($_POST['quantities'] as $productId => $quantity) {
                GioHang::updateItem($productId, intval($quantity));
            }
        }
        $referer = $_SERVER['HTTP_REFERER'] ?? '../VIEW/giohang.php';
        header("Location: " . $referer . (strpos($referer, '?') !== false ? '&' : '?') . "message=" . urlencode("Giỏ hàng đã được cập nhật"));
        exit;
        break;

    case 'buy':
    case 'buy_selected':
        if (!isset($_SESSION['user'])) {
            header("Location: ../VIEW/dangnhap.php");
            exit;
        }
        $id_user = isset($_SESSION['ID_user']) ? $_SESSION['ID_user'] : 1;
        $cartItems = ($action === 'buy_selected') ? GioHang::getSelectedItems() : GioHang::getItems();
        $data = new data_muahang();
        $data_cart = new data_mqd1();
        $allOk = true;
        foreach ($cartItems as $productId => $item) {
            $product = $data_cart->select_sanpham_id($productId);
            if (!$product || $item['quantity'] > $product[0]['soluong']) {
                $allOk = false;
                break;
            }
        }
        if ($allOk) {
            foreach ($cartItems as $productId => $item) {
                $product = $data_cart->select_sanpham_id($productId);
                $dongia = $product[0]['dongia'];
                $tongtien = $dongia * $item['quantity'];
                $data->insert_muahang($id_user, $productId, 1, $item['quantity'], $dongia, $tongtien, 'chờ xác nhận');
                if ($action === 'buy_selected') {
                    // Chỉ xóa sản phẩm được chọn, giữ lại sản phẩm không chọn
                    if (isset($_SESSION['cart'][$productId]) && isset($_SESSION['cart'][$productId]['selected']) && $_SESSION['cart'][$productId]['selected']) {
                        unset($_SESSION['cart'][$productId]);
                    }
                }
            }
            if ($action === 'buy') {
                unset($_SESSION['cart']);
            }
            // Không xóa toàn bộ giỏ hàng nếu vẫn còn sản phẩm chưa mua
            if (isset($_SESSION['cart']) && empty($_SESSION['cart'])) {
                unset($_SESSION['cart']);
            }
            $message = "Đặt mua thành công";
        } else {
            $message = "Một số sản phẩm không đủ tồn kho";
        }
        header("Location: ../VIEW/lichsumuahang.php?message=" . urlencode($message));
        exit;
        break;

    case 'view':
    default:
        header("Location: ../VIEW/giohang.php");
        exit;
        break;
}
?>
