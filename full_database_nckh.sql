-- Script tạo toàn bộ cơ sở dữ liệu NCKH

DROP DATABASE IF EXISTS NCKH;
CREATE DATABASE NCKH CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE NCKH;

-- Bảng users
CREATE TABLE IF NOT EXISTS users (
    ID_user INT AUTO_INCREMENT PRIMARY KEY,
    tendangnhap VARCHAR(255) UNIQUE NOT NULL,
    matkhau VARCHAR(255) NOT NULL,
    role ENUM('admin', 'nhanvien', 'user') DEFAULT 'user',
    hoten VARCHAR(255) DEFAULT '',
    sdt VARCHAR(20) DEFAULT NULL,
    email VARCHAR(255) DEFAULT NULL
);

-- Bảng products
CREATE TABLE IF NOT EXISTS products (
    ID_sanpham INT AUTO_INCREMENT PRIMARY KEY,
    tensanpham VARCHAR(255) NOT NULL,
    mota TEXT,
    hinhanh VARCHAR(255),
    soluong INT DEFAULT 0,
    dongia DECIMAL(10,2) DEFAULT 0,
    category VARCHAR(255),
    author VARCHAR(255),
    publisher VARCHAR(255),
    isbn VARCHAR(20),
    subcategory_id INT DEFAULT NULL
);

-- Bảng categories
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE
);

-- Bảng subcategories
CREATE TABLE IF NOT EXISTS subcategories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    parent_id INT NOT NULL,
    FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE CASCADE
);

-- Bảng vouchers
CREATE TABLE IF NOT EXISTS vouchers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    type ENUM('percentage', 'fixed') NOT NULL,
    value DECIMAL(10,2) NOT NULL,
    min_order DECIMAL(10,2) DEFAULT 0,
    max_uses_total INT DEFAULT NULL,
    expiry_date DATE DEFAULT NULL,
    applicable_to ENUM('all', 'specific') DEFAULT 'all',
    product_ids TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    uses_count INT DEFAULT 0
);

-- Bảng user_vouchers
CREATE TABLE IF NOT EXISTS user_vouchers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    voucher_id INT NOT NULL,
    claimed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(ID_user) ON DELETE CASCADE,
    FOREIGN KEY (voucher_id) REFERENCES vouchers(id) ON DELETE CASCADE
);

-- Bảng user_points
CREATE TABLE IF NOT EXISTS user_points (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    points DECIMAL(10,2) NOT NULL,
    transaction_type VARCHAR(50) NOT NULL,
    description VARCHAR(255),
    date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(ID_user) ON DELETE CASCADE
);

-- Bảng muahangg (đơn hàng)
CREATE TABLE IF NOT EXISTS muahangg (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    ID_user INT NOT NULL,
    ID_sanpham INT NOT NULL,
    solanmua INT DEFAULT 1,
    soluong INT NOT NULL,
    dongia DECIMAL(10,2) NOT NULL,
    tongtien DECIMAL(10,2) NOT NULL,
    trangthai VARCHAR(50) DEFAULT 'chờ xác nhận',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    delivered_at TIMESTAMP NULL,
    FOREIGN KEY (ID_user) REFERENCES users(ID_user),
    FOREIGN KEY (ID_sanpham) REFERENCES products(ID_sanpham)
);

-- Bảng danhgia (đánh giá)
CREATE TABLE IF NOT EXISTS danhgia (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user VARCHAR(255) NOT NULL,
    comment TEXT,
    rating INT DEFAULT 5,
    order_id INT,
    product_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(ID_sanpham)
);

-- Bảng returns (trả hàng)
CREATE TABLE IF NOT EXISTS returns (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    reason TEXT,
    request_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    notes TEXT,
    processed_date TIMESTAMP NULL,
    FOREIGN KEY (order_id) REFERENCES muahangg(ID),
    FOREIGN KEY (user_id) REFERENCES users(ID_user),
    FOREIGN KEY (product_id) REFERENCES products(ID_sanpham)
);

-- Bảng promotions (khuyến mãi)
CREATE TABLE IF NOT EXISTS promotions (
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
);

-- Bảng loyalty_rules (quy tắc tích điểm)
CREATE TABLE IF NOT EXISTS loyalty_rules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    points_per_vnd DECIMAL(5,2) DEFAULT 0,
    min_order_for_points DECIMAL(10,2) DEFAULT 0,
    redemption_rate DECIMAL(5,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Bảng voucher_usage (sử dụng voucher)
CREATE TABLE IF NOT EXISTS voucher_usage (
    id INT AUTO_INCREMENT PRIMARY KEY,
    voucher_id INT NOT NULL,
    user_id INT NOT NULL,
    order_id INT NOT NULL,
    discount_amount DECIMAL(10,2) NOT NULL,
    used_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (voucher_id) REFERENCES vouchers(id),
    FOREIGN KEY (user_id) REFERENCES users(ID_user),
    FOREIGN KEY (order_id) REFERENCES muahangg(ID)
);

-- Chèn dữ liệu mẫu

-- Dữ liệu categories
INSERT INTO categories (name) VALUES ('Sách'), ('Văn phòng phẩm'), ('Điện tử');

-- Dữ liệu subcategories
INSERT INTO subcategories (name, parent_id) VALUES ('Tiểu thuyết', 1), ('Sách giáo khoa', 1), ('Bút', 2), ('Vở', 2), ('Điện thoại', 3);

-- Dữ liệu products
INSERT INTO products (tensanpham, mota, hinhanh, soluong, dongia, category, author, publisher, isbn, subcategory_id) VALUES
('Tôi thấy hoa vàng trên cỏ xanh', 'Tiểu thuyết của Nguyễn Nhật Ánh', 'hoa_vang.jpg', 100, 50000.00, 'Sách', 'Nguyễn Nhật Ánh', 'NXB Trẻ', '9786041050000', 1),
('Toán học 12', 'Sách giáo khoa toán lớp 12', 'toan12.jpg', 50, 30000.00, 'Sách', 'Bộ GD&ĐT', 'NXB Giáo dục', '9786041050001', 2),
('Bút bi Thiên Long', 'Bút bi màu đen', 'but_bi.jpg', 200, 5000.00, 'Văn phòng phẩm', NULL, 'Thiên Long', NULL, 3),
('Vở học sinh', 'Vở 200 trang', 'vo.jpg', 150, 10000.00, 'Văn phòng phẩm', NULL, 'Thiên Long', NULL, 4),
('Ốp điện thoại iPhone', 'Ốp silicon cho iPhone', 'op_dt.jpg', 80, 50000.00, 'Điện tử', NULL, 'Samsung', NULL, 5);

-- Dữ liệu users (bao gồm admin)
INSERT INTO users (tendangnhap, matkhau, role, hoten, sdt, email) VALUES
('macquangdung', '$2y$10$abcdefghijklmnopqrstuvwxABCDEFGHIJK', 'admin', 'Mạc Quang Dũng', '0123456789', 'macquangdung@example.com'),
('user1', '$2y$10$hashedpassword1', 'user', 'Nguyễn Văn A', '0987654321', 'user1@example.com'),
('user2', '$2y$10$hashedpassword2', 'user', 'Trần Thị B', '0912345678', 'user2@example.com');

-- Dữ liệu vouchers
INSERT INTO vouchers (code, type, value, min_order, max_uses_total, expiry_date, applicable_to) VALUES
('WELCOME10', 'percentage', 10.00, 100000.00, 100, '2024-12-31', 'all'),
('BOOK20', 'fixed', 20000.00, 200000.00, 50, '2024-11-30', 'specific');

-- Dữ liệu loyalty_rules
INSERT INTO loyalty_rules (points_per_vnd, min_order_for_points, redemption_rate) VALUES (0.01, 50000.00, 0.01);

-- Dữ liệu muahangg
INSERT INTO muahangg (ID_user, ID_sanpham, solanmua, soluong, dongia, tongtien, trangthai) VALUES
(2, 1, 1, 1, 50000.00, 50000.00, 'đã giao hàng thành công'),
(3, 3, 1, 2, 5000.00, 10000.00, 'chờ xác nhận');

-- Dữ liệu danhgia
INSERT INTO danhgia (user, comment, rating, order_id, product_id) VALUES
('user1', 'Sách hay lắm!', 5, 1, 1),
('user2', 'Bút viết tốt.', 4, 2, 3);

-- Dữ liệu user_points
INSERT INTO user_points (user_id, points, transaction_type, description) VALUES
(2, 500.00, 'purchase', 'Tích điểm từ đơn hàng'),
(3, 100.00, 'purchase', 'Tích điểm từ đơn hàng');
