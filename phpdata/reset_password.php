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

    $email            = $input['email']            ?? '';
    $new_password     = $input['new_password']     ?? '';
    $confirm_password = $input['confirm_password'] ?? '';

    if (!$email || !$new_password || !$confirm_password) {
        echo json_encode(["success" => false, "message" => "All fields are required."]);
        exit;
    }

    if ($new_password !== $confirm_password) {
        echo json_encode(["success" => false, "message" => "Passwords do not match."]);
        exit;
    }

    if (strlen($new_password) < 6) {
        echo json_encode(["success" => false, "message" => "Password must be at least 6 characters."]);
        exit;
    }

    $db = DB::connect();

    // Check email wujud dalam database
    $stmt = $db->prepare("SELECT id, role, status FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode(["success" => false, "message" => "Email not found."]);
        exit;
    }

    // Admin tak boleh request reset
    if ($user['role'] === 'admin') {
        echo json_encode(["success" => false, "message" => "Admin accounts cannot request password reset here. Please contact the developer."]);
        exit;
    }

    // Pending staff tak boleh reset
    if ($user['status'] === 'pending') {
        echo json_encode(["success" => false, "message" => "Your account is still pending approval."]);
        exit;
    }

    if ($user['status'] === 'disabled') {
        echo json_encode(["success" => false, "message" => "Your account is disabled. Please contact admin."]);
        exit;
    }

    $hashed = password_hash($new_password, PASSWORD_BCRYPT, ['cost' => 10]);
    $stmt = $db->prepare("UPDATE users SET reset_requested = 1, reset_new_password = ? WHERE id = ?");
    $stmt->execute([$hashed, $user['id']]);

    echo json_encode(["success" => true, "message" => "Request sent! An admin will review and approve your password reset shortly."]);

} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>