<?php
$conn = new mysqli('localhost', 'root', '', 'nckhh');
if ($conn->connect_error) {
    echo 'Connection failed: ' . $conn->connect_error;
    exit;
}

echo "=== Kiểm tra bảng loyalty_rules ===\n";
$result = $conn->query('SHOW TABLES LIKE "loyalty_rules"');
if ($result->num_rows === 0) {
    echo "⚠ Bảng loyalty_rules KHÔNG tồn tại!\n";
    echo "Đang tạo bảng loyalty_rules...\n";
    
    $createTable = "CREATE TABLE loyalty_rules (
        id INT AUTO_INCREMENT PRIMARY KEY,
        points_per_vnd DECIMAL(10, 2) DEFAULT 1,
        min_order_for_points DECIMAL(10, 2) DEFAULT 100000,
        redemption_rate DECIMAL(10, 2) DEFAULT 1000,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    
    if ($conn->query($createTable)) {
        echo "✓ Bảng loyalty_rules đã được tạo thành công!\n";
        
        // Thêm dữ liệu mặc định
        $insertDefault = "INSERT INTO loyalty_rules (points_per_vnd, min_order_for_points, redemption_rate) 
                         VALUES (1, 100000, 1000)";
        if ($conn->query($insertDefault)) {
            echo "✓ Dữ liệu mặc định đã được thêm.\n";
        }
    } else {
        echo "✗ Lỗi tạo bảng: " . $conn->error . "\n";
    }
} else {
    echo "✓ Bảng loyalty_rules tồn tại.\n";
}

$conn->close();
?>
