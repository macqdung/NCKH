<?php
$conn = new mysqli('localhost', 'root', '', 'nckhh');
if ($conn->connect_error) {
    echo 'Connection failed: ' . $conn->connect_error;
    exit;
}

echo "=== Kiểm tra bảng promotions ===\n";
$result = $conn->query('SHOW TABLES LIKE "promotions"');
if ($result->num_rows === 0) {
    echo "⚠ Bảng promotions KHÔNG tồn tại!\n";
    echo "Đang tạo bảng promotions...\n";
    
    $createTable = "CREATE TABLE promotions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        description TEXT,
        discount_type VARCHAR(50),
        discount_value DECIMAL(10, 2),
        start_date DATETIME,
        end_date DATETIME,
        applicable_products TEXT,
        status VARCHAR(50) DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    
    if ($conn->query($createTable)) {
        echo "✓ Bảng promotions đã được tạo thành công!\n";
    } else {
        echo "✗ Lỗi tạo bảng: " . $conn->error . "\n";
    }
} else {
    echo "✓ Bảng promotions tồn tại.\n";
}

$conn->close();
?>
