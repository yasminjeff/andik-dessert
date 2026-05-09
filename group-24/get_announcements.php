<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
require $_SERVER['DOCUMENT_ROOT'] . "/project/db.php";

try {
    $db = DB::connect();
    $stmt = $db->query("SELECT * FROM announcements ORDER BY created_at DESC");
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($data);

} catch (Exception $e) {
    echo json_encode(["error" => true, "message" => $e->getMessage()]);
}
?>