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
return $baseUrl . '/uploads/' . $filename;}

try {
    $db = DB::connect();

    $imagePath = saveUploadedImage();
    if (!$imagePath) {
        $imagePath = $_POST['image'] ?? null;
    }
    if (!$imagePath) {
        $imagePath = "https://placehold.co/400x300/F5C800/1A1A1A?text=" . urlencode($_POST['name'] ?? 'Item');
    }

    $sql = "INSERT INTO menu_items
        (name, category, price, stock, available, image, description, available_days)
        VALUES
        (:name, :category, :price, :stock, :available, :image, :description, :available_days)";

    $stmt = $db->prepare($sql);
    $stmt->execute([
        ':name' => $_POST['name'] ?? null,
        ':category' => $_POST['category'] ?? null,
        ':price' => $_POST['price'] ?? null,
        ':stock' => $_POST['stock'] ?? null,
        ':available' => $_POST['available'] ?? 0,
        ':image' => $imagePath,
        ':description' => $_POST['description'] ?? null,
        ':available_days' => $_POST['available_days'] ?? null
    ]);

    echo json_encode(["status" => "success"]);

} catch (Exception $e) {
    echo json_encode(["error" => true, "message" => $e->getMessage()]);
}
?>