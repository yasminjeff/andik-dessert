<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");
require $_SERVER['DOCUMENT_ROOT'] . "/project/db.php";

try {
    $data = json_decode(file_get_contents("php://input"));

    $db = DB::connect();

    $sql = "INSERT INTO menu_items
    (name, category, price, stock, available, image, description)
    VALUES
    (:name, :category, :price, :stock, :available, :image, :description)";

    $stmt = $db->prepare($sql);

    $stmt->execute([
        ':name' => $data->name,
        ':category' => $data->category,
        ':price' => $data->price,
        ':stock' => $data->stock,
        ':available' => $data->available,
        ':image' => $data->image,
        ':description' => $data->description
    ]);

    echo json_encode(["status" => "success"]);

} catch (Exception $e) {
    echo json_encode(["error" => true, "message" => $e->getMessage()]);
}
?>