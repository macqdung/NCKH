<?php
include('connect.php');
class data_mqd2
{
    // Lấy danh sách các loại bánh (categories)
    public function select_all_categories()
    {
        global $conn;
        $sql = "SELECT * FROM categories"; // Giả sử có bảng categories
        $run = mysqli_query($conn, $sql);
        $data = [];
        while ($row = mysqli_fetch_assoc($run)) {
            $data[] = $row;
        }
        return $data;
    }
}
?>
