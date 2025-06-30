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
            <h1>Staff Cafeteria </h1>
            <nav>
                <a href="cashier_dashboard.php">Home</a>
                <!-- <a href="report.php">Report</a> -->
                <!-- <a href="settings.php">Settings</a> -->
                <!-- <a href="chart.php">Chart</a> -->
                <a href="statistics.php">order-history</a>
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>S</title>Staff Cafeteria
    <link rel="stylesheet" href="page.css">
    <script defer src="order.js"></script>
   
</head>
<body>
    <div class="wrapper">
        <header>
            <!-- <h1>Cash Register Pro</h1> -->
            <nav>
               
            </nav>
            
            <!-- <div class="user-info">HI! <?php echo strtoupper($_SESSION['username']); ?> | <a href="logout.php">LOGOUT</a></div> -->
        </header>
        <main>
            <section class="order-section">
                <table>
                    <thead class="Items">
                        <tr>
                            <th>Items</th>
                            <th>Price</th>
                            <th>Qty</th>
                            <th>Salesperson</th>
                            <th>Total Disc</th>
                            <th>Total</th>
                            <th>Del</th>
                        </tr>
                    </thead>
                    <tbody id="order-list">
                        <!-- Order items will be added dynamically -->
                    </tbody>
                </table>
                <div class="order-actions">
                    <button onclick="undo()">Undo</button>
                    <button onclick="clearAll()">Clear All</button>
                    <button onclick="applyDiscount()">Discount</button>
                    <button onclick="printReceipt()">Print or Send Email</button>
                    <button onclick="pay()">Pay</button>
                </div>
            </section>

            <section class="product-section">
                <div class="product-controls">
                    <!-- <span>
                        Sort by:
                        <select>
                            <option>Product Name</option>
                        </select> -->
                    </span>
                </div>
                <div class="product-list" id="product-list">
                    <!-- Products will be dynamically added here -->
                </div>
                <div class="category-buttons">
                    <button onclick="filterProducts('all')">All</button>
                    <button onclick="filterProducts('drinks')">Drinks</button>
                    <button onclick="filterProducts('food')">Food</button>
                    <button onclick="filterProducts('others')">Others</button>
                </div>
            </section>
        </main>
    </div>

    <div id="popup" class="popup">
        <div class="popup-content">
            <h3>Enter Details</h3>
            <label for="quantity">Quantity: </label>
            <input type="number" id="quantity" min="1" required>
            <!-- <label for="salesperson">Salesperson Name:</label> -->
            <!-- <input type="text" id="salesperson" required> -->
            <div class="popup-buttons">
                <button onclick="submitDetails()">Submit</button>
                <button onclick="closePopup()">Cancel</button>
            </div>
        </div>
    </div>

    <script>
        function filterProducts(category) {
            // Implement filtering logic
            console.log("Filtering products by category:", category);
        }
    </script>
</body>
</html>
