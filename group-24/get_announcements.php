<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
$dbPath = ($_SERVER['HTTP_HOST'] === 'localhost') 
    ? $_SERVER['DOCUMENT_ROOT'] . "/project/db.php" 
    : $_SERVER['DOCUMENT_ROOT'] . "/db.php";
require $dbPath;

try {
    $db = DB::connect();
    $stmt = $db->query("SELECT * FROM announcements ORDER BY created_at DESC");
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($data);

} catch (Exception $e) {
    echo json_encode(["error" => true, "message" => $e->getMessage()]);
}
?>