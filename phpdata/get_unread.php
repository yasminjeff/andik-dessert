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

    if (!$user_id) {
        echo json_encode(["error" => true, "message" => "Missing user_id"]);
        exit;
    }

    $db = DB::connect();

    // Get unread count per sender
    $stmt = $db->prepare("
        SELECT sender_id, COUNT(*) as unread_count, u.name as sender_name
        FROM messages m
        JOIN users u ON u.id = m.sender_id
        WHERE m.receiver_id = ? AND m.is_read = 0
        GROUP BY m.sender_id
    ");
    $stmt->execute([$user_id]);
    $unread = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $total = array_sum(array_column($unread, 'unread_count'));

    echo json_encode(["total" => $total, "per_sender" => $unread]);
} catch (Exception $e) {
    echo json_encode(["error" => true, "message" => $e->getMessage()]);
}
?>