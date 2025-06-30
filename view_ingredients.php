<?php
// Database connection
include 'db_connect.php';
?>

<?php
include './templates/header.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Assigned Ingredients</title>
    <!-- ✅ Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            /* background: green; */
            padding: 20px;
        }
        h2 {
            color: black;
            margin-bottom: 30px;
        }
        .card {
            margin-bottom: 25px;
        }
        .table thead {
            background-color: #e9ecef;
        }
        .no-ingredients {
            color: #6c757d;
            font-style: italic;
            padding: 10px;
        }
        h2{
            color: black;
        }
    </style>
</head>
<body>

<div class="container">
    <h2 class="text-center">🥘 Assigned Ingredients for Each Product</h2>

    <?php
    $products = $conn->query("SELECT id, name FROM product_added");
    while ($product = $products->fetch_assoc()) {
        echo "<div class='card shadow-sm'>";
        echo "<div class='card-header bg-primary text-white fw-bold'>" . htmlspecialchars($product['name']) . "</div>";
        echo "<div class='card-body'>";

        $product_id = $product['id'];
        $ingredients_sql = "
            SELECT ri.name AS raw_name, pi.quantity_required, pi.unit
            FROM product_ingredients pi
            JOIN raw_materials ri ON pi.material_id = ri.id
            WHERE pi.product_id = $product_id
        ";
        $ingredients_result = $conn->query($ingredients_sql);

        if ($ingredients_result->num_rows > 0) {
            echo "<div class='table-responsive'>";
            echo "<table class='table table-bordered'>";
            echo "<thead><tr><th>Raw Material</th><th>Quantity Required</th><th>Unit</th></tr></thead><tbody>";
            while ($row = $ingredients_result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['raw_name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['quantity_required']) . "</td>";
                echo "<td>" . htmlspecialchars($row['unit']) . "</td>";
                echo "</tr>";
            }
            echo "</tbody></table>";
            echo "</div>";
        } else {
            echo "<p class='no-ingredients'>No ingredients assigned.</p>";
        }

        echo "</div>";
        echo "</div>";
    }
    ?>
</div>

</body>
</html>
