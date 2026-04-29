<?php
$conn = new mysqli('localhost', 'root', '', 'nckhh');
if ($conn->connect_error) {
    echo 'Connection failed: ' . $conn->connect_error;
    exit;
}

echo "=== Kiểm tra bảng user_vouchers ===\n";
$result = $conn->query('SHOW TABLES LIKE "user_vouchers"');
if ($result->num_rows === 0) {
    echo "LỖI: Bảng user_vouchers KHÔNG tồn tại!\n";
    echo "\nĐang tạo bảng user_vouchers...\n";
    
    $createTable = "CREATE TABLE user_vouchers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        voucher_id INT NOT NULL,
        claimed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(ID_user),
        FOREIGN KEY (voucher_id) REFERENCES vouchers(id)
    )";
    
    if ($conn->query($createTable)) {
        echo "✓ Bảng user_vouchers đã được tạo thành công!";
    } else {
        echo "✗ Lỗi tạo bảng: " . $conn->error;
    }
} else {
    echo "✓ Bảng user_vouchers tồn tại.\n";
    echo "\nCấu trúc bảng:\n";
    $columns = $conn->query('DESCRIBE user_vouchers');
    while ($col = $columns->fetch_assoc()) {
        echo "  - " . $col['Field'] . " (" . $col['Type'] . ")\n";
    }
}

echo "\n=== Kiểm tra bảng vouchers ===\n";
$result = $conn->query('SHOW TABLES LIKE "vouchers"');
if ($result->num_rows === 0) {
    echo "LỖI: Bảng vouchers KHÔNG tồn tại!\n";
} else {
    echo "✓ Bảng vouchers tồn tại.\n";
}

$conn->close();
?>
