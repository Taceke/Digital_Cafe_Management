
For Report
<?php
 include './templates/header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Report - Cafeteria POS</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="script.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>

<style>
    #downloadExcel{
        background-color: #018786; 
    }
</style>
</head>
<body>
    <header>
        <!-- <h1>Cafeteria Sales Report</h1> -->
        <div class="header-buttons">
            <!-- <button onclick="printReport()">🖨 Print Report</button> -->
            <!-- <a href="download_report.php">📄 Download Report (CSV)</a> -->

            <a id="downloadExcel" href="download_report.php">📄 Download Excel</a>
           <a id="downloadpdf" href="downloadpdf.php">📄 Download Pdf</a>
<!-- 
           <a id="downloadExcel" href="download_report.php" onclick="downloadExcel()">📄 Download Excel</a>
           <a id="downloadpdf" href="downloadpdf.php" onclick="downloadPDF()">📄 Download Pdf</a> -->



            </div>
    </header>

    <main>
        <section class="table-container">
            <table>
                <thead>
                    <tr >
                        <th>Customer</th>
                        <th>Qty</th>
                        <th>Order Date</th>
                        <th>Salesperson</th>
                        <th>Payment Method</th>
                        <th>Discount</th>
                        <th>Total Discount</th>
                        <th>Total Tax</th>
                        <th>Total Amount</th>
                    </tr>
                </thead>
                <tbody id="reportData">
                    <!-- Report Data Will Be Loaded Here -->
                </tbody>
            </table>
        </section>

        <!-- Sidebar Calendar -->
        <aside class="sidebar">
            <h3>📅 Select Date Range</h3>
            <label for="startDate">Start Date:</label>
            <input type="date" id="startDate">
            <label for="endDate">End Date:</label>
            <input type="date" id="endDate">
            <button style="background-color: #018786; " onclick="fetchReportByDate()">Filter Report</button>
        </aside>
        <section class="chart-container">
    <canvas id="salesChart"></canvas>
</section>

    </main>
    <script>
    function updateDownloadLinks() {
        let startDate = document.getElementById("startDate").value;
        let endDate = document.getElementById("endDate").value;

        let excelLink = document.getElementById("downloadExcel");
        let pdfLink = document.getElementById("downloadpdf");

        let baseExcelURL = "download_report.php";
        let basePDFURL = "downloadpdf.php";

        if (startDate && endDate) {
            excelLink.href = `${baseExcelURL}?start_date=${startDate}&end_date=${endDate}`;
            pdfLink.href = `${basePDFURL}?start_date=${startDate}&end_date=${endDate}`;
        } else {
            excelLink.href = baseExcelURL;
            pdfLink.href = basePDFURL;
        }
    }

    document.getElementById("startDate").addEventListener("change", updateDownloadLinks);
    document.getElementById("endDate").addEventListener("change", updateDownloadLinks);
</script>


</body>
</html>





for fetch_report.php

<?php
include 'db_connect.php'; // Database connection

$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';

$sql = "SELECT * FROM sales_report WHERE 1";
if (!empty($start_date) && !empty($end_date)) {
    $sql .= " AND DATE(order_date) BETWEEN '$start_date' AND '$end_date'";
}
$sql .= " ORDER BY order_date DESC";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['customer']}</td>
                <td>{$row['qty']}</td>
                <td>{$row['order_date']}</td>
                <td>{$row['salesperson']}</td>
                <td>{$row['payment_method']}</td>
                <td>{$row['discount']}</td>
                <td>{$row['total_discount']}</td>
                <td>{$row['total_tax']}</td>
                <td>{$row['total_amount']}</td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='9'>No records found.</td></tr>";
}

$conn->close();
?>
