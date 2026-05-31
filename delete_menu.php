<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, X-Requested-With");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require __DIR__ . "/db.php";

try {
    $data = json_decode(file_get_contents("php://input"));

    $db = DB::connect();

    $stmt = $db->prepare("DELETE FROM menu_items WHERE id = :id");
    $stmt->execute([':id' => $data->id]);

    echo json_encode(["status" => "deleted"]);

} catch (Exception $e) {
    echo json_encode(["error" => true, "message" => $e->getMessage()]);
}
?>