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
                <a href="admin_dashboard.php">Home</a>
                <a href="report.php">Report</a>
                <a href="settings.php">Settings</a>
                <a href="inventory.php">Inventory</a>
                <a href="statistics.php">Statistic</a>
                <a href="manage_product.php">Options</a>
                <a href="profile.php">Profile</a>
                <a href="admin_manage_products.php">Add Products</a>
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
