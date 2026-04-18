<?php
include('connect.php');
class data_category
{
    public function select_all_categories()
    {
        global $conn;
        // Group by name to ensure even if DB has duplicates, UI only shows one of each category
        $sql = "SELECT MIN(id) as id, name FROM categories GROUP BY name ORDER BY name ASC";
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
        $category_id = mysqli_real_escape_string($conn, $category_id);
        
        // Find the category name to match products using names instead of IDs
        $name_query = "SELECT name FROM categories WHERE id = '$category_id'";
        $name_res = mysqli_query($conn, $name_query);
        $category_name = "";
        if ($cat = mysqli_fetch_assoc($name_res)) {
            $category_name = mysqli_real_escape_string($conn, $cat['name']);
        }

        // Fetch products matching by ID or the name string
        $sql = "SELECT * FROM products 
                WHERE (category = '$category_id' OR category = '$category_name') 
                GROUP BY tensanpham 
                LIMIT 20";
        
        $run = mysqli_query($conn, $sql);
        $data = [];
        while ($row = mysqli_fetch_assoc($run)) {
            $data[] = $row;
        }
        return $data;
    }
}
?>
