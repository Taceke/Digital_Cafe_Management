<?php
session_start();
include 'db_connect.php';

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['username'];
$user_role = $_SESSION['role'] ?? 'Cashier'; // Default role if not set

// Fetch user profile details
$query = "SELECT * FROM user_profiles WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $user_id); // Use $user_id instead of $username
$stmt->execute();
$result = $stmt->get_result();
$profile = $result->fetch_assoc();

// Handle cases where no profile exists
$profile_image = !empty($profile['profile_image']) ? $profile['profile_image'] : 'default.jpg';
$first_name = $profile['first_name'] ?? '';
$last_name = $profile['last_name'] ?? '';
$gender = $profile['gender'] ?? '';
$phone_number = $profile['phone_number'] ?? '';
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
    <title>Profile</title>
    <link rel="stylesheet" href="profile.css">
    <script src="profile.js" defer></script>
</head>
<body>
    <div class="profile-container">
        <div class="profile-header" style="background-image: url('uploads/<?php echo htmlspecialchars($profile_image); ?>');">
            <h2>Hi, this is <?php echo ucfirst(htmlspecialchars($user_role)); ?> Profile</h2>
        </div>
        <div class="profile-form">
            <?php if (isset($_GET['success'])): ?>
                <p class="success-message"><?php echo htmlspecialchars($_GET['success']); ?></p>
            <?php endif; ?>
            <?php if (isset($_GET['error'])): ?>
                <p class="error-message"><?php echo htmlspecialchars($_GET['error']); ?></p>
            <?php endif; ?>
            <form action="update_profile.php" method="POST" enctype="multipart/form-data">
                <label>Profile Image</label>
                <input type="file" name="profile_image" id="profileImageInput">
                <img id="previewImage" src="uploads/<?php echo htmlspecialchars($profile_image); ?>" alt="Profile Image">

                <label>First Name</label>
                <input type="text" name="first_name" value="<?php echo htmlspecialchars($first_name); ?>" required>

                <label>Last Name</label>
                <input type="text" name="last_name" value="<?php echo htmlspecialchars($last_name); ?>" required>

                <label>Gender</label>
                <select name="gender" required>
                    <option value="Male" <?php if ($gender == 'Male') echo 'selected'; ?>>Male</option>
                    <option value="Female" <?php if ($gender == 'Female') echo 'selected'; ?>>Female</option>
                </select>

                <label>Phone Number</label>
                <input type="text" name="phone_number" value="<?php echo htmlspecialchars($phone_number); ?>" required>

                <input type="hidden" name="username" value="<?php echo htmlspecialchars($user_id); ?>"> <!-- Corrected here -->
                <button type="submit" name="update_profile">Update Profile</button>
            </form>
        </div>
    </div>
</body>
</html>
