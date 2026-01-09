<?php
if (file_exists(__DIR__ . '/connect.php')) {
    include_once(__DIR__ . '/connect.php');
}

class data_danhgia
{
    /**
     * Lấy tất cả các bài đánh giá.
     */
    public function getReviews()
    {
        global $conn;
        $data = [];
        // Join with products to get product details
        $stmt = $conn->prepare("
            SELECT
                d.user, d.comment, d.rating, d.created_at,
                p.tensanpham, p.hinhanh, p.ID_sanpham
            FROM danhgia d
            JOIN products p ON d.product_id = p.ID_sanpham
            ORDER BY d.created_at DESC
        ");
        if ($stmt) {
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            $stmt->close();
        }
        return $data;
    }

    /**
     * Thêm một bài đánh giá mới.
     */
    public function addReview($user, $comment, $rating, $order_id, $product_id)
    {
        global $conn;
        // Sử dụng tên bảng là 'danhgia'
        $stmt = $conn->prepare("INSERT INTO danhgia (user, comment, rating, order_id, product_id, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        if ($stmt) {
            $stmt->bind_param("ssiii", $user, $comment, $rating, $order_id, $product_id);
            $result = $stmt->execute();
            $stmt->close();
            return $result;
        }
        return false;
    }

    /**
     * Lấy đánh giá theo sản phẩm và người dùng.
     */
    public function getReviewByProductAndUser($product_id, $username)
    {
        global $conn;
        // Sử dụng tên bảng là 'danhgia'
        $stmt = $conn->prepare("SELECT * FROM danhgia WHERE product_id = ? AND user = ? ORDER BY created_at DESC LIMIT 1");
        if ($stmt) {
            $stmt->bind_param("is", $product_id, $username);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            return $result;
        }
        return null;
    }
}
?>