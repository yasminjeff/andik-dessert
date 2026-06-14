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

    if (!$user) {
        echo json_encode(["error" => true, "message" => "Invalid email or password"]);
        exit;
    }

    // Check if account is locked
    if ($user['locked_until'] && strtotime($user['locked_until']) > time()) {
        $minsLeft = ceil((strtotime($user['locked_until']) - time()) / 60);
        echo json_encode(["error" => true, "message" => "Account locked due to too many failed attempts. Try again in $minsLeft minute(s)."]);
        exit;
    }

    if (!password_verify($password, $user['password'])) {
        $attempts = ($user['failed_attempts'] ?? 0) + 1;

        if ($attempts >= 5) {
            $lockUntil = gmdate('Y-m-d H:i:s', time() + (15 * 60) + (8 * 3600));
            $stmt = $db->prepare("UPDATE users SET failed_attempts = 0, locked_until = ? WHERE id = ?");
            $stmt->execute([$lockUntil, $user['id']]);
            echo json_encode(["error" => true, "message" => "Too many failed attempts. Account locked for 15 minutes."]);
            exit;
        }

        $stmt = $db->prepare("UPDATE users SET failed_attempts = ? WHERE id = ?");
        $stmt->execute([$attempts, $user['id']]);
        $remaining = 5 - $attempts;
        echo json_encode(["error" => true, "message" => "Invalid email or password. $remaining attempt(s) remaining."]);
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

    // Reset failed attempts on successful login
    $stmt = $db->prepare("UPDATE users SET failed_attempts = 0, locked_until = NULL WHERE id = ?");
    $stmt->execute([$user['id']]);

    unset($user['password']);
    unset($user['reset_new_password']);
    unset($user['failed_attempts']);
    unset($user['locked_until']);

    echo json_encode(["success" => true, "user" => $user]);

} catch (Exception $e) {
    echo json_encode(["error" => true, "message" => $e->getMessage()]);
}
?>