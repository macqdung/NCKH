<?php
include('connect.php');
class data_muahang
{
    // Lấy thông tin sản phẩm theo ID
    public function select_sanpham_id($id_sanpham)
    {
        global $conn;
        $sql = "SELECT * FROM products WHERE ID_sanpham = $id_sanpham";
        $run = mysqli_query($conn, $sql);
        $data = [];
        while ($row = mysqli_fetch_assoc($run)) {
            $data[] = $row;
        }
        return $data;
    }

    // Thêm đơn mua hàng mới và cập nhật số lượng sản phẩm
    public function insert_muahang($id_user, $id_sanpham, $solanmua, $soluong, $dongia, $tongtien, $trangthai, $voucher_code = null, $voucher_id = null)
    {
        global $conn;
        include_once('modeladmin.php');
        $admin_model = new data_admin();

        // Kiểm tra số lượng còn lại
        $sql_check = "SELECT soluong FROM products WHERE ID_sanpham = $id_sanpham";
        $run_check = mysqli_query($conn, $sql_check);
        if ($run_check && mysqli_num_rows($run_check) > 0) {
            $row = mysqli_fetch_assoc($run_check);
            $soluong_con = $row['soluong'];
            if ($soluong > $soluong_con) {
                return ['success' => false, 'message' => 'Số lượng yêu cầu vượt quá tồn kho (còn ' . $soluong_con . ')'];
            }
        } else {
            return ['success' => false, 'message' => 'Sản phẩm không tồn tại'];
        }

        $original_tongtien = $dongia * $soluong;
        $discount = 0;
        $discounted_dongia = $dongia;
        $voucher = null;

        if ($voucher_id) {
            $validation = $admin_model->validate_claimed_voucher($voucher_id, $id_user, $original_tongtien, $id_sanpham);
            if ($validation['valid']) {
                $voucher = $validation['voucher'];
                if ($voucher['applicable_to'] == 'product') {
                    // Product voucher: discount on unit price
                    if ($voucher['type'] == 'percent') {
                        $unit_discount = $dongia * ($voucher['value'] / 100);
                    } else {
                        $unit_discount = $voucher['value'];
                    }
                    $discounted_dongia = max(0, $dongia - $unit_discount);
                    $discount = $unit_discount * $soluong;
                    $tongtien = $discounted_dongia * $soluong;
                } else {
                    // Order voucher: discount on total
                    if ($voucher['type'] == 'percent') {
                        $discount = $original_tongtien * ($voucher['value'] / 100);
                    } else {
                        $discount = min($voucher['value'], $original_tongtien);
                    }
                    $tongtien = $original_tongtien - $discount;
                }
            } else {
                return ['success' => false, 'message' => $validation['message']];
            }
        }

        // Insert order
        $stmt = $conn->prepare("INSERT INTO muahangg (ID_user, ID_sanpham, solanmua, soluong, dongia, tongtien, trangthai, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("iiidids", $id_user, $id_sanpham, $solanmua, $soluong, $discounted_dongia, $tongtien, $trangthai);
        $insert_result = $stmt->execute();
        $order_id = $conn->insert_id;
        $stmt->close();

        if ($insert_result) {
            // Update stock
            $update_stock = $conn->prepare("UPDATE products SET soluong = soluong - ? WHERE ID_sanpham = ?");
            $update_stock->bind_param("ii", $soluong, $id_sanpham);
            $update_stock->execute();
            $update_stock->close();

            // If voucher, record usage
            if ($voucher_id && $voucher) {
                $update_uses = $conn->prepare("UPDATE vouchers SET uses_count = uses_count + 1 WHERE id = ?");
                $update_uses->bind_param("i", $voucher_id);
                $update_uses->execute();
                $update_uses->close();

                $insert_usage = $conn->prepare("INSERT INTO voucher_usage (voucher_id, user_id, order_id, discount_amount, used_at) VALUES (?, ?, ?, ?, NOW())");
                $insert_usage->bind_param("iiid", $voucher_id, $id_user, $order_id, $discount);
                $insert_usage->execute();
                $insert_usage->close();
            }

            return ['success' => true, 'order_id' => $order_id, 'discount' => $discount];
        }

        return ['success' => false, 'message' => 'Lỗi khi đặt hàng'];
    }

    // Hủy đơn hàng: cập nhật trạng thái và khôi phục số lượng sản phẩm
    public function cancel_order($id)
    {
        global $conn;
        if (!is_numeric($id)) {
            return false;
        }

        // Lấy thông tin đơn hàng để khôi phục số lượng
        $stmt = $conn->prepare("SELECT ID_sanpham, soluong FROM muahangg WHERE ID = ? AND trangthai = 'chờ xác nhận'");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $id_sanpham = $row['ID_sanpham'];
            $soluong = $row['soluong'];

            // Cập nhật trạng thái đơn hàng thành 'đã hủy'
            $update_stmt = $conn->prepare("UPDATE muahangg SET trangthai = 'đã hủy' WHERE ID = ?");
            $update_stmt->bind_param("i", $id);
            $update_success = $update_stmt->execute();
            $update_stmt->close();

            if ($update_success) {
                // Khôi phục số lượng sản phẩm
                $restore_sql = "UPDATE products SET soluong = soluong + $soluong WHERE ID_sanpham = $id_sanpham";
                $restore_run = mysqli_query($conn, $restore_sql);
                $stmt->close();
                return $restore_run;
            }
        }
        $stmt->close();
        return false;
    }

    // Lấy lịch sử mua hàng theo ID_user
    public function select_muahang_by_user($id_user)
    {
        global $conn;
        if (!is_numeric($id_user)) {
            return [];
        }
        $stmt = $conn->prepare("SELECT m.*, m.ID as id, p.tensanpham, p.hinhanh FROM muahangg m JOIN products p ON m.ID_sanpham = p.ID_sanpham WHERE m.ID_user = ? ORDER BY m.ID DESC");
        $stmt->bind_param("i", $id_user);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        $stmt->close();
        return $data;
    }

    // Lấy tất cả đơn hàng cho admin
    public function select_all_orders()
    {
        global $conn;
        $sql = "SELECT m.*, u.tendangnhap, p.tensanpham FROM muahangg m JOIN users u ON m.ID_user = u.ID_user JOIN products p ON m.ID_sanpham = p.ID_sanpham ORDER BY m.ID DESC";
        $run = mysqli_query($conn, $sql);
        $data = [];
        while ($row = mysqli_fetch_assoc($run)) {
            $data[] = $row;
        }
        return $data;
    }

    // Cập nhật trạng thái đơn hàng
    public function update_order_status($id, $new_status)
    {
        global $conn;
        include_once('modeladmin.php');
        $admin_model = new data_admin();

        if (!is_numeric($id)) {
            return false;
        }
        if ($new_status === 'đã giao hàng thành công') {
            // Cập nhật trạng thái và thời gian giao hàng thành công
            $stmt = $conn->prepare("UPDATE muahangg SET trangthai = ?, delivered_at = NOW() WHERE ID = ?");
            $stmt->bind_param("si", $new_status, $id);
        } else {
            // Chỉ cập nhật trạng thái
            $stmt = $conn->prepare("UPDATE muahangg SET trangthai = ? WHERE ID = ?");
            $stmt->bind_param("si", $new_status, $id);
        }
        $result = $stmt->execute();
        $stmt->close();

        // Nếu giao hàng thành công, thưởng điểm tích lũy
        if ($result && $new_status === 'đã giao hàng thành công') {
            $order = $this->select_order_by_id($id);
            if ($order) {
                $user_id = $order['ID_user'];
                $tongtien = $order['tongtien'];
                $rules = $admin_model->get_loyalty_rules();
                if (!empty($rules)) {
                    $rule = $rules[0]; // Assume first rule is active
                    if ($tongtien >= $rule['min_order_for_points']) {
                        $points = floor($tongtien * $rule['points_per_vnd']);
                        if ($points > 0) {
                            $admin_model->adjust_user_points($user_id, $points, 'earn', "Points earned from order #$id");
                        }
                    }
                }
            }
        }

        return $result;
    }

    // Lấy chi tiết đơn hàng theo ID (bao gồm thông tin sản phẩm)
    public function select_order_by_id($order_id)
    {
        global $conn;
        if (!is_numeric($order_id)) {
            return null;
        }
        $stmt = $conn->prepare("SELECT m.*, m.ID as id, p.tensanpham, p.hinhanh FROM muahangg m JOIN products p ON m.ID_sanpham = p.ID_sanpham WHERE m.ID = ?");
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $order = $result->fetch_assoc();
        $stmt->close();
        return $order;
    }
}
?>
