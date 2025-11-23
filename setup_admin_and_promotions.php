<?php
include_once 'MODEL/connect.php';

global $conn;

// Check if 'role' column exists in users table
$result = $conn->query("DESCRIBE users");
$columns = [];
while ($row = $result->fetch_assoc()) {
    $columns[] = $row['Field'];
}

if (!in_array('role', $columns)) {
    // Add the 'role' column
    $alter_sql = "ALTER TABLE users ADD COLUMN role ENUM('admin', 'nhanvien', 'user') DEFAULT 'user'";
    if ($conn->query($alter_sql) === TRUE) {
        echo "Column 'role' added successfully.\n";
    } else {
        echo "Error adding column: " . $conn->error . "\n";
    }
} else {
    echo "Column 'role' already exists.\n";
}

// Check if admin account exists
$check_admin = $conn->prepare("SELECT ID_user FROM users WHERE tendangnhap = ?");
$admin_username = 'macquangdung';
$check_admin->bind_param("s", $admin_username);
$check_admin->execute();
$result = $check_admin->get_result();

if ($result->num_rows == 0) {
    // Insert admin account with correct hash for '01062006'
    $hashed_password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'; // password_hash('01062006', PASSWORD_DEFAULT);
    $insert_admin = $conn->prepare("INSERT INTO users (tendangnhap, matkhau, role) VALUES (?, ?, 'admin')");
    $insert_admin->bind_param("ss", $admin_username, $hashed_password);
    
    if ($insert_admin->execute()) {
        echo "Admin account created successfully.\n";
    } else {
        echo "Error creating admin account: " . $conn->error . "\n";
    }
} else {
    echo "Admin account already exists.\n";
}

// Create promotions table if not exists
$create_promotions = "CREATE TABLE IF NOT EXISTS promotions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    discount_type ENUM('percentage', 'fixed') NOT NULL,
    discount_value DECIMAL(10,2) NOT NULL,
    start_date DATE,
    end_date DATE,
    applicable_products TEXT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($create_promotions) === TRUE) {
    echo "Promotions table created successfully.\n";
} else {
    echo "Error creating promotions table: " . $conn->error . "\n";
}

$conn->close();
?>
