<?php
include('connect.php');
class data_vouchers
{
    // Lấy danh sách tất cả voucher hợp lệ (chưa hết hạn, chưa hết lượt sử dụng)
    public function select_all_vouchers()
    {
        global $conn;
$sql = "SELECT * FROM vouchers WHERE (expiry_date IS NULL OR expiry_date = '0000-00-00' OR expiry_date >= CURDATE()) AND (max_uses_total = 0 OR max_uses_total IS NULL OR uses_count < max_uses_total) ORDER BY created_at DESC";
        $run = mysqli_query($conn, $sql);
        $data = [];
        while ($row = mysqli_fetch_assoc($run)) {
            $data[] = $row;
        }
        return $data;
    }

    // Lấy danh sách voucher đã nhận của user
    public function get_user_claimed_vouchers($user_id)
    {
        global $conn;
$stmt = $conn->prepare("SELECT v.*, uv.claimed_at FROM vouchers v JOIN user_vouchers uv ON v.id = uv.voucher_id WHERE uv.user_id = ? ORDER BY uv.claimed_at DESC");
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

    // Nhận voucher
    public function claim_voucher($user_id, $voucher_code)
    {
        global $conn;
        // Kiểm tra đã nhận chưa
        $check_claim = $conn->prepare("SELECT 1 FROM user_vouchers uv JOIN vouchers v ON uv.voucher_id = v.id WHERE uv.user_id = ? AND v.code = ?");
        $check_claim->bind_param("is", $user_id, $voucher_code);
        $check_claim->execute();
        if ($check_claim->get_result()->num_rows > 0) {
            $check_claim->close();
            return false; // Đã nhận
        }
        $check_claim->close();

        // Lấy voucher
$get_voucher = $conn->prepare("SELECT * FROM vouchers WHERE code = ? AND (expiry_date IS NULL OR expiry_date = '0000-00-00' OR expiry_date >= CURDATE()) AND (max_uses_total IS NULL OR max_uses_total = 0 OR uses_count < max_uses_total)");
$get_voucher->bind_param("s", $voucher_code);
$get_voucher->execute();
$result = $get_voucher->get_result();
$voucher = $result->fetch_assoc();
$get_voucher->close();

        if (!$voucher) {
            return false; // Không hợp lệ
        }

        // Thêm nhận
        $insert_claim = $conn->prepare("INSERT INTO user_vouchers (user_id, voucher_id, claimed_at) VALUES (?, ?, NOW())");
        $insert_claim->bind_param("ii", $user_id, $voucher['id']);
        $claim_result = $insert_claim->execute();
        $insert_claim->close();

        if ($claim_result) {
            // Tăng uses_count nếu có giới hạn
            if (isset($voucher['max_uses']) && $voucher['max_uses'] > 0) {
                $update_uses = $conn->prepare("UPDATE vouchers SET uses_count = uses_count + 1 WHERE id = ?");
                $update_uses->bind_param("i", $voucher['id']);
                $update_uses->execute();
                $update_uses->close();
            }
            return true;
        }

        return false;
    }
}
?>
