<?php
// Include your database connection
include 'db_connection.php';

// Get the selected options from the dropdown menus
$order_by = isset($_GET['order_by']) ? $_GET['order_by'] : 'id';
$sort_order = isset($_GET['sort_order']) ? $_GET['sort_order'] : 'desc';

// Create connection
//$conn = new mysqli($servername, $username, $password, $dbname);

//if ($conn->connect_error) {
//    die("Connection failed: " . $conn->connect_error);
//}

// Prevent SQL injection by validating the order_by parameter
$allowedColumns = ['id', 'name', 'author', 'category']; // Add more columns as needed
$order_by = in_array($order_by, $allowedColumns) ? $order_by : 'id';

$sql = "SELECT * FROM modlist WHERE approved = 1 ORDER BY $order_by $sort_order";
$result = $conn->query($sql);


if ($result->num_rows > 0) {
    $mods = array();

    while ($row = $result->fetch_assoc()) {
        $mods[] = $row;
    }

    // Output mods as JSON
    echo json_encode($mods);
} else {
    // Output an empty array if no mods are found
    echo json_encode(array());
}

$conn->close();
?>