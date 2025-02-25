<?php
include './templates/header.php';
include 'db_connect.php';

// Fetch company charge and VAT info
$companyResult = $conn->query("SELECT charge_amount, vat_charge FROM company_info WHERE id=1");
$company = $companyResult->fetch_assoc();
$chargeAmount = $company['charge_amount'];
$vatCharge = $company['vat_charge'];
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
        <div class="header-buttons">
            <a id="downloadExcel" href="download_report.php">📄 Download Excel</a>
            <a id="downloadpdf" href="downloadpdf.php">📄 Download Pdf</a>
        </div>
    </header>

    <main>
        <section class="table-container">
            <table>
            <thead>
    <tr>
        <th>Customer</th>
        <th>Qty</th>
        <th>Order Date</th>
        <th>Salesperson</th>
        <th>Payment Method</th>
        <th>Discount</th>
        <th>Charge Amount (%)</th>  <!-- Updated column -->
        <th>VAT Charge (%)</th>  <!-- Updated column -->
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
            <button id="filterButton" style="background-color: #018786;">Filter Report</button>
            </aside>
        <section class="chart-container">
    <canvas id="salesChart"></canvas>
</section>

    </main>
    <script>
document.addEventListener("DOMContentLoaded", function() {
    // Ensure the fetchReportByDate function is called when the page is loaded
    fetchAllReports(); // Load all reports on page load
    updateDownloadLinks();

    // Bind the Filter Report button click event
    const filterButton = document.getElementById('filterButton');
    filterButton.addEventListener('click', fetchReportByDate); // Bind the event here
});

function fetchAllReports() {
    $.ajax({
        url: "fetch_report.php",
        type: "POST",
        success: function(response) {
            console.log("Initial Report Loaded");
            $("#reportData").html(response);
        },
        error: function() {
            alert("Error loading initial report data.");
        }
    });
}

function fetchReportByDate() {
    let startDate = document.getElementById("startDate").value;
    let endDate = document.getElementById("endDate").value;

    if (!startDate || !endDate) {
        console.warn("Please select both start and end dates.");
        return;
    }

    console.log("Fetching report for:", startDate, "to", endDate);

    $.ajax({
        url: "fetch_report.php",
        type: "POST",
        data: { start_date: startDate, end_date: endDate },
        success: function(response) {
            console.log("Server Response:", response.trim());

            if (!response.trim()) {
                $("#reportData").html(`
                    <tr>
                        <td colspan='9' style='text-align:center; font-weight: bold; color: red;'>
                            NO DATA IS RECORDED IN THIS DATE
                        </td>
                    </tr>
                `);
            } else {
                $("#reportData").html(response);
            }
        },
        error: function() {
            alert("Error fetching report data.");
        }
    });

    updateDownloadLinks(); // Update download links after filtering
}

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

// Attach event listeners for start and end date changes
document.getElementById("startDate").addEventListener("change", updateDownloadLinks);
document.getElementById("endDate").addEventListener("change", updateDownloadLinks);



</script>

</body>
</html>
