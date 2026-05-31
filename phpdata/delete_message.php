<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");
require __DIR__ . "/../db.php";

try {
    $input = json_decode(file_get_contents("php://input"), true);
    $id = $input['id'] ?? null;
    $user_id = $input['user_id'] ?? null;

    if (!$id || !$user_id) {
        echo json_encode(["error" => true, "message" => "Missing fields"]);
        exit;
    }

    $db = DB::connect();
    $stmt = $db->prepare("DELETE FROM messages WHERE id = ?");
    $stmt->execute([$id]);
    echo json_encode(["success" => true]);
} catch (Exception $e) {
    echo json_encode(["error" => true, "message" => $e->getMessage()]);
}
?>