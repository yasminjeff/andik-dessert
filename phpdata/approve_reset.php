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
    $id = $input['id'] ?? null;
    $action = $input['action'] ?? ''; // 'approve' or 'reject'

    if (!$id || !in_array($action, ['approve', 'reject'])) {
        echo json_encode(["error" => true, "message" => "Invalid request"]);
        exit;
    }

    $db = DB::connect();

    if ($action === 'approve') {
        $stmt = $db->prepare("SELECT reset_new_password FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row || !$row['reset_new_password']) {
            echo json_encode(["error" => true, "message" => "No pending reset request found"]);
            exit;
        }

        $stmt = $db->prepare("UPDATE users SET password = ?, reset_requested = 0, reset_new_password = NULL, failed_attempts = 0, locked_until = NULL WHERE id = ?");
        $stmt->execute([$row['reset_new_password'], $id]);

        echo json_encode(["success" => true, "message" => "Password reset approved"]);
    } else {
        $stmt = $db->prepare("UPDATE users SET reset_requested = 0, reset_new_password = NULL WHERE id = ?");
        $stmt->execute([$id]);

        echo json_encode(["success" => true, "message" => "Reset request rejected"]);
    }

} catch (Exception $e) {
    echo json_encode(["error" => true, "message" => $e->getMessage()]);
}
?>