<?php
$servername = "localhost";
$username = "root"; // Use your database username
$password = ""; // Use your database password
$dbname = "vmsdb"; // Use your database name

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);  // Displays connection error if any
}
?>