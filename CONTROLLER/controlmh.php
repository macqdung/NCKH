<?php
session_start();

include('../MODEL/connect.php');
include('../MODEL/modelmh.php');

$get_data = new data_muahang();

$id_user = isset($_SESSION['ID_user']) ? $_SESSION['ID_user'] : null;

$message = '';

/* =========================
   XU LY DAT MUA
========================= */
if (isset($_POST['datmua'])) {

    if (!$id_user) {
        header("Location: dangnhap.php");
        exit;
    }

    $id_sanpham = intval($_POST['id_sanpham']);
    $solanmua = 1;
    $soluong = intval($_POST['txtmua']);
    $dongia = floatval($_POST['dongia']);
    $tongtien = $dongia * $soluong;
    $trangthai = 'chờ xác nhận';
    $voucher_id = isset($_POST['voucher_id']) ? intval($_POST['voucher_id']) : null;

    $insert = $get_data->insert_muahang(
        $id_user,
        $id_sanpham,
        $solanmua,
        $soluong,
        $dongia,
        $tongtien,
        $trangthai,
        null,
        $voucher_id
    );

    if (is_array($insert) && $insert['success']) {

        $discount_msg = '';
        if ($insert['discount'] > 0) {
            $discount_msg = " (Giảm giá: " .
                number_format($insert['discount'], 0, ',', '.') . " 円)";
        }

        header("Location: lichsumuahang.php?message=Đã đặt hàng thành công!" . $discount_msg);
        exit;

    } elseif (is_array($insert)) {
        $message = $insert['message'];
    } else {
        $message = "Đặt hàng thất bại!";
    }
}


/* =========================
   LAY DANH SACH SAN PHAM
========================= */

if (!isset($_GET['mua']) || empty($_GET['mua'])) {

    $sql = "SELECT ID_sanpham, tensanpham, soluong, dongia 
            FROM products";

    $run = mysqli_query($conn, $sql);

    $ds_sanpham = [];

    if ($run) {
        while ($row = mysqli_fetch_assoc($run)) {
            $ds_sanpham[] = $row;
        }
    }

    $sp = null;

} else {

    $id_sanpham = intval($_GET['mua']);
    $sp_arr = $get_data->select_sanpham_id($id_sanpham);

    $sp = !empty($sp_arr) ? $sp_arr[0] : null;
    $ds_sanpham = null;
}
?>