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

// Fetch low stock materials
$lowStockQuery = "SELECT name, quantity FROM raw_materials WHERE quantity < min_stock";
$lowStockResult = $conn->query($lowStockQuery);

if ($lowStockResult->num_rows > 0) {
    echo "<script>alert('Warning: Some raw materials are running low!');</script>";
}

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

// Handle raw material deduction when a product is assigned materials
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['product_id'], $_POST['materials_used'])) {
    $product_id = $_POST['product_id'];
    $materials_input = $_POST['materials_used'];

    $materials_list = explode(",", $materials_input);

    foreach ($materials_list as $entry) {
        $parts = explode("-", trim($entry));
        if (count($parts) == 2) {
            $material_name = trim($parts[0]);
            $quantity_used = floatval(trim($parts[1]));

            // Get material details
            $materialQuery = "SELECT id, quantity FROM raw_materials WHERE name = '$material_name' LIMIT 1";
            $materialResult = $conn->query($materialQuery);

            if ($materialResult->num_rows > 0) {
                $materialRow = $materialResult->fetch_assoc();
                $material_id = $materialRow['id'];
                $current_quantity = floatval($materialRow['quantity']);

                if ($current_quantity >= $quantity_used) {
                    // Deduct from inventory
                    $new_quantity = $current_quantity - $quantity_used;
                    $updateQuery = "UPDATE raw_materials SET quantity = '$new_quantity' WHERE id = '$material_id'";
                    $conn->query($updateQuery);
                } else {
                    echo "<script>alert('Insufficient stock for $material_name!');</script>";
                }
            }
        }
    }
}
?>

<?php include './templates/header.php'; ?>

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
        <td><?= isset($material['purchase_date']) ? date('Y-m-d', strtotime($material['purchase_date'])) : 'N/A' ?></td>
        <td class="action-buttons">
            <button class="edit-btn" onclick="editMaterial(<?= $material['id'] ?>)">Edit</button>
            <button class="delete-btn" onclick="deleteMaterial(<?= $material['id'] ?>)">Delete</button>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<h3 style="color: #007bff;">Equipment</h3>
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
        <td><?= isset($equip['purchase_date']) ? date('Y-m-d', strtotime($equip['purchase_date'])) : 'N/A' ?></td>
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
</script>
</body>
</html>
