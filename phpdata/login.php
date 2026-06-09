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

    $email = $input['email'] ?? '';
    $password = $input['password'] ?? '';

    if (!$email || !$password) {
        echo json_encode(["error" => true, "message" => "Email and password required"]);
        exit;
    }

    $db = DB::connect();

    $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($password, $user['password'])) {
        echo json_encode(["error" => true, "message" => "Invalid email or password"]);
        exit;
    }

    if ($user['status'] === 'pending') {
        echo json_encode(["error" => true, "message" => "Your account is pending approval from admin. Please wait."]);
        exit;
    }

    if ($user['status'] === 'disabled') {
        echo json_encode(["error" => true, "message" => "Your account has been disabled. Please contact an administrator."]);
        exit;
    }

    unset($user['password']);

    echo json_encode(["success" => true, "user" => $user]);

} catch (Exception $e) {
    echo json_encode(["error" => true, "message" => $e->getMessage()]);
}
?>