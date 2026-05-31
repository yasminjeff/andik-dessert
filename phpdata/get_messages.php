<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, X-Requested-With");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require __DIR__ . "/../db.php";

try {
    $user_id = $_GET['user_id'] ?? null;
    $other_id = $_GET['other_id'] ?? null;

    if (!$user_id || !$other_id) {
        echo json_encode(["error" => true, "message" => "Missing fields"]);
        exit;
    }

    $db = DB::connect();

    $stmt = $db->prepare("UPDATE messages SET is_read = 1 WHERE sender_id = ? AND receiver_id = ? AND is_read = 0");
    $stmt->execute([$other_id, $user_id]);

    $stmt = $db->prepare("
        SELECT m.*, u.name as sender_name 
        FROM messages m
        JOIN users u ON u.id = m.sender_id
        WHERE (m.sender_id = ? AND m.receiver_id = ?)
           OR (m.sender_id = ? AND m.receiver_id = ?)
        ORDER BY m.created_at ASC
        LIMIT 100
    ");
    $stmt->execute([$user_id, $other_id, $other_id, $user_id]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($messages);
} catch (Exception $e) {
    echo json_encode(["error" => true, "message" => $e->getMessage()]);
}
?>