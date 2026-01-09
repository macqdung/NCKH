<?php
session_start();
include_once '../MODEL/modeldanhgia.php';

$reviewModel = new data_danhgia();
$errors = [];
$success = '';
$username = isset($_SESSION['user']) ? $_SESSION['user'] : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user'])) {
        $errors[] = "Bạn cần đăng nhập để đánh giá.";
    } else {
        $username = $_SESSION['user'];
        $comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';
        $rating = isset($_POST['rating']) ? intval($_POST['rating']) : 0;

        if (empty($comment)) {
            $errors[] = "Nội dung đánh giá không được để trống.";
        }

        if ($rating < 1 || $rating > 5) {
            $errors[] = "Đánh giá sao phải từ 1 đến 5.";
        }

        if (empty($errors)) {
            $added = $reviewModel->addReview($username, $comment, $rating);
            if ($added) {
                $success = "Đánh giá đã được gửi thành công.";
                $comment = ''; // Clear form
            } else {
                $errors[] = "Có lỗi xảy ra khi gửi đánh giá.";
                $comment = $_POST['comment']; // Preserve on error
            }
        } else {
            $comment = $_POST['comment']; // Preserve on error
        }
    }
}

$reviews = $reviewModel->getReviews();

include_once '../VIEW/danhgia.php';
?>
