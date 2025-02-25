<?php
include 'db_connect.php';

// Fetch total sales amount
$totalSalesQuery = "SELECT SUM(total) AS total_sales FROM sales";
$totalSalesResult = $conn->query($totalSalesQuery);
$totalSales = $totalSalesResult->fetch_assoc()['total_sales'];

// Fetch total number of products sold
$totalProductsSoldQuery = "SELECT SUM(quantity) AS total_products_sold FROM sales";
$totalProductsSoldResult = $conn->query($totalProductsSoldQuery);
$totalProductsSold = $totalProductsSoldResult->fetch_assoc()['total_products_sold'];

// Fetch sales by category
$salesByCategoryQuery = "
    SELECT pa.category, SUM(s.total) AS category_sales
    FROM sales s
    JOIN product_added pa ON s.product_name = pa.name
    GROUP BY pa.category
";

$salesByCategoryResult = $conn->query($salesByCategoryQuery);
$salesByCategory = [];
while ($row = $salesByCategoryResult->fetch_assoc()) {
    $salesByCategory[] = $row;
}

// Fetch sales by salesperson with their username
$salesBySalespersonQuery = "
    SELECT u.username AS salesperson_name, SUM(s.total) AS salesperson_sales
    FROM sales s
    JOIN users u ON s.salesperson = u.username
    GROUP BY u.username
";
$salesBySalespersonResult = $conn->query($salesBySalespersonQuery);
$salesBySalesperson = [];
while ($row = $salesBySalespersonResult->fetch_assoc()) {
    $salesBySalesperson[] = $row;
}

// Fetch daily sales data
$dailySalesQuery = "
    SELECT DATE(date) AS sale_date, SUM(total) AS daily_sales
    FROM sales
    GROUP BY sale_date
    ORDER BY sale_date
";
$dailySalesResult = $conn->query($dailySalesQuery);
$dailySales = [];
while ($row = $dailySalesResult->fetch_assoc()) {
    $dailySales[] = $row;
}

// Fetch monthly sales data
$monthlySalesQuery = "
    SELECT DATE_FORMAT(date, '%Y-%m') AS sale_month, SUM(total) AS monthly_sales
    FROM sales
    GROUP BY sale_month
    ORDER BY sale_month
";
$monthlySalesResult = $conn->query($monthlySalesQuery);
$monthlySales = [];
while ($row = $monthlySalesResult->fetch_assoc()) {
    $monthlySales[] = $row;
}

// Fetch yearly sales data
$yearlySalesQuery = "
    SELECT DATE_FORMAT(date, '%Y') AS sale_year, SUM(total) AS yearly_sales
    FROM sales
    GROUP BY sale_year
    ORDER BY sale_year
";
$yearlySalesResult = $conn->query($yearlySalesQuery);
$yearlySales = [];
while ($row = $yearlySalesResult->fetch_assoc()) {
    $yearlySales[] = $row;
}
?>
<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start the session only if none exists
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cash Register Pro</title>
    <script defer src="order.js"></script>
    <style>
        .wrapper {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
          
        /* Header */
        header {
            background-color: #6a4c93;
            color: white;
            padding: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-shrink: 0;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        nav a {
            color: white;
            text-decoration: none;
            margin: 0 8px;
            font-weight: bold;
            transition: color 0.3s;
        }
        nav a:hover {
            color: #ffcc00;
        }
    </style>
</head>
<body>
    
    <div class="wrapper">
        <header>
            <h1>Cash Register Pro</h1>
            <nav>
                <a href="cashier_dashboard.php">Home</a>
                <!-- <a href="report.php">Report</a> -->
                <!-- <a href="settings.php">Settings</a> -->
                <!-- <a href="chart.php">Chart</a> -->
                <a href="statistics.php">Statistic</a>
                <!-- <a href="manage_product.php">Options</a> -->
                <a href="profile.php">Profile</a>
                <!-- <a href="admin_manage_products.php">Add Products</a> -->
            </nav>
            <div id="google_translate_element"></div>

            <script type="text/javascript">
            function googleTranslateElementInit() {
                new google.translate.TranslateElement({pageLanguage: 'en'}, 'google_translate_element');
            }
            </script>

            <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

            <div class="user-info">
                HI! 
                <?php 
                if (isset($_SESSION['username'])) {
                    echo strtoupper($_SESSION['username']);
                } else {
                    echo "Guest"; // Default text if no session exists
                }
                ?> 
                | <a href="logout.php">LOGOUT</a>
            </div>
        </header>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Sales Statistics</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Sales Statistics</h1>

    <h2>Total Sales: <?php echo number_format($totalSales, 2); ?> ETB</h2>
    <h2>Total Products Sold: <?php echo $totalProductsSold; ?></h2>

    <h3>Sales by Category</h3>
    <table>
        <thead>
            <tr>
                <th>Category</th>
                <th>Total Sales (ETB)</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($salesByCategory as $category): ?>
                <tr>
                    <td><?php echo htmlspecialchars($category['category']); ?></td>
                    <td><?php echo number_format($category['category_sales'], 2); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h3>Sales by Salesperson</h3>
    <table>
        <thead>
            <tr>
                <th>Salesperson</th>
                <th>Total Sales (ETB)</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($salesBySalesperson as $salesperson): ?>
                <tr>
                    <td><?php echo htmlspecialchars($salesperson['salesperson_name']); ?></td>
                    <td><?php echo number_format($salesperson['salesperson_sales'], 2); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h3>Daily Sales</h3>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Total Sales (ETB)</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($dailySales as $day): ?>
                <tr>
                    <td><?php echo htmlspecialchars($day['sale_date']); ?></td>
                    <td><?php echo number_format($day['daily_sales'], 2); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h3>Monthly Sales</h3>
    <table>
        <thead>
            <tr>
                <th>Month</th>
                <th>Total Sales (ETB)</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($monthlySales as $month): ?>
                <tr>
                    <td><?php echo htmlspecialchars($month['sale_month']); ?></td>
                    <td><?php echo number_format($month['monthly_sales'], 2); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h3>Yearly Sales</h3>
    <table>
        <thead>
            <tr>
                <th>Year</th>
                <th>Total Sales (ETB)</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($yearlySales as $year): ?>
                <tr>
                    <td><?php echo htmlspecialchars($year['sale_year']); ?></td>
                    <td><?php echo number_format($year['yearly_sales'], 2); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
