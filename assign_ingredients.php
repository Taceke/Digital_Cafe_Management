<?php
// Database connection
include 'db_connect.php';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = $_POST['product_id'];
    $material_ids = $_POST['material_id'];
    $quantities = $_POST['quantity_required'];
    $units = $_POST['unit'];

    for ($i = 0; $i < count($material_ids); $i++) {
        $material_id = $material_ids[$i];
        $quantity_required = $quantities[$i];
        $unit = $units[$i];

        $stmt = $conn->prepare("INSERT INTO product_ingredients (product_id, material_id, quantity_required, unit) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iids", $product_id, $material_id, $quantity_required, $unit);
        $stmt->execute();
    }

    echo "<script>alert('Ingredients assigned successfully!');</script>";
}
?>

<?php
include './templates/header.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Assign Ingredients to Product</title>
    <!-- ✅ Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .ingredient-row .form-control {
            margin-bottom: 10px;
        }
        .remove-btn {
            margin-top: 32px;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">🍽️ Assign Ingredients to Product</h4>
        </div>
        <div class="card-body">
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="product_id" class="form-label"><strong>Select Product:</strong></label>
                    <select name="product_id" class="form-select" required>
                        <option value="">-- Select Product --</option>
                        <?php
                        $products = $conn->query("SELECT id, name FROM product_added");
                        while ($product = $products->fetch_assoc()) {
                            echo "<option value='{$product['id']}'>{$product['name']}</option>";
                        }
                        ?>
                    </select>
                </div>

                <h5 class="mt-4">🧪 Ingredients:</h5>
                <div id="ingredients-container"></div>

                <button type="button" class="btn btn-outline-primary mt-2" onclick="addIngredientRow()">+ Add Ingredient</button>
                <button type="submit" class="btn btn-success mt-2 ms-2">✅ Assign Ingredients</button>
            </form>
        </div>
    </div>
</div>

<!-- ✅ JavaScript to Add Ingredient Rows -->
<script>
    function addIngredientRow() {
        const container = document.getElementById('ingredients-container');
        const row = document.createElement('div');
        row.className = 'row align-items-end ingredient-row';

        row.innerHTML = `
            <div class="col-md-4">
                <label class="form-label">Material</label>
                <select name="material_id[]" class="form-select" required>
                    <?php
                    $raw = $conn->query("SELECT id, name FROM raw_materials");
                    while ($r = $raw->fetch_assoc()) {
                        echo "<option value='{$r['id']}'>{$r['name']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Quantity</label>
                <input type="number" name="quantity_required[]" step="0.01" class="form-control" placeholder="Qty Required" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Unit</label>
                <input type="text" name="unit[]" class="form-control" placeholder="e.g., kg, pcs" required>
            </div>
            <div class="col-md-2 remove-btn">
                <button type="button" class="btn btn-danger" onclick="this.closest('.ingredient-row').remove()">🗑️ Remove</button>
            </div>
        `;

        container.appendChild(row);
    }
</script>
</body>
</html>
