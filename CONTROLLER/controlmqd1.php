<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include('../MODEL/modelmqd1.php');
$get_data = new data_mqd1();

$id_user = isset($_SESSION['ID_user']) ? $_SESSION['ID_user'] : 1; // fallback 1 nếu chưa đăng nhập

$message = '';
if (isset($_POST['datmua'])) {
    $id_sanpham = intval($_POST['id_sanpham']);
    $solanmua = 1;
    $soluong = intval($_POST['txtmua']);
    $dongia = floatval($_POST['dongia']);
    $tongtien = $dongia * $soluong;
$trangthai = 'chờ xác nhận';

    // Validate quantity
    if ($soluong < 1) {
        $message = 'Số lượng mua phải lớn hơn hoặc bằng 1.';
    } else {
        // Get current product quantity
        $product = $get_data->getProductById($id_sanpham);
        if ($product && $soluong <= $product['soluong']) {
            // Insert purchase
            $insert = $get_data->insert_muahang($id_user, $id_sanpham, $solanmua, $soluong, $dongia, $tongtien, $trangthai);
            if ($insert) {
                // Update product quantity
                $new_quantity = $product['soluong'] - $soluong;
                $update_sql = "UPDATE products SET soluong = $new_quantity WHERE ID_sanpham = $id_sanpham";
                global $conn;
                mysqli_query($conn, $update_sql);
                $message = 'Đặt mua thành công!';
            } else {
                $message = 'Đặt mua thất bại!';
            }
        } else {
            $message = 'Số lượng mua vượt quá số lượng hiện có.';
        }
    }
}

// Nếu không có tham số mua, lấy danh sách sản phẩm
if (!isset($_GET['mua']) || empty($_GET['mua'])) {
    $ds_sanpham = $get_data->select_all_sanpham();
    $sp = null;
} else {
    // Lấy thông tin sản phẩm theo ID
    $id_sanpham = intval($_GET['mua']);
    $sp_arr = $get_data->select_sanpham_id($id_sanpham);
    $sp = !empty($sp_arr) ? $sp_arr[0] : null;
    $ds_sanpham = null;
}

?>
