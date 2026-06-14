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
    $role = $_GET['role'] ?? null;

    if (!$user_id || !$role) {
        echo json_encode(["error" => true, "message" => "Missing fields"]);
        exit;
    }

    $db = DB::connect();

    if ($role === 'admin') {
        $stmt = $db->prepare("SELECT id, name, email, role, status FROM users WHERE status IN ('active','disabled') AND id != ?");
        $stmt->execute([$user_id]);
    } else {
        $stmt = $db->prepare("SELECT id, name, email, role, status FROM users WHERE role = 'admin' AND id != ?");
        $stmt->execute([$user_id]);
    }

    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($users as &$u) {
        $stmt2 = $db->prepare("SELECT message, created_at FROM messages WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?) ORDER BY created_at DESC LIMIT 1");
        $stmt2->execute([$user_id, $u['id'], $u['id'], $user_id]);
        $last = $stmt2->fetch(PDO::FETCH_ASSOC);
        $u['last_message'] = $last['message'] ?? null;
        $u['last_message_time'] = $last['created_at'] ?? null;

        $stmt3 = $db->prepare("SELECT COUNT(*) as cnt FROM messages WHERE sender_id = ? AND receiver_id = ? AND is_read = 0");
        $stmt3->execute([$u['id'], $user_id]);
        $unread = $stmt3->fetch(PDO::FETCH_ASSOC);
        $u['unread_count'] = $unread['cnt'] ?? 0;
    }

    echo json_encode($users);
} catch (Exception $e) {
    echo json_encode(["error" => true, "message" => $e->getMessage()]);
}
?>