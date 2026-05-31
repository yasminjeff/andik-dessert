<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
require __DIR__ . "/../db.php";

try {
    if (!isset($_FILES['image'])) {
        echo json_encode(["error" => true, "message" => "No image uploaded"]);
        exit;
    }

    $file = $_FILES['image'];
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'chat_' . uniqid() . '.' . $ext;
    $uploadDir = __DIR__ . '/../uploads/chat/';
    
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
    
    move_uploaded_file($file['tmp_name'], $uploadDir . $filename);
    
    $url = '/uploads/chat/' . $filename;
    echo json_encode(["success" => true, "url" => $url]);
} catch (Exception $e) {
    echo json_encode(["error" => true, "message" => $e->getMessage()]);
}
?>