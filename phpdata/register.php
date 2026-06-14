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

    if (strlen($password) < 8) {
        echo json_encode(["error" => true, "message" => "Password must be at least 8 characters."]);
        exit;
    }

    if (!preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) || !preg_match('/[0-9]/', $password) || !preg_match('/[^A-Za-z0-9]/', $password)) {
        echo json_encode(["error" => true, "message" => "Password must include uppercase, lowercase, number, and symbol."]);
        exit;
    }

    // block admin email
    if ($email === 'admin@andiks.my') {
        echo json_encode(["error" => true, "message" => "This email is not allowed for registration."]);
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

    // status pending — tunggu admin approve
    $stmt = $db->prepare("INSERT INTO users (name, email, password, role, status) VALUES (?, ?, ?, 'staff', 'pending')");
    $stmt->execute([$name, $email, $hashed]);

    echo json_encode(["success" => true, "message" => "Registration submitted! Please wait for admin approval."]);

} catch (Exception $e) {
    echo json_encode(["error" => true, "message" => $e->getMessage()]);
}
?>