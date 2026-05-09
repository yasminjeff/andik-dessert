<?php
require "db.php";
header('Content-Type: application/json');

try {
    $db = DB::connect();

    $category = $_GET['category'] ?? 'All';

    if ($category === 'All') {
        $stmt = $db->query("SELECT * FROM menu_items");
    } else {
        $stmt = $db->prepare("SELECT * FROM menu_items WHERE category = ?");
        $stmt->execute([$category]);
    }

    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // normalize data
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