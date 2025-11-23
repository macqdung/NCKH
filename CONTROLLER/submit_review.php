<?php
session_start();
include('../MODEL/modelreview.php');
include('../CONTROLLER/controlreview.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user'])) {
    $product_id = intval($_POST['product_id']);
    $rating = intval($_POST['rating']);
    $comment = trim($_POST['comment']);
    $user = $_SESSION['user'];

    // Get user ID
    include('../MODEL/modeldangnhap.php');
    $userModel = new data_user_login();
    $userData = $userModel->get_user_by_username($user);
    if (!$userData) {
        header('Location: ../VIEW/muahang.php?mua=' . $product_id . '&error=ユーザー認証エラー');
        exit();
    }
    $user_id = $userData['ID_user'];

    // Handle image uploads
    $uploadedImages = [];
    if (!empty($_FILES['images']['name'][0])) {
        $uploadDir = '../media/reviews/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
            $fileName = $_FILES['images']['name'][$key];
            $fileTmp = $_FILES['images']['tmp_name'][$key];
            $fileType = $_FILES['images']['type'][$key];
            $fileSize = $_FILES['images']['size'][$key];

            // Validate file type and size
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (in_array($fileType, $allowedTypes) && $fileSize <= 5 * 1024 * 1024) { // 5MB limit
                $newFileName = uniqid() . '_' . basename($fileName);
                $targetPath = $uploadDir . $newFileName;
                if (move_uploaded_file($fileTmp, $targetPath)) {
                    $uploadedImages[] = $newFileName;
                }
            }
        }
    }

    // Submit review
    $reviewController = new control_review();
    $result = $reviewController->submit_review($product_id, $user_id, $rating, $comment, $uploadedImages);

    if ($result['success']) {
        header('Location: ../VIEW/muahang.php?mua=' . $product_id . '&message=' . urlencode($result['message']));
    } else {
        header('Location: ../VIEW/muahang.php?mua=' . $product_id . '&error=' . urlencode($result['message']));
    }
    exit();
} else {
    header('Location: ../VIEW/dangnhap.php');
    exit();
}
?>
