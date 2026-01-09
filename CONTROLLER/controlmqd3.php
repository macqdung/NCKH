<?php
include('../MODEL/modelmqd3.php');
$get_data = new data_mqd3();

// Lấy danh sách bánh kem và bánh bông lan
$banhkem = $get_data->select_all_banhkem();
$banhbonglan = $get_data->select_all_banhbonglan();
?>
