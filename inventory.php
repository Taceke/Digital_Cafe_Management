<?php 
include 'db_connect.php';

// Handle Form Submission for Adding Materials or Equipment
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_material'])) {
        $name = $_POST['name'];
        $quantity = $_POST['quantity'];
        $unit = $_POST['unit'];
        $min_stock = $_POST['min_stock'];
        $cost = $_POST['cost'];
        $purchase_date = date('Y-m-d H:i:s', strtotime($_POST['purchase_date']));

        // Insert into raw_materials
        $stmt = $conn->prepare("INSERT INTO raw_materials (name, quantity, unit, min_stock) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sdsi", $name, $quantity, $unit, $min_stock);
        if ($stmt->execute()) {
            $material_id = $stmt->insert_id; // Get the last inserted material ID

            // Insert purchase details
            $stmt = $conn->prepare("INSERT INTO purchases (material_id, quantity, cost, purchase_date) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("idds", $material_id, $quantity, $cost, $purchase_date);
            if ($stmt->execute()) {
                echo json_encode(["message" => "Material & Purchase added successfully"]);
            } else {
                echo json_encode(["message" => "Failed to add purchase"]);
            }
        } else {
            echo json_encode(["message" => "Failed to add material"]);
        }
        exit;
    }

    if (isset($_POST['add_equipment'])) {
        $name = $_POST['name'];
        $quantity = $_POST['quantity'];
        $item_condition = $_POST['item_condition'];
        $cost = $_POST['cost'];
        $purchase_date = date('Y-m-d H:i:s', strtotime($_POST['purchase_date']));
    
        // Insert into equipment table
        $stmt = $conn->prepare("INSERT INTO equipment (name, quantity, item_condition) VALUES (?, ?, ?)");
        $stmt->bind_param("sis", $name, $quantity, $item_condition);
    
        if ($stmt->execute()) {
            $equipment_id = $stmt->insert_id; // Get the last inserted equipment ID
    
            // Insert purchase details for equipment
            $stmt = $conn->prepare("INSERT INTO purchases (equipment_id, quantity, cost, purchase_date) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("idds", $equipment_id, $quantity, $cost, $purchase_date);
    
            if ($stmt->execute()) {
                echo json_encode(["message" => "Equipment & Purchase added successfully"]);
            } else {
                echo json_encode(["message" => "Failed to add purchase details"]);
            }
        } else {
            echo json_encode(["message" => "Failed to add equipment"]);
        }
        exit;
    }
    
}
?>

<?php

include './templates/header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="inventory.css">
    <title>Cafeteria Inventory</title>
</head>
<body onload="fetchInventory()">
    

    <div class="forms-container">
        <form id="material-form" method="POST">
            <h3 style="color: #6a4c93;">Add New Material</h3>
            <input type="text" name="name" placeholder="Material Name" required>
            <input type="number" name="quantity" placeholder="Quantity" required>
            <input type="text" name="unit" placeholder="Unit (e.g., kg, liters)" required>
            <input type="number" name="min_stock" placeholder="Minimum Stock" required>
            <input type="number" name="cost" placeholder="Cost" required step="0.01">
            <input type="date" name="purchase_date" required>
            <input type="hidden" name="add_material" value="1">
            <button type="submit">Add Material</button>
        </form>

        <form id="equipment-form" method="POST">
            <h3 style="color: #6a4c93;">Add New Equipment</h3>
            <input type="text" name="name" placeholder="Equipment Name" required>
            <input type="number" name="quantity" placeholder="Quantity" required>
            <select name="item_condition" required>
                <option value="New">New</option>
                <option value="Needs Repair">Needs Repair</option>
                <option value="Broken">Broken</option>
            </select>
            <input type="number" name="cost" placeholder="Cost" required step="0.01">
            <input type="date" name="purchase_date" required>
            <input type="hidden" name="add_equipment" value="1">
            <button type="submit">Add Equipment</button>
            <input type="date" name="" id="">
        </form>
    </div>

    <div class="more-button-container">
        <button onclick="window.location.href='inventory_manage.php'">More</button>
    </div>

    <div id="inventory-list"></div>
    <div id="equipment-list"></div>

    <script src="inventory.js"></script>
</body>
</html>
