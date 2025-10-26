<?php
$servername = getenv("DB_HOST") ?: "localhost";
$username   = getenv("DB_USER") ?: "root";
$password   = getenv("DB_PASS") ?: "";
$dbname     = getenv("DB_NAME") ?: "cafeteria_pos";
$port       = getenv("DB_PORT") ?: "3308";

$conn = new mysqli($servername, $username, $password, $dbname, $port);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
