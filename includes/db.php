<?php
$servername = "localhost";
$username = "root";  // Default for MAMP
$password = "root";  // Default for MAMP
$dbname = "mythic_prints";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
