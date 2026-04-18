<?php
include_once('connect.php');
class data_admin
{
    // Thêm sản phẩm mới
    public function insert_product($tensanpham, $mota, $hinhanh, $soluong, $dongia, $category, $subcategory_id = null)
    {
        global $conn;
        // Check if product already exists
        $check_sql = "SELECT ID_sanpham FROM products WHERE tensanpham = '$tensanpham'";
        $check_run = mysqli_query($conn, $check_sql);
        if (mysqli_num_rows($check_run) > 0) {
            return false; // Duplicate
        }
        if ($subcategory_id === null) {
            $sql = "INSERT INTO products (tensanpham, mota, hinhanh, soluong, dongia, category)
                    VALUES ('$tensanpham', '$mota', '$hinhanh', '$soluong', '$dongia', '$category')";
        } else {
            $sql = "INSERT INTO products (tensanpham, mota, hinhanh, soluong, dongia, category, subcategory_id)
                    VALUES ('$tensanpham', '$mota', '$hinhanh', '$soluong', '$dongia', '$category', $subcategory_id)";
        }
        $run = mysqli_query($conn, $sql);
        return $run;
    }

    public function delete_product($id)
    {
        global $conn;
        $sql = "DELETE FROM products WHERE ID_sanpham = $id";
        $run = mysqli_query($conn, $sql);
        return $run;
    }

    public function update_price($id, $dongia)
    {
        global $conn;
        $sql = "UPDATE products SET dongia = $dongia WHERE ID_sanpham = $id";
        $run = mysqli_query($conn, $sql);
        return $run;
    }

    public function get_all_books()
    {
        global $conn;
        // Use a subquery to get unique categories to prevent product multiplication if categories table has duplicates
        $sql = "SELECT p.*, c.name as category_name
                FROM products p
                LEFT JOIN (SELECT MIN(id) as id, name FROM categories GROUP BY name) c ON p.category = c.id";
        $result = mysqli_query($conn, $sql);
        $books = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $books[] = $row;
        }
        return $books;
    }

    // Cập nhật số lượng sản phẩm
    public function update_quantity($id, $soluong)
    {
        global $conn;
        $sql = "UPDATE products SET soluong = $soluong WHERE ID_sanpham = $id";
        $run = mysqli_query($conn, $sql);
        return $run;
    }

    // Category and Subcategory Management
    public function add_category($name)
    {
        global $conn;
        // Check if category already exists
        $check = $conn->prepare("SELECT id FROM categories WHERE name = ?");
        $check->bind_param("s", $name);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            $check->close();
            return false; // Duplicate name
        }
        $check->close();

        $stmt = $conn->prepare("INSERT INTO categories (name) VALUES (?)");
        $stmt->bind_param("s", $name);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function get_all_categories()
    {
        global $conn;
        // Group by name to ensure even if DB has duplicates, UI only shows one of each category
        $sql = "SELECT MIN(id) as id, name FROM categories GROUP BY name ORDER BY name ASC";
        $result = mysqli_query($conn, $sql);
        $categories = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $categories[] = $row;
        }
        return $categories;
    }

    public function delete_category($id)
    {
        global $conn;
        // 1. Delete all subcategories under this main category
        mysqli_query($conn, "DELETE FROM subcategories WHERE parent_id = $id");
        
        // 2. Set product categories to NULL for products in this category
        // Note: category column is varchar, so we check if it matches the ID
        mysqli_query($conn, "UPDATE products SET category = NULL WHERE category = '$id'");
        
        // 3. Delete the category itself
        $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->bind_param("i", $id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function delete_all_categories()
    {
        global $conn;
        mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 0");
        mysqli_query($conn, "DELETE FROM subcategories");
        mysqli_query($conn, "UPDATE products SET category = NULL, subcategory_id = NULL");
        $result = mysqli_query($conn, "DELETE FROM categories");
        mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 1");
        return $result;
    }

    public function add_subcategory($name, $parent_id)
    {
        global $conn;
        $stmt = $conn->prepare("INSERT INTO subcategories (name, parent_id) VALUES (?, ?)");
        $stmt->bind_param("si", $name, $parent_id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function get_subcategories_by_category($category_id)
    {
        global $conn;
        $stmt = $conn->prepare("SELECT * FROM subcategories WHERE parent_id = ? ORDER BY name ASC");
        $stmt->bind_param("i", $category_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $subcategories = [];
        while ($row = $result->fetch_assoc()) {
            $subcategories[] = $row;
        }
        $stmt->close();
        return $subcategories;
    }

    public function update_subcategory($id, $name)
    {
        global $conn;
        $stmt = $conn->prepare("UPDATE subcategories SET name = ? WHERE id = ?");
        $stmt->bind_param("si", $name, $id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function delete_subcategory($id)
    {
        global $conn;
        // First, set subcategory_id to NULL for products using this subcategory
        $update_stmt = $conn->prepare("UPDATE products SET subcategory_id = NULL WHERE subcategory_id = ?");
        $update_stmt->bind_param("i", $id);
        $update_stmt->execute();
        $update_stmt->close();

        // Now delete the subcategory
        $stmt = $conn->prepare("DELETE FROM subcategories WHERE id = ?");
        $stmt->bind_param("i", $id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    // Book Management (updated for book attributes)
    public function insert_book($title, $author, $publisher, $isbn, $description, $cover_image, $quantity, $price, $category_id, $subcategory_id)
    {
        global $conn;
        // Check if book already exists by ISBN
        $check_sql = "SELECT ID_sanpham FROM products WHERE isbn = '$isbn'";
        $check_run = mysqli_query($conn, $check_sql);
        if (mysqli_num_rows($check_run) > 0) {
            return false; // Duplicate ISBN
        }
        $stmt = $conn->prepare("INSERT INTO products (tensanpham, author, publisher, isbn, mota, hinhanh, soluong, dongia, category, subcategory_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssidii", $title, $author, $publisher, $isbn, $description, $cover_image, $quantity, $price, $category_id, $subcategory_id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function update_book($id, $title, $author, $publisher, $isbn, $price, $stock, $description, $main_category_id, $subcategory_id)
    {
        global $conn;
        $stmt = $conn->prepare("UPDATE products SET tensanpham=?, author=?, publisher=?, isbn=?, dongia=?, soluong=?, mota=?, category=?, subcategory_id=? WHERE ID_sanpham=?");
        $stmt->bind_param("ssssdissii", $title, $author, $publisher, $isbn, $price, $stock, $description, $main_category_id, $subcategory_id, $id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function delete_book($id)
    {
        global $conn;
        // Handle foreign keys before deletion
        mysqli_query($conn, "DELETE FROM order_items WHERE ID_sanpham = $id");
        mysqli_query($conn, "DELETE FROM danhgia WHERE product_id = $id");
        mysqli_query($conn, "DELETE FROM returns WHERE product_id = $id");
        
        $stmt = $conn->prepare("DELETE FROM products WHERE ID_sanpham=?");
        $stmt->bind_param("i", $id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function delete_all_books()
    {
        global $conn;
        // Safer to use DELETE with foreign key check disabled if wanting to clear everything
        mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 0");
        mysqli_query($conn, "DELETE FROM order_items");
        mysqli_query($conn, "DELETE FROM danhgia");
        mysqli_query($conn, "DELETE FROM returns");
        $result = mysqli_query($conn, "DELETE FROM products");
        mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 1");
        return $result;
    }

    // Voucher CRUD
    public function insert_voucher($code, $type, $value, $min_order, $max_uses_total, $expiry_date, $applicable_to, $product_ids)
    {
        global $conn;
        $stmt = $conn->prepare("INSERT INTO vouchers (code, type, value, min_order, max_uses_total, expiry_date, applicable_to, product_ids) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssddiiss", $code, $type, $value, $min_order, $max_uses_total, $expiry_date, $applicable_to, $product_ids);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function get_all_vouchers()
    {
        global $conn;
        $sql = "SELECT * FROM vouchers ORDER BY created_at DESC";
        $result = mysqli_query($conn, $sql);
        $vouchers = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $vouchers[] = $row;
        }
        return $vouchers;
    }

    public function get_voucher_by_id($id)
    {
        global $conn;
        $stmt = $conn->prepare("SELECT * FROM vouchers WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $voucher = $result->fetch_assoc();
        $stmt->close();
        return $voucher;
    }

    public function update_voucher($id, $code, $type, $value, $min_order, $max_uses_total, $expiry_date, $applicable_to, $product_ids)
    {
        global $conn;
        $stmt = $conn->prepare("UPDATE vouchers SET code=?, type=?, value=?, min_order=?, max_uses_total=?, expiry_date=?, applicable_to=?, product_ids=? WHERE id=?");
        $stmt->bind_param("ssddiisssi", $code, $type, $value, $min_order, $max_uses_total, $expiry_date, $applicable_to, $product_ids, $id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function delete_voucher($id)
    {
        global $conn;
        $stmt = $conn->prepare("DELETE FROM vouchers WHERE id=?");
        $stmt->bind_param("i", $id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function delete_all_vouchers()
    {
        global $conn;
        mysqli_query($conn, "DELETE FROM user_vouchers"); // Delete linked records
        $result = mysqli_query($conn, "DELETE FROM vouchers");
        return $result;
    }

    public function validate_voucher($code, $user_id, $order_total, $product_id = null)
    {
        global $conn;
        $stmt = $conn->prepare("SELECT * FROM vouchers WHERE code=? AND (expiry_date IS NULL OR expiry_date >= CURDATE()) AND (max_uses IS NULL OR uses_count < max_uses)");
        $stmt->bind_param("s", $code);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            if ($order_total < $row['min_order']) {
                return ['valid' => false, 'message' => 'Order total does not meet minimum requirement.'];
            }
            if ($row['applicable_to'] == 'product' && $product_id) {
                $product_ids = json_decode($row['product_ids'], true);
                if (!in_array($product_id, $product_ids)) {
                    return ['valid' => false, 'message' => 'Voucher not applicable to this product.'];
                }
            }
            return ['valid' => true, 'voucher' => $row];
        }
        $stmt->close();
        return ['valid' => false, 'message' => 'Invalid or expired voucher.'];
    
        $stmt = $conn->prepare("SELECT * FROM vouchers WHERE code=? AND (expiry_date IS NULL OR expiry_date >= CURDATE()) AND (max_uses IS NULL OR uses_count < max_uses)");
        $stmt->bind_param("s", $code);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            if ($order_total < $row['min_order']) {
                return ['valid' => false, 'message' => 'Order total does not meet minimum requirement.'];
            }
            if ($row['applicable_to'] == 'product' && $product_id) {
                $product_ids = json_decode($row['product_ids'], true);
                if (!in_array($product_id, $product_ids)) {
                    return ['valid' => false, 'message' => 'Voucher not applicable to this product.'];
                }
            }
            return ['valid' => true, 'voucher' => $row];
        }
        $stmt->close();
        return ['valid' => false, 'message' => 'Invalid or expired voucher.'];
    }

    // Promotion CRUD
    public function insert_promotion($name, $description, $discount_type, $discount_value, $start_date, $end_date, $applicable_products, $status)
    {
        global $conn;
        $stmt = $conn->prepare("INSERT INTO promotions (name, description, discount_type, discount_value, start_date, end_date, applicable_products, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssddsss", $name, $description, $discount_type, $discount_value, $start_date, $end_date, $applicable_products, $status);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function get_all_promotions()
    {
        global $conn;
        $sql = "SELECT * FROM promotions ORDER BY created_at DESC";
        $result = mysqli_query($conn, $sql);
        $promotions = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $promotions[] = $row;
        }
        return $promotions;
    }

    public function update_promotion($id, $name, $description, $discount_type, $discount_value, $start_date, $end_date, $applicable_products, $status)
    {
        global $conn;
        $stmt = $conn->prepare("UPDATE promotions SET name=?, description=?, discount_type=?, discount_value=?, start_date=?, end_date=?, applicable_products=?, status=? WHERE id=?");
        $stmt->bind_param("sssddsssi", $name, $description, $discount_type, $discount_value, $start_date, $end_date, $applicable_products, $status, $id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function delete_promotion($id)
    {
        global $conn;
        $stmt = $conn->prepare("DELETE FROM promotions WHERE id=?");
        $stmt->bind_param("i", $id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function delete_all_promotions()
    {
        global $conn;
        $result = mysqli_query($conn, "DELETE FROM promotions");
        return $result;
    }

    // Loyalty Rules CRUD
    public function insert_loyalty_rule($points_per_vnd, $min_order_for_points, $redemption_rate)
    {
        global $conn;
        $stmt = $conn->prepare("INSERT INTO loyalty_rules (points_per_vnd, min_order_for_points, redemption_rate) VALUES (?, ?, ?)");
        $stmt->bind_param("ddd", $points_per_vnd, $min_order_for_points, $redemption_rate);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function get_loyalty_rules()
    {
        global $conn;
        $sql = "SELECT * FROM loyalty_rules ORDER BY created_at DESC";
        $result = mysqli_query($conn, $sql);
        $rules = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $rules[] = $row;
        }
        return $rules;
    }

    public function update_loyalty_rule($id, $points_per_vnd, $min_order_for_points, $redemption_rate)
    {
        global $conn;
        $stmt = $conn->prepare("UPDATE loyalty_rules SET points_per_vnd=?, min_order_for_points=?, redemption_rate=? WHERE id=?");
        $stmt->bind_param("dddi", $points_per_vnd, $min_order_for_points, $redemption_rate, $id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function delete_loyalty_rule($id)
    {
        global $conn;
        $stmt = $conn->prepare("DELETE FROM loyalty_rules WHERE id=?");
        $stmt->bind_param("i", $id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function update_loyalty_rules($points_per_purchase, $points_per_vnd, $redemption_rate, $min_points_redemption)
    {
        global $conn;
        $stmt = $conn->prepare("UPDATE loyalty_rules SET points_per_purchase=?, points_per_vnd=?, redemption_rate=?, min_points_redemption=? WHERE id=1");
        $stmt->bind_param("dddd", $points_per_purchase, $points_per_vnd, $redemption_rate, $min_points_redemption);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function update_user_loyalty_points($user_id, $points)
    {
        global $conn;
        // First, get current points
        $current_points = $this->get_user_points($user_id);
        $difference = $points - $current_points;
        if ($difference != 0) {
            $transaction_type = $difference > 0 ? 'admin_adjustment' : 'admin_deduction';
            return $this->adjust_user_points($user_id, $difference, $transaction_type, 'Admin adjustment');
        }
        return true; // No change needed
    }

    // User Points Management
    public function adjust_user_points($user_id, $points, $transaction_type, $description)
    {
        global $conn;
        $stmt = $conn->prepare("INSERT INTO user_points (user_id, points, type, reference_id, created_at) VALUES (?, ?, ?, NULL, NOW())");
        $stmt->bind_param("iis", $user_id, $points, $transaction_type);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function get_user_points($user_id)
    {
        global $conn;
        $stmt = $conn->prepare("SELECT SUM(points) as total_points FROM user_points WHERE user_id=?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row['total_points'] ?? 0;
    }

    public function get_user_points_history($user_id)
    {
        global $conn;
        $stmt = $conn->prepare("SELECT * FROM user_points WHERE user_id=? ORDER BY created_at DESC");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $history = [];
        while ($row = $result->fetch_assoc()) {
            $history[] = $row;
        }
        $stmt->close();
        return $history;
    }

    public function get_all_user_loyalty_points()
    {
        global $conn;
        $sql = "SELECT u.ID_user, u.tendangnhap, COALESCE(SUM(up.points), 0) as loyalty_points
                FROM users u
                LEFT JOIN user_points up ON u.ID_user = up.user_id
                GROUP BY u.ID_user, u.tendangnhap
                ORDER BY u.ID_user ASC";
        $result = mysqli_query($conn, $sql);
        $users = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $users[] = $row;
        }
        return $users;
    }

    // Report methods
    public function get_total_revenue()
    {
        global $conn;
        $sql = "SELECT SUM(tongtien) as total FROM orders WHERE trangthai = 'đã giao hàng thành công'";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($result);
        return $row['total'] ?? 0;
    }

    public function get_products_sold()
    {
        global $conn;
        $sql = "SELECT SUM(oi.soluong) as total_sold FROM order_items oi JOIN orders o ON oi.order_id = o.id WHERE o.trangthai = 'đã giao hàng thành công'";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($result);
        return $row['total_sold'] ?? 0;
    }

    public function get_order_stats()
    {
        global $conn;
        $stats = [];
        $statuses = ['chờ xác nhận', 'đang vận chuyển', 'đã giao hàng thành công', 'đã hủy'];
        foreach ($statuses as $status) {
            $sql = "SELECT COUNT(*) as count FROM orders WHERE trangthai = '$status'";
            $result = mysqli_query($conn, $sql);
            $row = mysqli_fetch_assoc($result);
            $stats[$status] = $row['count'];
        }
        return $stats;
    }

    // Returns management methods
    public function insert_return($order_id, $user_id, $product_id, $quantity, $reason)
    {
        global $conn;
        $stmt = $conn->prepare("INSERT INTO returns (order_id, user_id, product_id, quantity, reason) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iiisi", $order_id, $user_id, $product_id, $quantity, $reason);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function update_return_status($return_id, $status, $notes = null)
    {
        global $conn;
        if ($status === 'approved') {
            // Restore quantity
            $stmt = $conn->prepare("SELECT r.quantity, r.product_id FROM returns r WHERE r.id = ?");
            $stmt->bind_param("i", $return_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $quantity = $row['quantity'];
                $product_id = $row['product_id'];
                $update_qty = $conn->prepare("UPDATE products SET soluong = soluong + ? WHERE ID_sanpham = ?");
                $update_qty->bind_param("ii", $quantity, $product_id);
                $update_qty->execute();
                $update_qty->close();
            }
            $stmt->close();
        }

        $update_stmt = $conn->prepare("UPDATE returns SET status = ?, notes = ?, processed_date = NOW() WHERE id = ?");
        $update_stmt->bind_param("ssi", $status, $notes, $return_id);
        $result = $update_stmt->execute();
        $update_stmt->close();
        return $result;
    }

    public function get_all_returns()
    {
        global $conn;
        $sql = "SELECT r.*, m.tongtien as order_total, u.tendangnhap, p.tensanpham 
                FROM returns r 
                JOIN orders m ON r.order_id = m.id 
                JOIN users u ON r.user_id = u.ID_user 
                JOIN products p ON r.product_id = p.ID_sanpham 
                ORDER BY r.request_date DESC";
        $result = mysqli_query($conn, $sql);
        $returns = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $returns[] = $row;
        }
        return $returns;
    }

    // Voucher claiming methods
    public function claim_voucher($user_id, $voucher_code)
    {
        global $conn;
        // Check if already claimed
        $check_claim = $conn->prepare("SELECT 1 FROM user_vouchers uv JOIN vouchers v ON uv.voucher_id = v.id WHERE uv.user_id = ? AND v.code = ?");
        $check_claim->bind_param("is", $user_id, $voucher_code);
        $check_claim->execute();
        if ($check_claim->get_result()->num_rows > 0) {
            $check_claim->close();
            return false; // Already claimed
        }
        $check_claim->close();

        // Get voucher
        $get_voucher = $conn->prepare("SELECT * FROM vouchers WHERE code = ? AND (expiry_date IS NULL OR expiry_date >= CURDATE()) AND (max_uses IS NULL OR max_uses = 0 OR uses_count < max_uses)");
        $get_voucher->bind_param("s", $voucher_code);
        $get_voucher->execute();
        $result = $get_voucher->get_result();
        $voucher = $result->fetch_assoc();
        $get_voucher->close();

        if (!$voucher) {
            return false; // Invalid or expired
        }

        // Insert claim
        $insert_claim = $conn->prepare("INSERT INTO user_vouchers (user_id, voucher_id, claimed_at) VALUES (?, ?, NOW())");
        $insert_claim->bind_param("ii", $user_id, $voucher['id']);
        $claim_result = $insert_claim->execute();
        $insert_claim->close();

        if ($claim_result) {
            return true;
        }

        return false;
    }

    public function get_user_claimed_vouchers($user_id)
    {
        global $conn;
        $stmt = $conn->prepare("SELECT v.* FROM vouchers v JOIN user_vouchers uv ON v.id = uv.voucher_id WHERE uv.user_id = ? ORDER BY uv.claimed_at DESC");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $vouchers = [];
        while ($row = $result->fetch_assoc()) {
            $vouchers[] = $row;
        }
        $stmt->close();
        return $vouchers;
    }

    public function get_available_vouchers($user_id)
    {
        global $conn;
        $stmt = $conn->prepare("SELECT * FROM vouchers WHERE (expiry_date IS NULL OR expiry_date >= CURDATE()) AND (max_uses_total = 0 OR max_uses_total IS NULL OR uses_count < max_uses_total) AND id NOT IN (SELECT voucher_id FROM user_vouchers WHERE user_id = ?) ORDER BY created_at DESC");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $vouchers = [];
        while ($row = $result->fetch_assoc()) {
            $vouchers[] = $row;
        }
        $stmt->close();
        return $vouchers;
    }

    // Updated validate for claimed vouchers
    public function validate_claimed_voucher($voucher_id, $user_id, $order_total, $product_id = null)
    {
        global $conn;
        // Check if voucher is claimed by user
        $stmt = $conn->prepare("SELECT v.*, uv.claimed_at FROM vouchers v JOIN user_vouchers uv ON v.id = uv.voucher_id WHERE uv.user_id = ? AND v.id = ?");
        $stmt->bind_param("ii", $user_id, $voucher_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if (!$row = $result->fetch_assoc()) {
            $stmt->close();
            return ['valid' => false, 'message' => 'Bạn chưa claim voucher này hoặc voucher không tồn tại.'];
        }
        $stmt->close();

        // Check expiry
        $current_date = date('Y-m-d');
        $expiry_date = isset($row['expiry_date']) ? $row['expiry_date'] : '';
        if ($expiry_date && $expiry_date !== '0000-00-00' && $expiry_date !== '30/11/-0001') {
            if ($current_date > $expiry_date) {
                return ['valid' => false, 'message' => 'Voucher đã hết hạn.'];
            }
        }
        // Check max uses
        if (!empty($row['max_uses_total']) && $row['max_uses_total'] > 0 && $row['uses_count'] >= $row['max_uses_total']) {
            return ['valid' => false, 'message' => 'Voucher đã hết lượt sử dụng.'];
        }
        // Check min order
        if ($order_total < $row['min_order']) {
            return ['valid' => false, 'message' => 'Đơn hàng chưa đạt giá trị tối thiểu để dùng voucher.'];
        }
        // Check applicable product
        if ($row['applicable_to'] == 'product' && $product_id) {
            $product_ids = json_decode($row['product_ids'], true) ?: [];
            if (!in_array($product_id, $product_ids)) {
                return ['valid' => false, 'message' => 'Voucher không áp dụng cho sản phẩm này.'];
            }
        }
        return ['valid' => true, 'voucher' => $row];
    }
    // Quản lý nhân viên
    // Thêm nhân viên đúng cấu trúc bảng users (có hoten, có sdt)
    public function add_staff($username, $password, $hoten, $sdt, $role = 'nhanvien')
    {
        global $conn;
        // Check duplicate username
        $stmt = $conn->prepare("SELECT ID_user FROM users WHERE tendangnhap=?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $stmt->close();
            return false;
        }
        $stmt->close();
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (tendangnhap, matkhau, hoten, sdt, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $username, $hashed, $hoten, $sdt, $role);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function delete_staff($id)
    {
        global $conn;
        // Không cho xóa admin cuối cùng
        $stmt = $conn->prepare("SELECT COUNT(*) as total FROM users WHERE role='admin'");
        $stmt->execute();
        $total_admin = $stmt->get_result()->fetch_assoc()['total'];
        $stmt->close();
        $stmt = $conn->prepare("SELECT role FROM users WHERE ID_user=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $role = $stmt->get_result()->fetch_assoc()['role'] ?? '';
        $stmt->close();
        if ($role == 'admin' && $total_admin <= 1) return false;
        $stmt = $conn->prepare("DELETE FROM users WHERE ID_user=?");
        $stmt->bind_param("i", $id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function update_staff_role($id, $role)
    {
        global $conn;
        $stmt = $conn->prepare("UPDATE users SET role=? WHERE ID_user=?");
        $stmt->bind_param("si", $role, $id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function get_all_staff()
    {
        global $conn;
        $result = mysqli_query($conn, "SELECT ID_user, tendangnhap, role FROM users ORDER BY ID_user ASC");
        $staffs = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $staffs[] = $row;
        }
        return $staffs;
    }

    // Lấy danh sách đơn hàng (join user, sản phẩm)
    public function select_all_orders()
    {
        global $conn;
        $sql = "SELECT o.id as ID, u.tendangnhap, p.tensanpham, oi.soluong, o.tongtien, o.trangthai
                FROM orders o
                LEFT JOIN users u ON o.id_user = u.ID_user
                LEFT JOIN order_items oi ON o.id = oi.order_id
                LEFT JOIN products p ON oi.ID_sanpham = p.ID_sanpham
                ORDER BY o.id DESC";
        $result = mysqli_query($conn, $sql);
        $orders = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $orders[] = $row;
        }
        return $orders;
    }

    // Cập nhật trạng thái đơn hàng
    public function update_order_status($order_id, $status)
    {
        global $conn;
        $stmt = $conn->prepare("UPDATE orders SET trangthai=? WHERE id=?");
        if ($stmt === false) {
            return false;
        }
        $stmt->bind_param("si", $status, $order_id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
}
?>
