<?php
include 'db_connect.php';

$sql = "SELECT salesperson, COUNT(*) AS total_orders 
        FROM sales_report 
        GROUP BY salesperson";

$result = $conn->query($sql);

$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = [
        "salesperson" => $row['salesperson'],
        "total_orders" => (int)$row['total_orders']
    ];
}

echo json_encode($data);

$conn->close();
?>
