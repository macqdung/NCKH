<?php
include('connect.php');
class data_mqd1
{
    // Lấy danh sách tất cả sản phẩm
    public function select_all_sanpham()
    {
        global $conn;
        $sql = "SELECT * FROM products";
        $run = mysqli_query($conn, $sql);
        $data = [];
        while ($row = mysqli_fetch_assoc($run)) {
            $data[] = $row;
        }
        return $data;
    }

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

    // Lấy thông tin sản phẩm theo ID (trả về 1 sản phẩm)
    public function getProductById($id_sanpham)
    {
        global $conn;
        $sql = "SELECT * FROM products WHERE ID_sanpham = $id_sanpham LIMIT 1";
        $run = mysqli_query($conn, $sql);
        if ($run) {
            return mysqli_fetch_assoc($run);
        }
        return null;
    }

    // Thêm đơn mua hàng mới và cập nhật số lượng tồn kho
    public function insert_muahang($id_user, $id_sanpham, $solanmua, $soluong, $dongia, $tongtien, $trangthai)
    {
        global $conn;

        // Bắt đầu transaction
        mysqli_begin_transaction($conn);

        try {
            // Kiểm tra số lượng tồn kho hiện tại
            $sql_check = "SELECT soluong FROM products WHERE ID_sanpham = $id_sanpham FOR UPDATE";
            $result = mysqli_query($conn, $sql_check);
            if (!$result || mysqli_num_rows($result) == 0) {
                throw new Exception("Sản phẩm không tồn tại");
            }
            $row = mysqli_fetch_assoc($result);
            $soluong_ton = intval($row['soluong']);

            if ($soluong > $soluong_ton) {
                throw new Exception("Số lượng mua vượt quá tồn kho");
            }

            // Thêm đơn mua hàng mới
            $sql_insert = "INSERT INTO muahangg(ID_user, ID_sanpham, solanmua, soluong, dongia, tongtien, trangthai)
                VALUES ('$id_user', '$id_sanpham', '$solanmua', '$soluong', '$dongia', '$tongtien', '$trangthai')";
            $insert_result = mysqli_query($conn, $sql_insert);
            if (!$insert_result) {
                throw new Exception("Lỗi khi thêm đơn mua hàng");
            }

            // Cập nhật số lượng tồn kho
            $new_soluong = $soluong_ton - $soluong;
            $sql_update = "UPDATE products SET soluong = $new_soluong WHERE ID_sanpham = $id_sanpham";
            $update_result = mysqli_query($conn, $sql_update);
            if (!$update_result) {
                throw new Exception("Lỗi khi cập nhật số lượng tồn kho");
            }

            // Commit transaction
            mysqli_commit($conn);
            return true;
        } catch (Exception $e) {
            // Rollback transaction nếu có lỗi
            mysqli_rollback($conn);
            return false;
        }
    }
}
?>
