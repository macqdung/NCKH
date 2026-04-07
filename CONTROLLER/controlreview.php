<?php
include_once('../MODEL/modelreview.php');
include_once('../MODEL/modelmh.php');

class control_review {
    private $reviewModel;
    private $orderModel;
    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
        $this->reviewModel = new ModelReview();
        $this->orderModel = new data_muahang();
    }

    public function submit_review($product_id, $user_id, $rating, $comment, $images = []) {
        // 1. Kiểm tra xem người dùng đã mua sản phẩm chưa
        $orders = $this->orderModel->select_muahang_by_user($user_id);
        $hasPurchased = false;
        $orderId = 0;

        foreach ($orders as $order) {
            // Kiểm tra sản phẩm và trạng thái đơn hàng (ví dụ: đã giao hàng thành công)
            if ($order['ID_sanpham'] == $product_id && $order['trangthai'] == 'đã giao hàng thành công') {
                $hasPurchased = true;
                $orderId = $order['id'];
                break;
            }
        }

        if (!$hasPurchased) {
            return ['success' => false, 'message' => 'Bạn chỉ có thể đánh giá sản phẩm đã mua và được giao thành công.'];
        }

        // 2. Lấy tên đăng nhập từ user_id (vì bảng danhgia dùng tên đăng nhập)
        $username = '';
        $stmt = $this->conn->prepare("SELECT tendangnhap FROM users WHERE ID_user = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($row = $res->fetch_assoc()) {
            $username = $row['tendangnhap'];
        }
        $stmt->close();

        // 3. Kiểm tra xem đã đánh giá chưa
        $reviews = $this->reviewModel->getReviewsByProduct($product_id);
        foreach ($reviews as $review) {
            if ($review['user'] === $username) {
                 return ['success' => false, 'message' => 'Bạn đã đánh giá sản phẩm này rồi.'];
            }
        }

        // 4. Thêm đánh giá
        if ($this->reviewModel->addReview($username, $comment, $rating, $orderId, $product_id)) {
            return ['success' => true, 'message' => 'Đánh giá của bạn đã được gửi thành công!'];
        } else {
            return ['success' => false, 'message' => 'Có lỗi xảy ra khi gửi đánh giá.'];
        }
    }

    public function get_reviews($product_id) {
        return $this->reviewModel->getReviewsByProduct($product_id);
    }
}
?>
