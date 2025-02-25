<?php
$servername = "localhost:3308";
$username = "root";  // Change if using a different user
$password = "";      // Set your database password if required
$dbname = "cafeteria_pos";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
