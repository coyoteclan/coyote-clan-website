<?php
// Replace with your database credentials
$servername = "";// Replace with your MySQL hostname
$username = ""; // Replace with your MySQL username
$password = "";// Replace with your MySQL password
$dbname = ""; // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
