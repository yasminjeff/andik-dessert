<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
require $_SERVER['DOCUMENT_ROOT'] . "/project/db.php";

try {
    $db = DB::connect();
    $stmt = $db->query("SELECT * FROM menu_items"); 
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($data as &$item) {
        $item['available'] = (int)$item['available'];
        $item['price'] = (float)$item['price'];
        $item['stock'] = (int)$item['stock'];
    }

    echo json_encode($data);

} catch (Exception $e) {
    echo json_encode([
        "error" => true,
        "message" => $e->getMessage()
    ]);
}
?>