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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
    <style>
        #downloadExcel {
            background-color: #018786;
        }
/* 
        .chart-container {
    width: 100%;
    max-width: 350px;
    margin: 2rem auto;
} */

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
                        <th>Charge Amount (%)</th>
                        <th>VAT Charge (%)</th>
                        <th>Total Amount</th>
                    </tr>
                </thead>
                <tbody id="reportData">
                    <!-- Report Data Will Be Loaded Here -->
                </tbody>
            </table>
        </section>

        <aside class="sidebar">
            <h3>📅 Select Date Range</h3>
            <label for="startDate">Start Date:</label>
            <input type="date" id="startDate">
            <label for="endDate">End Date:</label>
            <input type="date" id="endDate">
            <button id="filterButton" style="background-color: #018786;">Filter Report</button>
        </aside>

        <section class="chart-container">
            <h3 style="text-align:center">📊 Sales Distribution by Salesperson</h3>
            <canvas id="salesPieChart" width="100" height="100"></canvas>
            </section>
    </main>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            fetchAllReports();
            updateDownloadLinks();
            fetchSalesDataForPieChart();

            document.getElementById('filterButton').addEventListener('click', fetchReportByDate);
            document.getElementById("startDate").addEventListener("change", updateDownloadLinks);
            document.getElementById("endDate").addEventListener("change", updateDownloadLinks);
        });

        function fetchAllReports() {
            $.ajax({
                url: "fetch_report.php",
                type: "GET",
                success: function(response) {
                    $("#reportData").html(response);
                },
                error: function() {
                    alert("Error loading all reports.");
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

            $.ajax({
                url: "fetch_report.php",
                type: "POST",
                data: { start_date: startDate, end_date: endDate },
                success: function(response) {
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

            updateDownloadLinks();
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

        function fetchSalesDataForPieChart() {
            $.ajax({
                url: "fetch_chart_data.php", // ✅ Correct filename
                type: "GET",
                dataType: "json",
                success: function(data) {
                    renderSalesPieChart(data);
                },
                error: function(xhr, status, error) {
                    console.error("Error loading pie chart data:", error);
                    alert("Failed to load pie chart data.");
                }
            });
        }
        function renderSalesPieChart(data) {
    const ctx = document.getElementById('salesPieChart').getContext('2d');

    const labels = data.map(item => item.salesperson);
    const values = data.map(item => item.total_orders);

    // Calculate total orders
    const totalOrders = values.reduce((a, b) => a + b, 0);

    // Add 'Total Orders' as an extra slice
    labels.push('Total Orders');
    values.push(totalOrders);

    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: labels,
            datasets: [{
                data: values,
                backgroundColor: [
                    ...labels.slice(0, -1).map((_, i) => i % 2 === 0 ? 'red' : 'yellow'),
                    'green'  // Green for 'Total Orders'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                datalabels: {
                    formatter: (value, context) => {
                        let total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                        let percentage = (value / total * 100).toFixed(1);
                        return percentage + '%';
                    },
                    color: '#000',
                    font: {
                        weight: 'bold',
                        size: 14
                    }
                },
                legend: {
                    position: 'bottom',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            let value = context.parsed;
                            let total = context.dataset.data.reduce((a, b) => a + b, 0);
                            let percentage = (value / total * 100).toFixed(1);
                            return `${label}: ${value} orders (${percentage}%)`;
                        }
                    }
                }
            }
        },
        plugins: [ChartDataLabels]
    });
}

    </script>
</body>
</html>
