<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, X-Requested-With");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require __DIR__ . "/../db.php";

try {
    $input = json_decode(file_get_contents("php://input"), true);
    $sender_id = $input['sender_id'] ?? null;
    $receiver_id = $input['receiver_id'] ?? null;
    $message = trim($input['message'] ?? '');

    if (!$sender_id || !$receiver_id || !$message) {
        echo json_encode(["error" => true, "message" => "Missing fields"]);
        exit;
    }

    $db = DB::connect();
    $stmt = $db->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
    $stmt->execute([$sender_id, $receiver_id, $message]);

    echo json_encode(["success" => true, "id" => $db->lastInsertId()]);
} catch (Exception $e) {
    echo json_encode(["error" => true, "message" => $e->getMessage()]);
}
?>