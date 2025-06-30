<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];
    $material_id = $_POST['material_id'];
    $quantity_used = $_POST['quantity_used'];

    $stmt = $conn->prepare("INSERT INTO product_raw_materials (product_id, material_id, quantity_used) VALUES (?, ?, ?)");
    $stmt->bind_param("iid", $product_id, $material_id, $quantity_used);
    
    if ($stmt->execute()) {
        echo "Raw material usage added successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
