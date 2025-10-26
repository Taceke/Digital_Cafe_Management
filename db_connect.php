<?php
// Use environment variables if they exist (Render will provide them)
$servername = getenv("DB_HOST") ?: "localhost";
$username   = getenv("DB_USER") ?: "root";
$password   = getenv("DB_PASS") ?: "";
$dbname     = getenv("DB_NAME") ?: "cafeteria_pos";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
