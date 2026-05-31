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
    $name = $input['name'] ?? '';
    $email = $input['email'] ?? '';
    $password = $input['password'] ?? '';

    if (!$name || !$email || !$password) {
        echo json_encode(["error" => true, "message" => "All fields required"]);
        exit;
    }

    $db = DB::connect();

    $check = $db->prepare("SELECT id FROM users WHERE email = ?");
    $check->execute([$email]);
    if ($check->fetch()) {
        echo json_encode(["error" => true, "message" => "Email already exists"]);
        exit;
    }

    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $db->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'staff')");
    $stmt->execute([$name, $email, $hashed]);

    echo json_encode(["success" => true]);
} catch (Exception $e) {
    echo json_encode(["error" => true, "message" => $e->getMessage()]);
}
?>