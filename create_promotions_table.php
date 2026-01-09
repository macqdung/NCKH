<?php
include 'MODEL/connect.php';

global $conn;

$sql = "CREATE TABLE IF NOT EXISTS promotions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    discount_type ENUM('percentage', 'fixed') NOT NULL,
    discount_value DECIMAL(10, 2) NOT NULL,
    start_date DATETIME,
    end_date DATETIME,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "Bảng 'promotions' đã được tạo thành công hoặc đã tồn tại.";
} else {
    echo "Lỗi khi tạo bảng 'promotions': " . $conn->error;
}

$conn->close();
?>