<?php
include_once 'connect.php';

global $conn;

echo "<h3>Kiểm tra và cập nhật cấu trúc bảng 'orders'</h3>";

// 1. Kiểm tra xem bảng orders có tồn tại không
$check_table = $conn->query("SHOW TABLES LIKE 'orders'");
if ($check_table->num_rows == 0) {
    echo "Bảng 'orders' chưa tồn tại. Đang tạo mới...<br>";
    $sql_create = "CREATE TABLE orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        id_user INT NOT NULL,
        tongtien DECIMAL(10,2),
        trangthai VARCHAR(50),
        payment_method VARCHAR(50) DEFAULT 'COD',
        payment_status VARCHAR(50) DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        paid_at TIMESTAMP NULL,
        delivered_at TIMESTAMP NULL,
        FOREIGN KEY (id_user) REFERENCES users(ID_user)
    )";
    if ($conn->query($sql_create)) {
        echo "-> Tạo bảng 'orders' thành công.<br>";
    } else {
        echo "-> Lỗi tạo bảng: " . $conn->error . "<br>";
    }
} else {
    echo "Bảng 'orders' đã tồn tại. Đang kiểm tra các cột còn thiếu...<br>";
    
    // 2. Danh sách các cột cần có
    $columns_needed = [
        'payment_method' => "VARCHAR(50) DEFAULT 'COD'",
        'payment_status' => "VARCHAR(50) DEFAULT 'pending'",
        'paid_at' => "TIMESTAMP NULL",
        'delivered_at' => "TIMESTAMP NULL"
    ];

    // Lấy danh sách cột hiện tại
    $result = $conn->query("DESCRIBE orders");
    $existing_columns = [];
    while ($row = $result->fetch_assoc()) {
        $existing_columns[] = $row['Field'];
    }

    // Thêm cột nếu chưa có
    foreach ($columns_needed as $col => $def) {
        if (!in_array($col, $existing_columns)) {
            echo "-> Đang thêm cột '$col'... ";
            $sql_alter = "ALTER TABLE orders ADD COLUMN $col $def";
            if ($conn->query($sql_alter)) {
                echo "Thành công.<br>";
            } else {
                echo "Thất bại: " . $conn->error . "<br>";
            }
        } else {
            echo "-> Cột '$col' đã tồn tại.<br>";
        }
    }
}

echo "<br><strong>Hoàn tất! Hãy thử đặt hàng lại.</strong>";
$conn->close();
?>