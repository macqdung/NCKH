<?php
include('../MODEL/modelmqd2.php');
$get_data = new data_mqd2();

// Lấy danh sách categories nếu cần
$categories = $get_data->select_all_categories();
?>
