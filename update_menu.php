<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");
require __DIR__ . "/db.php";

function saveUploadedImage() {
    if (empty($_FILES['image']['tmp_name'])) return null;
    $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/uploads';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
    $file = $_FILES['image'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png','webp','gif'];
    if (!in_array($ext, $allowed)) return null;
    $filename = uniqid('menu_', true) . '.' . $ext;
    $destination = $uploadDir . '/' . $filename;
    if (!move_uploaded_file($file['tmp_name'], $destination)) return null;
    $baseUrl = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
// Detect subfolder
$scriptDir = str_replace($_SERVER['DOCUMENT_ROOT'], '', __DIR__);
return $baseUrl . $scriptDir . '/uploads/' . $filename;
}

try {
    $db = DB::connect();

    $imagePath = saveUploadedImage();
    if (!$imagePath) {
        $imagePath = $_POST['image'] ?? null;
    }

    $sql = "UPDATE menu_items SET
        name=:name,
        category=:category,
        price=:price,
        stock=:stock,
        available=:available,
        image=:image,
        description=:description,
        available_days=:available_days
        WHERE id=:id";

    $stmt = $db->prepare($sql);
    $stmt->execute([
        ':id' => $_POST['id'] ?? null,
        ':name' => $_POST['name'] ?? null,
        ':category' => $_POST['category'] ?? null,
        ':price' => $_POST['price'] ?? null,
        ':stock' => $_POST['stock'] ?? null,
        ':available' => $_POST['available'] ?? 0,
        ':image' => $imagePath,
        ':description' => $_POST['description'] ?? null,
        ':available_days' => $_POST['available_days'] ?? null
    ]);

    echo json_encode(["status" => "updated"]);

} catch (Exception $e) {
    echo json_encode(["error" => true, "message" => $e->getMessage()]);
}
?>