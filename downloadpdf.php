<?php
// Start output buffering to prevent header issues
ob_start();

// Include FPDF and database connection
require('library/fpdf.php');
include 'db_connect.php';

// Suppress warnings (but show fatal errors)
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);

// Check if FPDF is loaded
if (!class_exists('FPDF')) {
    die("Error: FPDF library is missing!");
}

// Ensure database connection is working
if (!$conn) {
    die("Error: Database connection failed: " . mysqli_connect_error());
}

// Get filtered date range from URL parameters
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date   = isset($_GET['end_date']) ? $_GET['end_date'] : '';

// Prepare the SQL query
$sql = "SELECT * FROM sales_report WHERE 1";
if (!empty($start_date) && !empty($end_date)) {
    $sql .= " AND DATE(order_date) BETWEEN '$start_date' AND '$end_date'";
}
$sql .= " ORDER BY order_date DESC";
$result = $conn->query($sql);

// Ensure query execution
if (!$result) {
    die("Error: Query failed: " . $conn->error);
}

// Create PDF document
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'Cafeteria Sales Report', 0, 1, 'C');
$pdf->Ln(5);

// Table Headers
$pdf->SetFont('Arial', 'B', 12);
$headers = ['Customer', 'Qty', 'Order Date', 'Salesperson', 'Payment', 'Discount', 'Charge', 'VAT', 'Total'];
$widths  = [30, 20, 40, 30, 30, 20, 20, 20, 30];

foreach ($headers as $i => $header) {
    $pdf->Cell($widths[$i], 10, $header, 1);
}
$pdf->Ln();

// Table Data
$pdf->SetFont('Arial', '', 10);
while ($row = $result->fetch_assoc()) {
    $data = [
        $row['customer'], $row['qty'], $row['order_date'], $row['salesperson'], $row['payment_method'],
        number_format($row['discount'], 2), number_format($row['charge_amount'], 2),
        number_format($row['vat_charge'], 2), number_format($row['total_amount'], 2)
    ];
    
    foreach ($data as $i => $cell) {
        $pdf->Cell($widths[$i], 10, $cell, 1);
    }
    $pdf->Ln();
}

// Clean output buffer and send PDF
ob_end_clean();
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="sales_report.pdf"');
$pdf->Output('D', 'sales_report.pdf');

// Close database connection
$conn->close();
exit;
?>
