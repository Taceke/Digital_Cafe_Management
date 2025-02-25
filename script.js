
$(document).ready(function(){
    fetchReport();
});

function fetchReport() {
    $.ajax({
        url: 'fetch_report.php',
        method: 'GET',
        success: function(response) {
            $('#reportData').html(response);
        }
    });
}

function printReport() {
    window.print();
}

function fetchReportByDate() {
    let startDate = $('#startDate').val();
    let endDate = $('#endDate').val();

    if (!startDate || !endDate) {
        alert("Please select both start and end dates.");
        return;
    }

    $.ajax({
        url: 'fetch_report.php',
        method: 'GET',
        data: { start_date: startDate, end_date: endDate },
        success: function(response) {
            $('#reportData').html(response);
        }
    });
}
function downloadPDF() {
    let startDate = $('#startDate').val();
    let endDate = $('#endDate').val();
    let url = 'generate_pdf.php';

    if (startDate && endDate) {
        url += `?start_date=${startDate}&end_date=${endDate}`;
    }

    window.open(url, '_blank');
}

//asdfghjklCHARTS
document.addEventListener("DOMContentLoaded", function () {
    fetchChartData();
});

function fetchChartData() {
    fetch('fetch_chart_data.php')
        .then(response => response.json())
        .then(data => {
            createPieChart(data.sales, data.orders, data.salespersons);
        })
        .catch(error => console.error("Error fetching chart data:", error));
}

function createPieChart(sales, orders, salespersons) {
    const ctx = document.getElementById('salesChart').getContext('2d');
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Total Sales', 'Total Orders', 'Total Salespersons'],
            datasets: [{
                data: [sales, orders, salespersons],
                backgroundColor: ['#4CAF50', '#FFC107', '#FF5733']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
}
