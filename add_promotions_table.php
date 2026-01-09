<?php
include_once 'MODEL/connect.php';

global $conn;

// Tạo bảng promotions nếu chưa tồn tại
$sql = "CREATE TABLE IF NOT EXISTS promotions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    discount_type ENUM('percentage', 'fixed') NOT NULL,
    discount_value DECIMAL(10,2) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    applicable_products TEXT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "Bảng promotions đã được tạo thành công hoặc đã tồn tại.<br>";
} else {
    echo "Lỗi khi tạo bảng: " . $conn->error . "<br>";
}

$conn->close();
?>
