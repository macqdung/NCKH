 <?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user'])) {
    header("Location: ../VIEW/dangnhap.php");
    exit;
}

include('../MODEL/modeldangnhap.php');
include('../MODEL/modelvouchers.php');

$userModel = new data_user_login();
$voucher_model = new data_vouchers();

$user = $userModel->get_user_by_username($_SESSION['user']);
if (!$user) {
    header("Location: ../VIEW/dangnhap.php?error=invalid_user");
    exit;
}
$id_user = $user['ID_user'];

$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['claim_voucher'])) {
    $voucher_code = trim($_POST['voucher_code']);
    if (!empty($voucher_code)) {
        $claimed = $voucher_model->claim_voucher($id_user, $voucher_code);
        if ($claimed) {
            $message = 'Voucher đã được nhận thành công!';
        } else {
            $message = 'Không thể nhận voucher. Có thể đã hết hạn, hết lượt sử dụng, hoặc bạn đã nhận rồi.';
        }
    } else {
        $message = 'Vui lòng nhập mã voucher.';
    }
}

// Get claimed and available vouchers
$claimed_vouchers = $voucher_model->get_user_claimed_vouchers($id_user);
$all_vouchers = $voucher_model->select_all_vouchers();
$claimed_ids = array_column($claimed_vouchers, 'id');
$available_vouchers = array_filter($all_vouchers, function($v) use ($claimed_ids) {
    return !in_array($v['id'], $claimed_ids);
});
?>
