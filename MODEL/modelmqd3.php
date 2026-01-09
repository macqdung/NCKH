<?php
include('connect.php');
class data_mqd3
{
    // Lấy danh sách bánh kem từ database
    public function select_all_banhkem()
    {
        global $conn;
        $sql = "SELECT * FROM products WHERE category = 'banhkem'";
        $run = mysqli_query($conn, $sql);
        $data = [];
        while ($row = mysqli_fetch_assoc($run)) {
            $data[] = $row;
        }
        return $data;
    }

    // Lấy danh sách bánh bông lan từ database
    public function select_all_banhbonglan()
    {
        global $conn;
        $sql = "SELECT * FROM products WHERE category = 'banhbonglan'";
        $run = mysqli_query($conn, $sql);
        $data = [];
        while ($row = mysqli_fetch_assoc($run)) {
            $data[] = $row;
        }
        return $data;
    }
}
?>
