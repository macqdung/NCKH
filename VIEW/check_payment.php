<?php
include_once '../MODEL/connect.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
    $action = isset($_POST['action']) ? $_POST['action'] : '';

    if ($order_id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid Order ID']);
        exit;
    }

    global $conn;

    if ($action === 'confirm') {
        // Giả lập thanh toán thành công
        // Cập nhật: payment_status = 'da thanh toan', trangthai = 'chờ xác nhận', paid_at = NOW()
        $sql = "UPDATE orders 
                SET payment_status = 'da thanh toan', 
                    trangthai = 'chờ xác nhận', 
                    paid_at = NOW() 
                WHERE id = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $order_id);
        
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Payment confirmed']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $conn->error]);
        }
        $stmt->close();

    } elseif ($action === 'timeout') {
        // Hết giờ: payment_status = 'that bai', trangthai = 'đã hủy'
        $sql = "UPDATE orders SET payment_status = 'that bai', trangthai = 'huy' WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $order_id);
        
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Order cancelled due to timeout']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Database error']);
        }
        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
    }
}
$conn->close();
?>