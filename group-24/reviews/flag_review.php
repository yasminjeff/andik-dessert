<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require __DIR__ . "/../db.php";

$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'] ?? null;
$flagged = $data['flagged'] ?? 1;

try {
    $db = DB::connect();
    $stmt = $db->prepare("UPDATE reviews SET flagged = ? WHERE id = ?");
    $stmt->execute([$flagged, $id]);
    echo json_encode(["success" => true]);
} catch (Exception $e) {
    echo json_encode(["error" => true, "message" => $e->getMessage()]);
}
?>