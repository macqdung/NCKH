<?php
session_start();
include('../MODEL/modelmh.php');
$get_data = new data_muahang();

$id_user = isset($_SESSION['ID_user']) ? $_SESSION['ID_user'] : 1; // fallback 1 nếu chưa đăng nhập

// Xử lý đặt mua
$message = '';
if (isset($_POST['datmua'])) {
    if (!isset($_SESSION['ID_user'])) {
        header("Location: /SANPHAMMOI/VIEW/dangnhap.php?redirect=muahang.php&mua=" . $_POST['id_sanpham']);
        exit;
    }
    $id_sanpham = intval($_POST['id_sanpham']);
    $solanmua = 1;
    $soluong = intval($_POST['txtmua']);
    $dongia = floatval($_POST['dongia']);
    $tongtien = $dongia * $soluong;
    $trangthai = 'chờ xác nhận';
                $voucher_id = isset($_POST['voucher_id']) ? intval($_POST['voucher_id']) : null;

                $insert = $get_data->insert_muahang($id_user, $id_sanpham, $solanmua, $soluong, $dongia, $tongtien, $trangthai, null, $voucher_id);
    if (is_array($insert) && $insert['success']) {
        $discount_msg = $insert['discount'] > 0 ? " (Giảm giá: " . number_format($insert['discount'], 0, ',', '.') . " 円)" : "";
        header("Location: /SANPHAMMOI/VIEW/lichsumuahang.php?message=Đã đặt hàng thành công và đang chờ xác nhận!" . $discount_msg);
        exit;
    } elseif (is_array($insert) && !$insert['success']) {
        $message = $insert['message'];
    } else {
        $message = 'Đặt hàng thất bại! (Kiểm tra số lượng tồn kho)';
    }
}

// Nếu không có tham số mua, lấy danh sách sản phẩm
if (!isset($_GET['mua']) || empty($_GET['mua'])) {
    global $conn;
    $sql = "SELECT ID_sanpham, tensanpham, soluong, dongia FROM sanpham";
    $run = mysqli_query($conn, $sql);
    $ds_sanpham = [];
    while ($row = mysqli_fetch_assoc($run)) {
        $ds_sanpham[] = $row;
    }
    $sp = null;
} else {
    // Lấy thông tin sản phẩm theo ID
    $id_sanpham = intval($_GET['mua']);
    $sp_arr = $get_data->select_sanpham_id($id_sanpham);
    $sp = !empty($sp_arr) ? $sp_arr[0] : null;
    $ds_sanpham = null;
}

?>
