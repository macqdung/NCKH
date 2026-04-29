<?php
$conn = new mysqli('localhost', 'root', '', 'nckhh');
if ($conn->connect_error) {
    echo 'Connection failed: ' . $conn->connect_error;
    exit;
}

echo "=== Kiểm tra bảng returns ===\n";
$result = $conn->query('SHOW TABLES LIKE "returns"');
if ($result->num_rows === 0) {
    echo "⚠ Bảng returns KHÔNG tồn tại!\n";
    echo "Đang tạo bảng returns...\n";
    
    $createTable = "CREATE TABLE returns (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        user_id INT NOT NULL,
        product_id INT NOT NULL,
        reason VARCHAR(255),
        status VARCHAR(50) DEFAULT 'pending',
        request_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (order_id) REFERENCES orders(id),
        FOREIGN KEY (user_id) REFERENCES users(ID_user),
        FOREIGN KEY (product_id) REFERENCES products(ID_sanpham)
    )";
    
    if ($conn->query($createTable)) {
        echo "✓ Bảng returns đã được tạo thành công!\n";
    } else {
        echo "✗ Lỗi tạo bảng: " . $conn->error . "\n";
    }
} else {
    echo "✓ Bảng returns tồn tại.\n";
}

$conn->close();
?>
