<?php
include 'db_connect.php';
session_start();

if ($_SESSION["role"] !== "admin") {
    die("Unauthorized");
}

$name = $_POST["name"];
$price = $_POST["price"];
$category = $_POST["category"];
$image = $_POST["image"];
$stock = $_POST["stock"];

$query = "INSERT INTO products (name, price, category, image, stock) VALUES ('$name', '$price', '$category', '$image', '$stock')";
$conn->query($query);
echo json_encode(["success" => true]);

?>
