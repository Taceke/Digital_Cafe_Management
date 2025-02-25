<?php
require __DIR__ . '/vendor/autoload.php';
include 'db_connect.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Get filtered date range
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date   = isset($_GET['end_date']) ? $_GET['end_date'] : '';

$sql = "SELECT * FROM sales_report WHERE 1";
if (!empty($start_date) && !empty($end_date)) {
    $sql .= " AND DATE(order_date) BETWEEN '$start_date' AND '$end_date'";
}
$sql .= " ORDER BY order_date DESC";

$result = $conn->query($sql);

if ($result->num_rows == 0) {
    die("No data available for the selected date range.");
}

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$sheet->setTitle('Cafeteria Sales Report');
$headers = ['Customer', 'Qty', 'Order Date', 'Salesperson', 'Payment Method', 'Discount', 'Charge Amount', 'VAT Charge', 'Total Amount'];

$col = 'A';
foreach ($headers as $header) {
    $sheet->setCellValue($col . '1', $header);
    $col++;
}

$rowNumber = 2;
while ($row = $result->fetch_assoc()) {
    $sheet->setCellValue('A' . $rowNumber, $row['customer']);
    $sheet->setCellValue('B' . $rowNumber, $row['qty']);
    $sheet->setCellValue('C' . $rowNumber, $row['order_date']);
    $sheet->setCellValue('D' . $rowNumber, $row['salesperson']);
    $sheet->setCellValue('E' . $rowNumber, $row['payment_method']);
    $sheet->setCellValue('F' . $rowNumber, number_format($row['discount'], 2));
    $sheet->setCellValue('G' . $rowNumber, number_format($row['charge_amount'], 2));
    $sheet->setCellValue('H' . $rowNumber, number_format($row['vat_charge'], 2));
    $sheet->setCellValue('I' . $rowNumber, number_format($row['total_amount'], 2));
    $rowNumber++;
}

// Set headers for Excel file download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="sales_report.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');

$conn->close();
exit;
?>
