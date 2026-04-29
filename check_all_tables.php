<?php
$conn = new mysqli('localhost', 'root', '', 'nckhh');
if ($conn->connect_error) {
    echo 'Connection failed: ' . $conn->connect_error;
    exit;
}

echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║         KIỂM TRA TOÀN BỘ DATABASE - TÌNH TRẠNG           ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n\n";

$tables = ['users', 'products', 'categories', 'orders', 'order_items', 'vouchers', 
           'user_vouchers', 'promotions', 'loyalty_rules', 'returns'];

$all_exist = true;
foreach ($tables as $table) {
    $result = $conn->query("SHOW TABLES LIKE '$table'");
    if ($result->num_rows > 0) {
        echo "  ✓ $table\n";
    } else {
        echo "  ✗ $table (KHÔNG TỒN TẠI)\n";
        $all_exist = false;
    }
}

echo "\n";
if ($all_exist) {
    echo "✓✓✓ TẤT CẢ CÁC BẢNG ĐỀU TỒN TẠI! ✓✓✓\n";
} else {
    echo "⚠ CÓ BẢNG BỊ THIẾU - HÃY KIỂM TRA LẠI!\n";
}

$conn->close();
?>
