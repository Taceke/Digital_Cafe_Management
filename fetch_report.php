<?php
include 'db_connect.php';

// Fetch start and end dates from the request (if provided)
$startDate = $_POST['start_date'] ?? '';
$endDate = $_POST['end_date'] ?? '';

// Fetch company charge and VAT info
$companyResult = $conn->query("SELECT charge_amount, vat_charge FROM company_info WHERE id=1");
$company = $companyResult->fetch_assoc();
$chargeAmount = $company['charge_amount'];
$vatCharge = $company['vat_charge'];

// Base query: Fetch all sales data
$query = "SELECT * FROM sales_report";

// If date range is selected, filter the data
if (!empty($startDate) && !empty($endDate)) {
    $startDate = date('Y-m-d', strtotime($startDate)) . " 00:00:00";
    $endDate = date('Y-m-d', strtotime($endDate)) . " 23:59:59";
    $query .= " WHERE order_date BETWEEN '$startDate' AND '$endDate'";
}

$query .= " ORDER BY order_date DESC"; // Sort by latest orders

$result = $conn->query($query);

$output = "";
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $output .= "<tr>
            <td>{$row['customer']}</td>
            <td>{$row['qty']}</td>
            <td>{$row['order_date']}</td>
            <td>{$row['salesperson']}</td>
            <td>{$row['payment_method']}</td>
            <td>{$row['discount']}%</td>
            <td>{$chargeAmount}%</td>  
            <td>{$vatCharge}%</td>  
            <td>{$row['total_amount']} ETB</td>
        </tr>";
    }
} else {
    $output = "<tr><td colspan='9' style='text-align:center; font-weight: bold; color: red;'>NO DATA IS RECORDED IN THIS DATE</td></tr>";
}

echo $output;
?>
