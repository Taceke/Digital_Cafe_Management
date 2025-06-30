<?php
include 'db_connect.php'; // Include your database connection file

$category = isset($_GET['category']) ? $_GET['category'] : 'All';

// Add 'unit' to the selected fields
$sql = "SELECT id, name, category, price, image, description, quantity, unit FROM product_added";

if ($category !== 'All') {
    $sql .= " WHERE category = ?";
}

$stmt = $conn->prepare($sql);

if ($category !== 'All') {
    $stmt->bind_param("s", $category);
}

$stmt->execute();
$result = $stmt->get_result();

$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

header("Content-Type: application/json");
echo json_encode($products);
?>
