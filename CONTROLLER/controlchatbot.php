<?php
// controlchatbot.php - PHIÊN BẢN HOÀN HẢO 2025
header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['reply' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$userMessage = trim($input['message'] ?? '');

if ($userMessage === '') {
    echo json_encode(['reply' => 'Bạn chưa nhập gì cả!']);
    exit;
}

// Gọi Python server
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8000/chatbot');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['message' => $userMessage], JSON_UNESCAPED_UNICODE));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json; charset=utf-8']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$err = curl_error($ch);
curl_close($ch);

if ($err) {
    echo json_encode(['reply' => 'Bot đang ngủ quên rồi, bạn đợi chút nha!']);
    exit;
}

$data = json_decode($response, true);
$reply = $data['reply'] ?? 'Mình đang hơi lag, bạn nói lại được không?';

// Fix lỗi font 100%
$reply = mb_convert_encoding($reply, 'UTF-8', 'UTF-8');

echo json_encode(['reply' => $reply], JSON_UNESCAPED_UNICODE);
?>