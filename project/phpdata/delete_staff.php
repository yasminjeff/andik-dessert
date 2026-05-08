<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");
require $_SERVER['DOCUMENT_ROOT'] . "/project/db.php";

try {
    $input = json_decode(file_get_contents("php://input"), true);
    $id = $input['id'] ?? '';

    if (!$id) {
        echo json_encode(["error" => true, "message" => "Missing id"]);
        exit;
    }

    $db = DB::connect();
    $stmt = $db->prepare("DELETE FROM users WHERE id = ? AND role = 'staff'");
    $stmt->execute([$id]);

    echo json_encode(["success" => true]);
} catch (Exception $e) {
    echo json_encode(["error" => true, "message" => $e->getMessage()]);
}
?>