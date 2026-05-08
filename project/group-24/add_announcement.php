<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");
require $_SERVER['DOCUMENT_ROOT'] . "/project/db.php";

try {
    $input = json_decode(file_get_contents("php://input"), true);

    $title = $input['title'] ?? '';
    $body = $input['body'] ?? '';

    if (!$title || !$body) {
        echo json_encode(["error" => true, "message" => "All fields required"]);
        exit;
    }

    $db = DB::connect();
    $stmt = $db->prepare("INSERT INTO announcements (title, body) VALUES (?, ?)");
    $stmt->execute([$title, $body]);

    echo json_encode(["success" => true]);

} catch (Exception $e) {
    echo json_encode(["error" => true, "message" => $e->getMessage()]);
}
?>