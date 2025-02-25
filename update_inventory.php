<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = intval($_POST["id"]);
    $type = $_POST["type"];
    $name = $conn->real_escape_string($_POST["name"]);
    $quantity = intval($_POST["quantity"]);
    $cost = floatval($_POST["cost"]);
    $purchase_date = $conn->real_escape_string($_POST["purchase_date"]);

    if ($type === "material") {
        $unit = $conn->real_escape_string($_POST["unit"]);
        $min_stock = intval($_POST["min_stock"]);
        $sql = "UPDATE raw_materials SET name='$name', quantity='$quantity', unit='$unit', min_stock='$min_stock', cost='$cost', purchase_date='$purchase_date' WHERE id=$id";
    } elseif ($type === "equipment") {
        $condition = $conn->real_escape_string($_POST["condition"]);
        $sql = "UPDATE equipment SET name='$name', quantity='$quantity', item_condition='$condition', cost='$cost', purchase_date='$purchase_date' WHERE id=$id";
    } else {
        die(json_encode(["error" => "Invalid type."]));
    }

    if ($conn->query($sql)) {
        echo "<script>alert('Item updated successfully!'); window.location.href='inventory_manage.php';</script>";
    } else {
        echo "<script>alert('Error updating item: " . $conn->error . "'); window.history.back();</script>";
    }
}
?>
