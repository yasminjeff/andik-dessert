<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");
require $_SERVER['DOCUMENT_ROOT'] . "/project/db.php";

try {
    $input = json_decode(file_get_contents("php://input"), true);

    $id = $input['id'] ?? null;
    $reply = $input['reply'] ?? '';

    if (!$id) {
        echo json_encode(["error" => true, "message" => "Missing id"]);
        exit;
    }

    $db = DB::connect();

    $stmt = $db->prepare("UPDATE reviews SET reply = ? WHERE id = ?");
    $stmt->execute([$reply, $id]);

    echo json_encode(["success" => true]);

} catch (Exception $e) {
    echo json_encode(["error" => true, "message" => $e->getMessage()]);
}
?>