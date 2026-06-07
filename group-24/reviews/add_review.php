<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, X-Requested-With");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}
require __DIR__ . "/../db.php";
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

    // Check duplicate — same item_id dan same comment
    $checkStmt = $db->prepare("SELECT COUNT(*) FROM reviews WHERE item_id = ? AND comment = ?");
    $checkStmt->execute([$item_id, $comment]);
    $count = $checkStmt->fetchColumn();

    if ($count > 0) {
    echo json_encode(["error" => true, "message" => "You have already submitted a similar comment for this item."]);
    exit;
    }

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