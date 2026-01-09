<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include_once('../MODEL/modeladmin.php'); // Model cho các chức năng admin
include_once('../MODEL/modelmh.php'); // Model cho mua hàng
include_once('../MODEL/modelmqd1.php'); // Model chứa hàm lấy sản phẩm

$get_data = new data_admin();
$order_data = new data_muahang();

if (!isset($_SESSION['admin'])) {
    header("Location: ../VIEW/dangnhap.php");
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_product'])) {
    $tensanpham = mysqli_real_escape_string($conn, $_POST['tensanpham']);
    $mota = mysqli_real_escape_string($conn, $_POST['mota']);
    $soluong = intval($_POST['soluong']);
    $dongia = floatval($_POST['dongia']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);

    $hinhanh = '';
    if (isset($_FILES['hinhanh']) && $_FILES['hinhanh']['error'] == 0) {
        $target_dir = "../media/";
        $target_file = $target_dir . basename($_FILES["hinhanh"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES["hinhanh"]["tmp_name"]);
        if ($check !== false) {
            if (move_uploaded_file($_FILES["hinhanh"]["tmp_name"], $target_file)) {
                $hinhanh = basename($_FILES["hinhanh"]["name"]);
            } else {
                $message = "Sorry, there was an error uploading your file.";
            }
        } else {
            $message = "File is not an image.";
        }
    }

    if ($hinhanh && $message == '') {
        $insert = $get_data->insert_product($tensanpham, $mota, $hinhanh, $soluong, $dongia, $category);
        if ($insert) {
            header("Location: admin.php?message=" . urlencode('Sản phẩm đã được thêm thành công!'));
            exit;
        } else {
            $message = 'Thêm sản phẩm thất bại!';
        }
    }
}

if (isset($_GET['delete']) && !empty($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $delete = $get_data->delete_product($id);
    if ($delete) {
        header("Location: admin.php?message=" . urlencode('Sản phẩm đã được xóa thành công!'));
        exit;
    } else {
        $message = 'Xóa sản phẩm thất bại!';
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_price'])) {
    $id = intval($_POST['product_id']);
    $dongia = floatval($_POST['dongia']);
    $update = $get_data->update_price($id, $dongia);
    if ($update) {
        header("Location: admin.php?message=" . urlencode('Giá sản phẩm đã được cập nhật thành công!'));
        exit;
    } else {
        $message = 'Cập nhật giá thất bại!';
    }
}

// Cập nhật số lượng sản phẩm
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_quantity'])) {
    $id = intval($_POST['product_id']);
    $soluong = intval($_POST['soluong']);
    $update = $get_data->update_quantity($id, $soluong);
    if ($update) {
        header("Location: admin.php?message=" . urlencode('Số lượng sản phẩm đã được cập nhật thành công!'));
        exit;
    } else {
        $message = 'Cập nhật số lượng thất bại!';
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $id = intval($_POST['id_muahang']);
    $new_status = mysqli_real_escape_string($conn, $_POST['update_status']);

    // Lấy trạng thái hiện tại của đơn hàng
    $order = $order_data->select_order_by_id($id);
    if ($order && isset($order['trangthai']) && $order['trangthai'] === 'đã hủy') {
        $message = 'Không thể cập nhật trạng thái cho đơn hàng đã hủy!';
    } else {
        $update = $order_data->update_order_status($id, $new_status);
        if ($update) {
            header("Location: admin.php?message=" . urlencode('Trạng thái đơn hàng đã được cập nhật thành công!'));
            exit;
        } else {
            $message = 'Cập nhật trạng thái thất bại!';
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_voucher'])) {
    $code = mysqli_real_escape_string($conn, $_POST['code']);
    $type = $_POST['type'];
    $value = floatval($_POST['value']);
    $min_order = floatval($_POST['min_order']);
    $max_uses = intval($_POST['max_uses']);
    $expiry_date = $_POST['expiry_date'] ?: null;
    $applicable_to = $_POST['applicable_to'];
    $product_ids = isset($_POST['product_ids']) ? json_encode(array_map('intval', $_POST['product_ids'])) : null;

    $insert = $get_data->insert_voucher($code, $type, $value, $min_order, $max_uses, $expiry_date, $applicable_to, $product_ids);
    if ($insert) {
        header("Location: admin.php?message=" . urlencode('Voucher đã được thêm thành công!'));
        exit;
    } else {
        $message = 'Thêm voucher thất bại!';
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_voucher'])) {
    $id = intval($_POST['voucher_id']);
    $code = mysqli_real_escape_string($conn, $_POST['code']);
    $type = $_POST['type'];
    $value = floatval($_POST['value']);
    $min_order = floatval($_POST['min_order']);
    $max_uses = intval($_POST['max_uses']);
    $expiry_date = $_POST['expiry_date'] ?: null;
    $applicable_to = $_POST['applicable_to'];
    $product_ids = isset($_POST['product_ids']) ? json_encode(array_map('intval', $_POST['product_ids'])) : null;

    $update = $get_data->update_voucher($id, $code, $type, $value, $min_order, $max_uses, $expiry_date, $applicable_to, $product_ids);
    if ($update) {
        header("Location: admin.php?message=" . urlencode('Voucher đã được cập nhật thành công!'));
        exit;
    } else {
        $message = 'Cập nhật voucher thất bại!';
    }
}

if (isset($_GET['delete_voucher']) && !empty($_GET['delete_voucher'])) {
    $id = intval($_GET['delete_voucher']);
    $delete = $get_data->delete_voucher($id);
    if ($delete) {
        header("Location: admin.php?message=" . urlencode('Voucher đã được xóa thành công!'));
        exit;
    } else {
        $message = 'Xóa voucher thất bại!';
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_promotion'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $discount_type = $_POST['discount_type'];
    $discount_value = floatval($_POST['discount_value']);
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $applicable_products = isset($_POST['applicable_products']) ? json_encode(array_map('intval', $_POST['applicable_products'])) : 'all';
    $status = $_POST['status'];

    $insert = $get_data->insert_promotion($name, $description, $discount_type, $discount_value, $start_date, $end_date, $applicable_products, $status);
    if ($insert) {
        header("Location: admin.php?message=" . urlencode('Promotion đã được thêm thành công!'));
        exit;
    } else {
        $message = 'Thêm promotion thất bại!';
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_promotion'])) {
    $id = intval($_POST['promotion_id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $discount_type = $_POST['discount_type'];
    $discount_value = floatval($_POST['discount_value']);
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $applicable_products = isset($_POST['applicable_products']) ? json_encode(array_map('intval', $_POST['applicable_products'])) : 'all';
    $status = $_POST['status'];

    $update = $get_data->update_promotion($id, $name, $description, $discount_type, $discount_value, $start_date, $end_date, $applicable_products, $status);
    if ($update) {
        header("Location: admin.php?message=" . urlencode('Promotion đã được cập nhật thành công!'));
        exit;
    } else {
        $message = 'Cập nhật promotion thất bại!';
    }
}

if (isset($_GET['delete_promotion']) && !empty($_GET['delete_promotion'])) {
    $id = intval($_GET['delete_promotion']);
    $delete = $get_data->delete_promotion($id);
    if ($delete) {
        header("Location: admin.php?message=" . urlencode('Promotion đã được xóa thành công!'));
        exit;
    } else {
        $message = 'Xóa promotion thất bại!';
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_loyalty_rule'])) {
    $points_per_vnd = floatval($_POST['points_per_vnd']);
    $min_order_for_points = floatval($_POST['min_order_for_points']);
    $redemption_rate = floatval($_POST['redemption_rate']);

    $insert = $get_data->insert_loyalty_rule($points_per_vnd, $min_order_for_points, $redemption_rate);
    if ($insert) {
        header("Location: admin.php?message=" . urlencode('Loyalty rule đã được thêm thành công!'));
        exit;
    } else {
        $message = 'Thêm loyalty rule thất bại!';
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_loyalty_rule'])) {
    $id = intval($_POST['rule_id']);
    $points_per_vnd = floatval($_POST['points_per_vnd']);
    $min_order_for_points = floatval($_POST['min_order_for_points']);
    $redemption_rate = floatval($_POST['redemption_rate']);

    $update = $get_data->update_loyalty_rule($id, $points_per_vnd, $min_order_for_points, $redemption_rate);
    if ($update) {
        header("Location: admin.php?message=" . urlencode('Loyalty rule đã được cập nhật thành công!'));
        exit;
    } else {
        $message = 'Cập nhật loyalty rule thất bại!';
    }
}

if (isset($_GET['delete_loyalty_rule']) && !empty($_GET['delete_loyalty_rule'])) {
    $id = intval($_GET['delete_loyalty_rule']);
    $delete = $get_data->delete_loyalty_rule($id);
    if ($delete) {
        header("Location: admin.php?message=" . urlencode('Loyalty rule đã được xóa thành công!'));
        exit;
    } else {
        $message = 'Xóa loyalty rule thất bại!';
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['adjust_points'])) {
    $user_id = intval($_POST['user_id']);
    $points = intval($_POST['points']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    $adjust = $get_data->adjust_user_points($user_id, $points, 'adjust', $description);
    if ($adjust) {
        header("Location: admin.php?message=" . urlencode('Điểm đã được điều chỉnh thành công!'));
        exit;
    } else {
        $message = 'Điều chỉnh điểm thất bại!';
    }
}

// Handle returns
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_return_status'])) {
    $return_id = intval($_POST['return_id']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $notes = isset($_POST['notes']) ? mysqli_real_escape_string($conn, $_POST['notes']) : null;

    $update = $get_data->update_return_status($return_id, $status, $notes);
    if ($update) {
        $msg = $status == 'approved' ? 'Yêu cầu đổi trả đã được phê duyệt!' : 'Yêu cầu đổi trả đã bị từ chối!';
        header("Location: admin.php?message=" . urlencode($msg));
        exit;
    } else {
        $message = 'Cập nhật thất bại!';
    }
}

// Khởi tạo model sản phẩm và lấy danh sách sản phẩm
$product_data = new data_mqd1();
$products = $product_data->select_all_sanpham();
$orders = $order_data->select_all_orders();
$vouchers = $get_data->get_all_vouchers();
$promotions = $get_data->get_all_promotions();
$loyalty_rules = $get_data->get_loyalty_rules();

// Handle edit voucher
$edit_voucher = null;
if (isset($_GET['edit_voucher']) && !empty($_GET['edit_voucher'])) {
    $edit_voucher = $get_data->get_voucher_by_id(intval($_GET['edit_voucher']));
}

// Reports data
$total_revenue = $get_data->get_total_revenue();
$total_products_sold = $get_data->get_products_sold();
$order_stats = $get_data->get_order_stats();

// Returns data
$returns = $get_data->get_all_returns();
?>
