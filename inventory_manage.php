<?php
include 'db_connect.php'; // Ensure database connection

// Fetch raw materials with cost and purchase date
$materialsQuery = "
    SELECT rm.*, p.cost, p.purchase_date 
    FROM raw_materials rm
    LEFT JOIN purchases p ON rm.id = p.material_id";

$materialsResult = $conn->query($materialsQuery);

if (!$materialsResult) {
    die("Error fetching raw materials: " . $conn->error);
}

$materials = $materialsResult->fetch_all(MYSQLI_ASSOC);

// Fetch equipment with cost and purchase date
$equipmentQuery = "
    SELECT e.*, p.cost, p.purchase_date 
    FROM equipment e
    LEFT JOIN purchases p ON e.id = p.equipment_id";

$equipmentResult = $conn->query($equipmentQuery);

if (!$equipmentResult) {
    die("Error fetching equipment: " . $conn->error);
}

$equipment = $equipmentResult->fetch_all(MYSQLI_ASSOC);

?>

<?php

include './templates/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="invet_mana.css">
    <title>Manage Inventory</title>
</head>
<body>
<button class="small-back-btn" onclick="window.location.href='inventory.php'">Back</button>

    <h3 style="color: #007bff;">Raw Materials</h3>
    <table>
        <tr>
            <th>Name</th>
            <th>Quantity</th>
            <th>Unit</th>
            <th>Min Stock</th>
            <th>Cost</th>
            <th>Purchase Date</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($materials as $material): ?>
        <tr class="<?= $material['quantity'] < $material['min_stock'] ? 'low-stock' : '' ?>">
            <td><?= htmlspecialchars($material['name']) ?></td>
            <td><?= $material['quantity'] ?></td>
            <td><?= htmlspecialchars($material['unit']) ?></td>
            <td><?= $material['min_stock'] ?></td>
            <td><?= isset($material['cost']) ? number_format($material['cost'], 2) : 'N/A' ?></td>
            <td><?= isset($material['purchase_date']) ? date('Y-m-d ', strtotime($material['purchase_date'])) : 'N/A' ?></td>
            <td class="action-buttons">
                <button class="edit-btn" onclick="editMaterial(<?= $material['id'] ?>)">Edit</button>
                <button class="delete-btn" onclick="deleteMaterial(<?= $material['id'] ?>)">Delete</button>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <h3  style="color: #007bff;">Equipment</h3>
    <table>
        <tr>
            <th>Name</th>
            <th>Quantity</th>
            <th>Condition</th>
            <th>Cost</th>
            <th>Purchase Date</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($equipment as $equip): ?>
        <tr>
            <td><?= htmlspecialchars($equip['name']) ?></td>
            <td><?= $equip['quantity'] ?></td>
            <td><?= htmlspecialchars($equip['item_condition']) ?></td>
            <td><?= isset($equip['cost']) ? number_format($equip['cost'], 2) : 'N/A' ?></td>
            <td><?= isset($equip['purchase_date']) ? date('Y-m-d ', strtotime($equip['purchase_date'])) : 'N/A' ?></td>
            <td class="action-buttons">
                <button class="edit-btn" onclick="editEquipment(<?= $equip['id'] ?>)">Edit</button>
                <button class="delete-btn" onclick="deleteEquipment(<?= $equip['id'] ?>)">Delete</button>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <script>
    function editMaterial(id) {
        window.location.href = `inventory.php?id=${id}&type=material`;
    }

    function deleteMaterial(id) {
        if (confirm("Are you sure you want to delete this material?")) {
            fetch(`delete_inventory.php?id=${id}&type=material`, { method: "GET" })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert("Material deleted successfully!");
                        location.reload();
                    } else {
                        alert("Error: " + data.error);
                    }
                });
        }
    }

    function editEquipment(id) {
        window.location.href = `inventory.php?id=${id}&type=equipment`;
    }

    function deleteEquipment(id) {
        if (confirm("Are you sure you want to delete this equipment?")) {
            fetch(`delete_inventory.php?id=${id}&type=equipment`, { method: "GET" })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert("Equipment deleted successfully!");
                        location.reload();
                    } else {
                        alert("Error: " + data.error);
                    }
                });
        }
    }
    </script>
</body>
</html>
