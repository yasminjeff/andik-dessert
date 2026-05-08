<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");
require $_SERVER['DOCUMENT_ROOT'] . "/project/db.php";

try {
    $data = json_decode(file_get_contents("php://input"));

    $db = DB::connect();

    $sql = "UPDATE menu_items SET
    name=:name,
    category=:category,
    price=:price,
    stock=:stock,
    available=:available,
    image=:image,
    description=:description
    WHERE id=:id";

    $stmt = $db->prepare($sql);

    $stmt->execute([
        ':id' => $data->id,
        ':name' => $data->name,
        ':category' => $data->category,
        ':price' => $data->price,
        ':stock' => $data->stock,
        ':available' => $data->available,
        ':image' => $data->image,
        ':description' => $data->description
    ]);

    echo json_encode(["status" => "updated"]);

} catch (Exception $e) {
    echo json_encode(["error" => true, "message" => $e->getMessage()]);
}
?>