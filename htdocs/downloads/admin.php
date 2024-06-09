<?php
session_start();

// Check if the user is logged in and has admin privileges
if (!isset($_SESSION['username']) || $_SESSION['admin'] != 1) {
    header("Location: unauthorized.php"); // Redirect to unauthorized page
    exit();
}
error_reporting(E_ALL);
ini_set('display_errors', 1);
// Include your database connection
include 'db_connection.php';

// Check if 'modId' is set in the POST data (when approving a mod)
if (isset($_POST['modId'])) {
    // Get the mod ID from the POST request
    $modId = $_POST['modId'];

    // Validate and sanitize the modId (you might need to adjust this based on your needs)
    $modId = filter_var($modId, FILTER_VALIDATE_INT);
    if ($modId === false || $modId <= 0) {
        $approvalError = 'Invalid modId';
    } else {
        // Update the 'approved' status in the database
        $sql = "UPDATE modlist SET approved = 1 WHERE id = $modId";
        if ($conn->query($sql) === TRUE) {
            // Success message, if needed
            $approvalSuccess = true;
        } else {
            // Error message if the query fails
            $approvalError = $conn->error;
        }
    }
}

// Fetch mods that are not approved
$sql = "SELECT * FROM modlist WHERE approved = 0";
$result = $conn->query($sql);

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Page</title>
    <!-- Add your styles and scripts here -->
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <script>
        // Function to show the popup
        function showPopup(popupId) {
            // Show the popup
            document.getElementById(popupId).style.display = 'block';
            
            // Hide the popup after 3 seconds
            setTimeout(function() {
                document.getElementById(popupId).style.display = 'none';
            }, 3000);
        }
    </script>

    <div class="container">
        <h1>Welcome to The Admin Page!</h1>

        <?php if (isset($approvalSuccess) && $approvalSuccess): ?>
            <div class="popup" id="approvalPopup">Mod approved successfully!</div>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    showPopup('approvalPopup');
                });
            </script>
        <?php elseif (isset($approvalError) && $approvalError): ?>
            <div class="error-message"><?php echo $approvalError; ?></div>
        <?php endif; ?>

        <?php
        // Display the list of mods
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<div class="mod">';
                echo '<h2>' . $row['name'] . '</h2>';
                echo '<p>' . $row['description'] . '</p>';
                echo '<img src="' . $row['image_url'] . '" class="mod-image">';
                echo '<a href="' . $row['download_link'] . '">Download</a>';
                echo '<form method="POST">';
                echo '<input type="hidden" name="modId" value="' . $row['id'] . '">';
                echo '<button type="submit" class="category-button">Approve</button>';
                echo '</form>';
                echo '</div>';
            }
        } else {
            echo '<div class="popup" id="nomods">No mods pending for approval.</div>';
            echo '<script type="text/javascript">showPopup("nomods");</script>';
        }
        ?>
	</div>
</body>
</html>
