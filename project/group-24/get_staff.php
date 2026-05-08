<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
require $_SERVER['DOCUMENT_ROOT'] . "/project/db.php";

try {
    $db = DB::connect();
    $status = $_GET['status'] ?? null;

    if ($status) {
        $stmt = $db->prepare("SELECT id, name, email, role, status, created_at FROM users WHERE role = 'staff' AND status = ?");
        $stmt->execute([$status]);
    } else {
        $stmt = $db->query("SELECT id, name, email, role, status, created_at FROM users WHERE role = 'staff'");
    }

    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($data);

} catch (Exception $e) {
    echo json_encode(["error" => true, "message" => $e->getMessage()]);
}
?>