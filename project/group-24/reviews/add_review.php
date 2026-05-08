<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");
require $_SERVER['DOCUMENT_ROOT'] . "/project/db.php";

try {
    $input = json_decode(file_get_contents("php://input"), true);

    $item_id = $input['item_id'] ?? null;
    $rating = $input['rating'] ?? null;
    $comment = $input['comment'] ?? '';

    if (!$item_id || !$rating) {
        echo json_encode(["error" => true, "message" => "Missing fields"]);
        exit;
    }

    $db = DB::connect();

    $stmt = $db->prepare("INSERT INTO reviews (item_id, rating, comment) VALUES (?, ?, ?)");
    $stmt->execute([$item_id, $rating, $comment]);

    // update avg_rating dan review_count dalam menu_items
    $stmt2 = $db->prepare("UPDATE menu_items SET
        avg_rating = (SELECT AVG(rating) FROM reviews WHERE item_id = ?),
        review_count = (SELECT COUNT(*) FROM reviews WHERE item_id = ?)
        WHERE id = ?");
    $stmt2->execute([$item_id, $item_id, $item_id]);

    echo json_encode(["success" => true]);

} catch (Exception $e) {
    echo json_encode(["error" => true, "message" => $e->getMessage()]);
}
?>