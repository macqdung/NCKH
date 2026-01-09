<?php
include('connect.php');
class data_category
{
    public function select_all_categories()
    {
        global $conn;
        $sql = "SELECT * FROM categories ORDER BY name ASC";
        $run = mysqli_query($conn, $sql);
        $data = [];
        while ($row = mysqli_fetch_assoc($run)) {
            $data[] = $row;
        }
        return $data;
    }

    public function select_products_by_category($category_id)
    {
        global $conn;
        $category_id = intval($category_id);
        // Modify query to fetch products where the category or subcategory matches the category_id
        $sql = "SELECT * FROM products WHERE category = $category_id OR subcategory_id = $category_id LIMIT 10";
        $run = mysqli_query($conn, $sql);
        $data = [];
        while ($row = mysqli_fetch_assoc($run)) {
            $data[] = $row;
        }
        return $data;
    }
}
?>
