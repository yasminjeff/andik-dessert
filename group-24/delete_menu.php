<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");
require $_SERVER['DOCUMENT_ROOT'] . "/project/db.php";

try {
    $data = json_decode(file_get_contents("php://input"));

    $db = DB::connect();

    $stmt = $db->prepare("DELETE FROM menu_items WHERE id = :id");

    $stmt->execute([
        ':id' => $data->id
    ]);

    echo json_encode(["status" => "deleted"]);

} catch (Exception $e) {
    echo json_encode(["error" => true, "message" => $e->getMessage()]);
}
?>