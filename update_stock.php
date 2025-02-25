<?php
include 'db_connect.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['items'])) {
    echo json_encode(["success" => false, "message" => "No items provided"]);
    exit;
}

foreach ($data['items'] as $item) {
    $name = $conn->real_escape_string($item['name']);
    $quantity = (int) $item['quantity'];

    // Reduce the stock quantity
    $sql = "UPDATE product_added SET quantity = quantity - $quantity WHERE name = '$name'";
    $conn->query($sql);
}

// Respond with success
echo json_encode(["success" => true]);
?>
