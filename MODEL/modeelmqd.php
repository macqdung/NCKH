<?php
include('connect.php');
class data_mqd
{
    public function select_featured_products()
    {
        global $conn;
        $sql = "SELECT * FROM products LIMIT 4"; 
        $run = mysqli_query($conn, $sql);
        $data = [];
        while ($row = mysqli_fetch_assoc($run)) {
            $data[] = $row;
        }
        return $data;
    }

    public function select_all_products()
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

    public function search_products($term)
    {
        global $conn;
        $term = mysqli_real_escape_string($conn, $term);
        $sql = "SELECT * FROM products WHERE tensanpham LIKE '%$term%' LIMIT 10";
        $run = mysqli_query($conn, $sql);
        $data = [];
        while ($row = mysqli_fetch_assoc($run)) {
            $data[] = $row;
        }
        return $data;
    }
}
?>
