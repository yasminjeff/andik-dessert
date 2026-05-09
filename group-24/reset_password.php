<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");
require $_SERVER['DOCUMENT_ROOT'] . "/project/db.php";

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
    $stmt = $db->prepare("SELECT id, status FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode(["success" => false, "message" => "Email not found."]);
        exit;
    }

    // Pending staff tak boleh reset
    if ($user['status'] === 'pending') {
        echo json_encode(["success" => false, "message" => "Your account is still pending approval."]);
        exit;
    }

    $hashed = password_hash($new_password, PASSWORD_DEFAULT);
    $stmt = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->execute([$hashed, $user['id']]);

    echo json_encode(["success" => true, "message" => "Password updated successfully."]);

} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>