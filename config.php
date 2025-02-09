<?php
$host = "127.0.0.1";
$username = "root"; // Your database username
$password = ""; // Your database password
$database = "btg_invoice_db"; // Your database name

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>