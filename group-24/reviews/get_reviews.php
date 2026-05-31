<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");
require __DIR__ . "/../db.php";
try {
   $db = DB::connect();
   $item_id = $_GET['item_id'] ?? null;

   if ($item_id) {
       $stmt = $db->prepare("SELECT * FROM reviews WHERE item_id = ? ORDER BY created_at DESC");
       $stmt->execute([$item_id]);
   } else {
       $stmt = $db->query("SELECT * FROM reviews ORDER BY created_at DESC");
   }

   $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
   echo json_encode($data);

} catch (Exception $e) {
   echo json_encode(["error" => true, "message" => $e->getMessage()]);
}
?>