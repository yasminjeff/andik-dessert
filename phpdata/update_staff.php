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
    $id = $input['id'] ?? '';
    $name = $input['name'] ?? null;
    $email = $input['email'] ?? null;
    $password = $input['password'] ?? null;
    $status = $input['status'] ?? null;

    if (!$id) {
        echo json_encode(["error" => true, "message" => "Missing id"]);
        exit;
    }

    $db = DB::connect();

    // update status je
    if ($status && !$name) {
        $stmt = $db->prepare("UPDATE users SET status = ? WHERE id = ?");
        $stmt->execute([$status, $id]);
    } elseif ($password) {
        if (strlen($password) < 8) {
            echo json_encode(["error" => true, "message" => "Password must be at least 8 characters."]);
            exit;
        }
        if (!preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) || !preg_match('/[0-9]/', $password) || !preg_match('/[^A-Za-z0-9]/', $password)) {
            echo json_encode(["error" => true, "message" => "Password must include uppercase, lowercase, number, and symbol."]);
            exit;
        }
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $db->prepare("UPDATE users SET name = ?, email = ?, password = ? WHERE id = ?");
        $stmt->execute([$name, $email, $hashed, $id]);
    } else {
        $stmt = $db->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
        $stmt->execute([$name, $email, $id]);
    }

    echo json_encode(["success" => true]);

} catch (Exception $e) {
    echo json_encode(["error" => true, "message" => $e->getMessage()]);
}
?>