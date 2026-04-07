<?php
class ModelReview {
    private $conn;

    public function __construct() {
        // Giả sử biến kết nối $conn được khai báo global từ file config/database
        global $conn;
        $this->conn = $conn;
    }

    // Lấy danh sách đánh giá theo ID sản phẩm
    public function getReviewsByProduct($product_id) {
        // Kiểm tra kết nối
        if (!$this->conn) {
            return [];
        }

        $sql = "SELECT * FROM danhgia WHERE product_id = ? ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $reviews = [];
        while ($row = $result->fetch_assoc()) {
            $reviews[] = $row;
        }
        return $reviews;
    }

    // Thêm đánh giá mới
    public function addReview($user, $comment, $rating, $order_id, $product_id) {
        $sql = "INSERT INTO danhgia (user, comment, rating, order_id, product_id) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        // user: string, comment: string, rating: int, order_id: int, product_id: int
        $stmt->bind_param("ssiii", $user, $comment, $rating, $order_id, $product_id);
        
        return $stmt->execute();
    }
}
?>