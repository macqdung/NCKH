<?php
session_start();
include_once '../MODEL/connect.php';
include_once '../MODEL/modelmh.php';
include_once '../MODEL/modeldangnhap.php';

// --- KIỂM TRA ĐĂNG NHẬP ---
if (!isset($_SESSION['user'])) {
    header("Location: dangnhap.php");
    exit;
}

$userModel = new data_user_login();
$user = $userModel->get_user_by_username($_SESSION['user']);
$id_user = $user['ID_user'];
$data_mh = new data_muahang();
global $conn;

$order = null;
$remaining_time = 0;
$qr_url = '';
$display_mode = 'choose_method'; // 'choose_method' hoặc 'show_qr'

// --- BƯỚC 2: TẠO ĐƠN HÀNG VÀ XỬ LÝ PHƯƠNG THỨC THANH TOÁN ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['payment_method'])) {
    $payment_method = $_POST['payment_method'];
    $id_sanpham = intval($_POST['id_sanpham']);
    $soluong = intval($_POST['soluong']);
    $dongia = floatval($_POST['dongia']);
    $voucher_id = isset($_POST['voucher_id']) ? intval($_POST['voucher_id']) : null;
    
    // Tính toán tổng tiền tạm thời, model sẽ tính lại chính xác với voucher
    $tongtien = $dongia * $soluong; 
    $trangthai = 'chờ xác nhận';

    // Gọi hàm insert_muahang đã được sửa đổi
    $insert_result = $data_mh->insert_muahang($id_user, $id_sanpham, 1, $soluong, $dongia, $tongtien, $trangthai, $payment_method, $voucher_id);

    if (is_array($insert_result) && $insert_result['success']) {
        $order_id = $insert_result['order_id'];

        if ($payment_method == 'COD') {
            // Với COD, chuyển thẳng đến lịch sử mua hàng
            header('Location: lichsumuahang.php?message=Đặt hàng thành công! Nhân viên sẽ sớm liên hệ với bạn.');
            exit;
        } 
        // Với 'bank', hiển thị trang QR
        else {
            $stmt = $conn->prepare("SELECT * FROM orders WHERE id = ? AND id_user = ? LIMIT 1");
            $stmt->bind_param("ii", $order_id, $id_user);
            $stmt->execute();
            $order = $stmt->get_result()->fetch_assoc();
            
            $created_time = strtotime($order['created_at']);
            $expire_time = $created_time + (10 * 60); // 10 phút
            $remaining_time = $expire_time - time();

            if ($remaining_time <= 0) {
                $update_sql = "UPDATE orders SET payment_status = 'that bai', trangthai = 'đã hủy' WHERE id = ?";
                $stmt_update = $conn->prepare($update_sql);
                $stmt_update->bind_param("i", $order_id);
                $stmt_update->execute();
                die("Đơn hàng đã hết hạn thanh toán ngay khi tạo. Vui lòng thử lại.");
            }

            $qr_content = "Thanh toan don hang #" . $order['id'];
            $qr_url = "https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=" . urlencode($qr_content);
            $display_mode = 'show_qr';
        }
    } else {
        die("Lỗi khi tạo đơn hàng: " . ($insert_result['message'] ?? 'Vui lòng thử lại.'));
    }
}
// --- BƯỚC 1: NHẬN THÔNG TIN TỪ muahang.php VÀ HIỂN THỊ LỰA CHỌN ---
elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['datmua'])) {
    $id_sanpham = intval($_POST['id_sanpham']);
    $soluong = intval($_POST['txtmua']);
    $dongia = floatval($_POST['dongia']);
    $voucher_id = isset($_POST['voucher_id']) ? intval($_POST['voucher_id']) : null;
    $display_mode = 'choose_method';
} 
// --- XỬ LÝ THANH TOÁN LẠI TỪ lichsumuahang.php ---
elseif (isset($_GET['order_id'])) {
    $order_id = intval($_GET['order_id']);
    $stmt = $conn->prepare("SELECT * FROM orders WHERE id = ? AND id_user = ? LIMIT 1");
    $stmt->bind_param("ii", $order_id, $id_user);
    $stmt->execute();
    $order = $stmt->get_result()->fetch_assoc();
    if (!$order) { die("Đơn hàng không tồn tại hoặc bạn không có quyền truy cập."); }
    if ($order['payment_status'] == 'da thanh toan' || $order['payment_status'] == 'paid') { die("Đơn hàng này đã được thanh toán. <a href='lichsumuahang.php'>Xem lịch sử</a>"); }
    if ($order['trangthai'] == 'đã hủy' || $order['trangthai'] == 'huy' || $order['payment_status'] == 'that bai') { die("Đơn hàng này đã bị hủy hoặc thanh toán thất bại. <a href='lichsumuahang.php'>Quay lại</a>"); }
    
    $created_time = strtotime($order['created_at']);
    $expire_time = $created_time + (10 * 60);
    $remaining_time = $expire_time - time();
    if ($remaining_time <= 0) {
        $update_sql = "UPDATE orders SET payment_status = 'that bai', trangthai = 'huy' WHERE id = ?";
        $stmt_update = $conn->prepare($update_sql);
        $stmt_update->bind_param("i", $order_id);
        $stmt_update->execute();
        die("Đơn hàng đã hết hạn thanh toán. <a href='lichsumuahang.php'>Quay lại</a>");
    }
    $qr_content = "Thanh toan don hang #" . $order['id'];
    $qr_url = "https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=" . urlencode($qr_content);
    $display_mode = 'show_qr';
}
else {
    // Truy cập không hợp lệ
    header('Location: mqd1.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thanh toán</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .payment-container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 15px rgba(0,0,0,0.1); text-align: center; max-width: 400px; width: 100%; }
        .timer { font-size: 24px; color: #d9534f; font-weight: bold; margin: 20px 0; }
        .qr-code img { max-width: 100%; height: auto; border: 1px solid #ddd; padding: 5px; }
        .btn-confirm { background-color: #28a745; color: white; padding: 12px 20px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; width: 100%; margin-top: 20px; transition: background 0.3s; }
        .btn-confirm:hover { background-color: #4cae4c; }
        .order-info { margin-bottom: 20px; text-align: left; border-bottom: 1px solid #eee; padding-bottom: 10px; }
        .note { font-size: 12px; color: #666; margin-top: 10px; }
        .payment-options form { margin-bottom: 15px; }
        .btn-payment-method { width: 100%; padding: 15px; font-size: 16px; border-radius: 8px; border: 1px solid #ddd; background-color: #f8f9fa; cursor: pointer; text-align: left; }
        .btn-payment-method:hover { background-color: #e9ecef; }
        .btn-payment-method i { margin-right: 10px; color: #0d6efd; }
    </style>
</head>
<body>

<div class="payment-container">
    <?php if ($display_mode === 'show_qr'): ?>
        <h2>Thanh toán đơn hàng</h2>
        <div class="order-info">
            <p><strong>Mã đơn hàng:</strong> #<?php echo $order['id']; ?></p>
            <p><strong>Tổng tiền:</strong> <?php echo number_format($order['tongtien'], 0, ',', '.'); ?> VNĐ</p>
            <p><strong>Phương thức:</strong> Chuyển khoản Ngân hàng</p>
        </div>
        <div class="qr-section">
            <p>Quét mã QR để thanh toán</p>
            <div class="qr-code"><img src="<?php echo $qr_url; ?>" alt="QR Code Thanh Toán"></div>
            <div class="timer" id="countdown">10:00</div>
            <p class="note">Vui lòng thanh toán trong vòng 10 phút. Đơn hàng sẽ tự động hủy nếu quá hạn.</p>
            <button class="btn-confirm" onclick="confirmPayment()">Tôi đã thanh toán</button>
        </div>

    <?php elseif ($display_mode === 'choose_method'): ?>
        <h2>Chọn phương thức thanh toán</h2>
        <div class="payment-options">
            <form method="POST" action="thanhtoan.php">
                <input type="hidden" name="id_sanpham" value="<?php echo $id_sanpham; ?>">
                <input type="hidden" name="soluong" value="<?php echo $soluong; ?>">
                <input type="hidden" name="dongia" value="<?php echo $dongia; ?>">
                <?php if ($voucher_id): ?><input type="hidden" name="voucher_id" value="<?php echo $voucher_id; ?>"><?php endif; ?>
                <input type="hidden" name="payment_method" value="COD">
                <button type="submit" class="btn-payment-method">
                    <i class="fas fa-money-bill-wave"></i> Thanh toán khi nhận hàng (COD)
                </button>
            </form>
            <form method="POST" action="thanhtoan.php">
                <input type="hidden" name="id_sanpham" value="<?php echo $id_sanpham; ?>">
                <input type="hidden" name="soluong" value="<?php echo $soluong; ?>">
                <input type="hidden" name="dongia" value="<?php echo $dongia; ?>">
                <?php if ($voucher_id): ?><input type="hidden" name="voucher_id" value="<?php echo $voucher_id; ?>"><?php endif; ?>
                <input type="hidden" name="payment_method" value="bank">
                <button type="submit" class="btn-payment-method">
                    <i class="fas fa-qrcode"></i> Chuyển khoản qua QR Code
                </button>
            </form>
        </div>
    <?php endif; ?>
</div>

<script>
    <?php if ($display_mode === 'show_qr'): ?>
    let timeLeft = <?php echo $remaining_time; ?>;
    const orderId = <?php echo $order['id']; ?>;

    function updateTimer() {
        let minutes = Math.floor(timeLeft / 60);
        let seconds = timeLeft % 60;

        // Thêm số 0 đằng trước nếu < 10
        minutes = minutes < 10 ? "0" + minutes : minutes;
        seconds = seconds < 10 ? "0" + seconds : seconds;

        document.getElementById("countdown").innerHTML = minutes + ":" + seconds;

        if (timeLeft <= 0) {
            clearInterval(timerInterval);
            document.getElementById("countdown").innerHTML = "HẾT GIỜ";
            handleTimeout();
        }
        timeLeft--;
    }

    let timerInterval = setInterval(updateTimer, 1000);
    updateTimer(); // Chạy ngay lập tức

    function confirmPayment() {
        if (confirm("Bạn xác nhận đã chuyển khoản thành công?")) {
            fetch('check_payment.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'order_id=' + orderId + '&action=confirm'
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert("Thanh toán thành công! Đơn hàng đang chờ xác nhận.");
                    window.location.href = 'lichsumuahang.php';
                } else {
                    alert("Lỗi: " + data.message);
                }
            })
            .catch(error => console.error('Error:', error));
        }
    }

    function handleTimeout() {
        fetch('check_payment.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'order_id=' + orderId + '&action=timeout'
        })
        .then(response => response.json())
        .then(data => {
            alert("Đơn hàng đã hết hạn thanh toán và bị hủy.");
            window.location.href = 'lichsumuahang.php';
        });
    }
    <?php endif; ?>
</script>

</body>
</html>
