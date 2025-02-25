<?php
include 'db_connect.php';

// Fetch total sales
$salesQuery = "SELECT SUM(total_amount) AS total_sales FROM sales_report";
$salesResult = $conn->query($salesQuery);
$sales = ($salesResult->num_rows > 0) ? $salesResult->fetch_assoc()['total_sales'] : 0;

// Fetch total orders
$ordersQuery = "SELECT COUNT(*) AS total_orders FROM sales_report";
$ordersResult = $conn->query($ordersQuery);
$orders = ($ordersResult->num_rows > 0) ? $ordersResult->fetch_assoc()['total_orders'] : 0;

// Fetch total sales reports
$reportsQuery = "SELECT COUNT(DISTINCT salesperson) AS total_salespersons FROM sales_report";
$reportsResult = $conn->query($reportsQuery);
$salespersons = ($reportsResult->num_rows > 0) ? $reportsResult->fetch_assoc()['total_salespersons'] : 0;

// Send data as JSON
echo json_encode([
    "sales" => $sales,
    "orders" => $orders,
    "salespersons" => $salespersons
]);

$conn->close();
?>
