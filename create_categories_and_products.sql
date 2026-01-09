-- Drop existing tables if needed for clean setup (optional)
DROP TABLE IF EXISTS user_vouchers;
DROP TABLE IF EXISTS vouchers;
DROP TABLE IF EXISTS promotions;
DROP TABLE IF EXISTS loyalty_rules;
DROP TABLE IF EXISTS returns;
DROP TABLE IF EXISTS muahangg;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS users;

-- Create categories table
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) UNIQUE NOT NULL
);

-- Insert categories aligned with media keywords and product mapping
INSERT INTO categories (name) VALUES
('Science Fiction'),
('Fantasy'),
('Romance'),
('History'),
('Technology'),
('Self-help');

-- Create products table
CREATE TABLE products (
    ID_sanpham INT AUTO_INCREMENT PRIMARY KEY,
    tensanpham VARCHAR(255) NOT NULL,
    mota TEXT,
    hinhanh VARCHAR(255),
    soluong INT DEFAULT 10,
    dongia INT DEFAULT 100000,
    category INT,
    author VARCHAR(255),
    publisher VARCHAR(255),
    isbn VARCHAR(20),
    subcategory_id INT DEFAULT NULL,
    FOREIGN KEY (category) REFERENCES categories(id)
);

-- Insert example products matching media images and category IDs

INSERT INTO products (tensanpham, mota, hinhanh, soluong, dongia, category, author, publisher, isbn) VALUES
('Dune', 'Epic Science Fiction novel', 'dune.jpg', 10, 150000, 1, 'Frank Herbert', 'Chilton Books', '9780441013593'),
('The Hobbit', 'Fantasy classic by J.R.R. Tolkien', 'hobbit.jpg', 10, 120000, 2, 'J.R.R. Tolkien', 'George Allen & Unwin', '9780547928227'),
('Pride and Prejudice', 'Romantic novel by Jane Austen', 'pride.jpg', 10, 90000, 3, 'Jane Austen', 'T. Egerton', '9780141439518'),
('Sapiens', 'A Brief History of Humankind', 'sapiens.jpg', 10, 130000, 4, 'Yuval Noah Harari', 'Harvill Secker', '9780099590088'),
('Clean Code', 'A Handbook of Agile Software Craftsmanship', 'clean_code.jpg', 10, 150000, 5, 'Robert C. Martin', 'Prentice Hall', '9780132350884'),
('The Power of Habit', 'Why We Do What We Do', 'power_of_habit.jpg', 10, 110000, 6, 'Charles Duhigg', 'Random House', '9781400069286');

-- Add other product insert statements here following the pattern above for all media images

-- Additional tables like users, vouchers, promotions, loyalty_rules, returns, etc. should be created and seeded as per your existing schema for completeness.
