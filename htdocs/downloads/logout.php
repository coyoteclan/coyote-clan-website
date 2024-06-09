<?php

session_start();

session_destroy(); // Destroy the session

// You can perform additional cleanup tasks if needed

// Redirect to the login page (adjust the URL as needed)

header("Location: login/index.php");

exit;

?>

