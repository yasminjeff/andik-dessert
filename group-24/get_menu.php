<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
 
require $_SERVER['DOCUMENT_ROOT'] . "/project/db.php";
 
try {
    $db = DB::connect();
 
    // Get menu items with average rating and review count from reviews table
    $stmt = $db->query("
        SELECT 
            m.*,
            ROUND(AVG(r.rating), 1) AS avg_rating,
            COUNT(r.id)             AS review_count
        FROM menu_items m
        LEFT JOIN reviews r ON r.item_id = m.id
        GROUP BY m.id
        ORDER BY m.id DESC
    ");
 
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
 
    foreach ($data as &$item) {
        $item['available']     = (int)$item['available'];
        $item['price']         = (float)$item['price'];
        $item['stock']         = (int)$item['stock'];
        $item['avg_rating']    = $item['avg_rating'] ? (float)$item['avg_rating'] : 0;
        $item['review_count']  = (int)$item['review_count'];
    }
 
    echo json_encode($data);
 
} catch (Exception $e) {
    echo json_encode([
        "error"   => true,
        "message" => $e->getMessage()
    ]);
}
?>