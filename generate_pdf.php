<?php
require('library/fpdf.php');
include 'db_connect.php';  // Database connection

// Get date range parameters
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date   = isset($_GET['end_date']) ? $_GET['end_date'] : '';

// Build SQL query for date filtering
$sql = "SELECT * FROM sales_report WHERE 1";
if (!empty($start_date) && !empty($end_date)) {
    $sql .= " AND DATE(order_date) BETWEEN '$start_date' AND '$end_date'";
}
$sql .= " ORDER BY order_date DESC";
$result = $conn->query($sql);

// Create new PDF document using FPDF
$pdf = new FPDF();
$pdf->AddPage();

// Title
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'Cafeteria Sales Report', 0, 1, 'C');
$pdf->Ln(5);

// Table Header
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(30, 10, 'Customer', 1);
$pdf->Cell(20, 10, 'Qty', 1);
$pdf->Cell(40, 10, 'Order Date', 1);
$pdf->Cell(30, 10, 'Salesperson', 1);
$pdf->Cell(30, 10, 'Payment', 1);
$pdf->Cell(20, 10, 'Discount', 1);
$pdf->Cell(20, 10, 'Tax', 1);
$pdf->Cell(30, 10, 'Total', 1);
$pdf->Ln();

// Table Data
$pdf->SetFont('Arial', '', 10);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $pdf->Cell(30, 10, $row['customer'], 1);
        $pdf->Cell(20, 10, $row['qty'], 1);
        $pdf->Cell(40, 10, $row['order_date'], 1);
        $pdf->Cell(30, 10, $row['salesperson'], 1);
        $pdf->Cell(30, 10, $row['payment_method'], 1);
        $pdf->Cell(20, 10, number_format($row['discount'], 2), 1);
        $pdf->Cell(20, 10, number_format($row['total_tax'], 2), 1);
        $pdf->Cell(30, 10, number_format($row['total_amount'], 2), 1);
        $pdf->Ln();
    }
} else {
    $pdf->Cell(0, 10, 'No records found.', 1, 1, 'C');
}

// Output the PDF as a download
$pdf->Output('D', 'sales_report.pdf');

$conn->close();
?>
