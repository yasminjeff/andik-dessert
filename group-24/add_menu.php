<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");
require $_SERVER['DOCUMENT_ROOT'] . "/db.php";

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

    return 'https://andiksdesserts.infinityfreeapp.com/uploads/' . $filename;
}

try {
    $data = json_decode(file_get_contents("php://input"));
    $imagePath = saveUploadedImage();

    if (!$imagePath) {
        $imagePath = $_POST['image'] ?? null;
        if (!$imagePath && $data) {
            $imagePath = $data->image ?? null;
        }
    }

    $db = DB::connect();

    $sql = "INSERT INTO menu_items
    (name, category, price, stock, available, image, description, available_days)
    VALUES
    (:name, :category, :price, :stock, :available, :image, :description, :available_days)";

    $stmt = $db->prepare($sql);

    $stmt->execute([
        ':name' => $data->name ?? $_POST['name'],
        ':category' => $data->category ?? $_POST['category'],
        ':price' => $data->price ?? $_POST['price'],
        ':stock' => $data->stock ?? $_POST['stock'],
        ':available' => $data->available ?? ($_POST['available'] ?? 0),
        ':image' => $imagePath,
        ':description' => $data->description ?? $_POST['description'],
        ':available_days' => $data->available_days ?? $_POST['available_days'] ?? null
    ]);

    echo json_encode(["status" => "success"]);

} catch (Exception $e) {
    echo json_encode(["error" => true, "message" => $e->getMessage()]);
}
?>