<?php
include 'db_connect.php';

header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);
if (!$data || empty($data['items'])) {
    echo json_encode(["success" => false, "message" => "No items received."]);
    exit();
}

$conn->begin_transaction();

try {
    foreach ($data['items'] as $item) {
        $product_id = $item['product_id'];
        $quantity_sold = $item['quantity'];

        // Update product stock
        $updateProduct = $conn->prepare("UPDATE product_added SET quantity = quantity - ? WHERE id = ?");
        $updateProduct->bind_param("ii", $quantity_sold, $product_id);
        $updateProduct->execute();
        $updateProduct->close();

        // Deduct raw materials based on product_ingredients
        $ingredientQuery = $conn->prepare("SELECT material_id, quantity_required FROM product_ingredients WHERE product_id = ?");
        $ingredientQuery->bind_param("i", $product_id);
        $ingredientQuery->execute();
        $ingredientResult = $ingredientQuery->get_result();

        while ($row = $ingredientResult->fetch_assoc()) {
            $material_id = $row['material_id'];
            $required_qty = $row['quantity_required'] * $quantity_sold;

            // Deduct from raw materials
            $updateRawMaterial = $conn->prepare("UPDATE raw_materials SET quantity = quantity - ? WHERE id = ?");
            $updateRawMaterial->bind_param("di", $required_qty, $material_id);
            $updateRawMaterial->execute();
            $updateRawMaterial->close();
        }
        $ingredientQuery->close();

        // Deduct raw materials based on product_raw_materials
        $rawMaterialQuery = $conn->prepare("SELECT material_id, quantity_used FROM product_raw_materials WHERE product_id = ?");
        $rawMaterialQuery->bind_param("i", $product_id);
        $rawMaterialQuery->execute();
        $rawMaterialQuery->bind_result($material_id, $quantity_used);

        while ($rawMaterialQuery->fetch()) {
            $total_deduction = $quantity_sold * $quantity_used;

            // Deduct from raw materials stock
            $updateRawMaterial = $conn->prepare("UPDATE raw_materials SET quantity = quantity - ? WHERE id = ?");
            $updateRawMaterial->bind_param("di", $total_deduction, $material_id);
            $updateRawMaterial->execute();
            $updateRawMaterial->close();
        }

        $rawMaterialQuery->close();
    }

    $conn->commit();
    echo json_encode(["success" => true, "message" => "Stock updated successfully!"]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(["success" => false, "message" => "Error updating stock: " . $e->getMessage()]);
}

exit();
?>
