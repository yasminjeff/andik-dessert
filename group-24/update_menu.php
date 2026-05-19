<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");
require $_SERVER['DOCUMENT_ROOT'] . "/project/db.php";

function saveUploadedImage() {
    if (empty($_FILES['image']['tmp_name'])) return null;
    $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/project/uploads';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

    $file = $_FILES['image'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png','webp','gif'];
    if (!in_array($ext, $allowed)) return null;

    $filename = uniqid('menu_', true) . '.' . $ext;
    $destination = $uploadDir . '/' . $filename;
    if (!move_uploaded_file($file['tmp_name'], $destination)) return null;

    return '/project/uploads/' . $filename;
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
        ':id' => $data->id ?? $_POST['id'],
        ':name' => $data->name ?? $_POST['name'],
        ':category' => $data->category ?? $_POST['category'],
        ':price' => $data->price ?? $_POST['price'],
        ':stock' => $data->stock ?? $_POST['stock'],
        ':available' => $data->available ?? ($_POST['available'] ?? 0),
        ':image' => $imagePath,
        ':description' => $data->description ?? $_POST['description'],
        ':available_days' => $data->available_days ?? $_POST['available_days'] ?? null
    ]);

    echo json_encode(["status" => "updated"]);

} catch (Exception $e) {
    echo json_encode(["error" => true, "message" => $e->getMessage()]);
}
?>