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
    <title>Staff cafeteria</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

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
        .card {
    transition: transform 0.2s ease-in-out;
  }
  .card:hover {
    transform: scale(1.02);
  }

   /* Existing styles for totals */
   .stat-card {
    border-radius: 12px;
    padding: 25px 20px;
    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
    color: white;
  }

  .total-sales {
    background: linear-gradient(135deg, #4a90e2, #357ABD);
  }

  .total-products {
    background: linear-gradient(135deg, #f2994a, #f2c94c);
  }

  .stat-card h5 {
    font-weight: 600;
    letter-spacing: 1px;
    margin-bottom: 12px;
    text-transform: uppercase;
    opacity: 0.85;
  }

  .stat-card h2 {
    font-size: 2.8rem;
    font-weight: 700;
    letter-spacing: 0.02em;
  }

  /* New styles for Sales by Category section */
  h3 {
    font-weight: 700;
    color: #333;
    margin-bottom: 20px;
    border-bottom: 3px solid #4a90e2;
    padding-bottom: 8px;
    letter-spacing: 1.2px;
  }

  .table-responsive {
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
  }

  table {
    background: white;
    border-collapse: separate;
    border-spacing: 0 10px; /* Adds vertical spacing between rows */
    width: 100%;
  }

  thead tr {
    background: #4a90e2;
    color: white;
    text-transform: uppercase;
    font-weight: 700;
    letter-spacing: 1.2px;
  }

  tbody tr {
    background: #f9f9f9;
    transition: background-color 0.3s ease;
    border-radius: 10px;
  }

  tbody tr:hover {
    background-color: #d6e5fd;
  }

  tbody td {
    padding: 15px 20px;
    font-weight: 600;
    color: #555;
    border-bottom: none !important;
  }
    </style>
</head>
<body>
    
    <div class="wrapper">
        <header>
            <h1>Staff Cafeteria</h1>
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
<style>
  .stat-card {
    border-radius: 12px;
    padding: 25px 20px;
    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
    color: white;
  }

  .total-sales {
    background: linear-gradient(135deg, #4a90e2, #357ABD); /* Bright blue */
  }

  .total-products {
    background: linear-gradient(135deg, #f2994a, #f2c94c); /* Warm orange-yellow */
  }

  .stat-card h5 {
    font-weight: 600;
    letter-spacing: 1px;
    margin-bottom: 12px;
    text-transform: uppercase;
    opacity: 0.85;
  }

  .stat-card h2 {
    font-size: 2.8rem;
    font-weight: 700;
    letter-spacing: 0.02em;
  }
  h1.fw-bold.text-dark.display-5 {
  background: linear-gradient(135deg, #4a90e2 0%, #357ABD 100%);
  color: white;
  padding: 20px 30px;
  border-radius: 12px;
  box-shadow: 0 6px 12px rgba(54, 112, 183, 0.5);
  display: inline-block;
  user-select: none;
  letter-spacing: 2px;
}

</style>

<div class="container mt-5">

  <div class="text-center mb-5">
    <h1 class="fw-bold text-dark display-5" >📊 Sales Statistics</h1>
  </div>

  <div class="row text-center mb-4">
    <!-- Total Sales -->
    <div class="col-md-6 mb-3">
      <div class="stat-card total-sales">
        <h5>Total Sales</h5>
        <h2>
          <?php echo number_format($totalSales, 2); ?> ETB
        </h2>
      </div>
    </div>

    <!-- Total Products Sold -->
    <div class="col-md-6 mb-3">
      <div class="stat-card total-products">
        <h5>Total Products Sold</h5>
        <h2>
          <?php echo $totalProductsSold; ?>
        </h2>
      </div>
    </div>
  </div>

  <div class="mb-4">
    <h3 class="text-dark mb-3 border-bottom pb-2">Sales by Category</h3>
    <div class="table-responsive">
      <table class="table table-striped table-hover table-bordered align-middle">
        <thead class="table-primary">
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
    </div>
  </div>

</div>

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
