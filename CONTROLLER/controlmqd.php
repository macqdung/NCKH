<?php
include('../MODEL/modeelmqd.php');
$get_data = new data_mqd();

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_term = trim($_GET['search']);
    $search_results = $get_data->search_products($search_term);
    $is_search = true;
} else {
    // Hiển thị trang chủ
    $featured_products = $get_data->select_featured_products();
    $all_products = $get_data->select_all_products();
    $is_search = false;
}
?>
