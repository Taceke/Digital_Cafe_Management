<?php
include 'db_connect.php';

// Fetch products
$productQuery = "SELECT id, name FROM product_added";
$productResult = $conn->query($productQuery);

// Fetch raw materials
$materialQuery = "SELECT id, name, unit, quantity FROM raw_materials";
$materialResult = $conn->query($materialQuery);

$materials = [];
while ($row = $materialResult->fetch_assoc()) {
    $materials[$row['id']] = ['name' => $row['name'], 'unit' => $row['unit'], 'quantity' => $row['quantity']];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = $_POST['product_id'];
    $materials_used = $_POST['materials'];
    $insufficient_stock = false;

    foreach ($materials_used as $material_id => $quantity_used) {
        if ($quantity_used > 0) {
            $available_quantity = $materials[$material_id]['quantity'];
            if ($available_quantity < $quantity_used) {
                $insufficient_stock = true;
                $errorMessage = "Not enough stock for " . $materials[$material_id]['name'] . ". Available: " . $available_quantity;
                break;
            }
        }
    }

    if (!$insufficient_stock) {
        foreach ($materials_used as $material_id => $quantity_used) {
            if ($quantity_used > 0) {
                $new_quantity = $materials[$material_id]['quantity'] - $quantity_used;
                $updateStockQuery = "UPDATE raw_materials SET quantity = '$new_quantity' WHERE id = '$material_id'";
                $conn->query($updateStockQuery);

                $insertQuery = "INSERT INTO product_raw_materials (product_id, material_id, quantity_used) 
                                VALUES ('$product_id', '$material_id', '$quantity_used')";
                $conn->query($insertQuery);
            }
        }
        $successMessage = "Raw material usage added successfully, and stock updated!";
    }
}

$usageQuery = "
    SELECT 
        p.name AS product_name, 
        GROUP_CONCAT(CONCAT(rm.name, ' (', pmu.quantity_used, ' ', rm.unit, ')') SEPARATOR ', ') AS materials_used
    FROM product_raw_materials pmu
    JOIN product_added p ON pmu.product_id = p.id
    JOIN raw_materials rm ON pmu.material_id = rm.id
    GROUP BY p.id";
$usageResult = $conn->query($usageQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Assign Raw Materials</title>
    <link rel="stylesheet" href="product_material_usage.css">
</head>
<body>
    <h2>Assign Raw Materials to a Product</h2>
    <?php if (isset($successMessage)) echo "<p style='color:green;'>$successMessage</p>"; ?>
    <?php if (isset($errorMessage)) echo "<p style='color:red;'>$errorMessage</p>"; ?>
    <form method="POST">
        <label>Select Product:</label>
        <select name="product_id" required>
            <option value="">-- Select Product --</option>
            <?php while ($product = $productResult->fetch_assoc()): ?>
                <option value="<?= $product['id'] ?>"><?= htmlspecialchars($product['name']) ?></option>
            <?php endwhile; ?>
        </select>
        <h3>Raw Materials</h3>
        <?php foreach ($materials as $id => $material): ?>
            <label><?= htmlspecialchars($material['name']) ?> (<?= $material['unit'] ?>):</label>
            <input type="number" name="materials[<?= $id ?>]" min="0" step="any" value="0">
            <br>
        <?php endforeach; ?>
        <button type="submit">Add</button>
    </form>

    <br><br>
    <h3>Existing Product-Material Usage</h3>
    <table border="1">
        <tr>
            <th>Product</th>
            <th>Raw Materials Used (Quantity)</th>
        </tr>
        <?php while ($usage = $usageResult->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($usage['product_name']) ?></td>
                <td><?= htmlspecialchars($usage['materials_used']) ?></td>
            </tr>
        <?php endwhile; ?>
    </table>

    <br><br>
    <h3>Current Raw Material Stock</h3>
    <table border="1">
        <tr>
            <th>Material Name</th>
            <th>Available Quantity</th>
            <th>Unit</th>
        </tr>
        <?php
        $materialStockQuery = "SELECT name, quantity, unit FROM raw_materials";
        $stockResult = $conn->query($materialStockQuery);
        while ($stock = $stockResult->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($stock['name']) ?></td>
                <td><?= htmlspecialchars($stock['quantity']) ?></td>
                <td><?= htmlspecialchars($stock['unit']) ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
