<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login/index.php");
    exit;
}

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include your database connection
include 'db_connection.php';

$mod_uploaded = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $mod_name = $_POST['mod_name'];
    $mod_author = $_SESSION['username'];
    $mod_description = $_POST['mod_description'];
    $mod_category = $_POST['mod_category'];
    $image_url = $_POST['image_url'];
    $mod_file = $_FILES["mod_file"];
    //$mod_link = $_POST['mod_link'];
    $user_ip = $_SERVER['REMOTE_ADDR']; // Get the user's IP address

    // Check if file upload is successful
    if ($mod_file["error"] !== UPLOAD_ERR_OK) {
        echo "Error uploading mod file.";
        exit;
    }

    // Get file extension
    $file_ext = pathinfo($mod_file["name"], PATHINFO_EXTENSION);

    // Validate file extension (optional)
    $allowed_extensions = ["zip", "rar"]; // Allowed extensions
    if (!in_array($file_ext, $allowed_extensions)) {
        echo "Invalid file format.";
        exit;
    }

    // Create a unique filename
    //$unique_filename = uniqid() . "." . $file_ext;
    $unique_filename = strtolower($mod_file["name"]);
    $unique_filename = str_replace(" ", "-", $unique_filename);

    // Construct the target file path
    $target_file = "./files/" . $mod_category . "/" . $unique_filename;

    // Move the uploaded file to the target directory
    if (move_uploaded_file($mod_file["tmp_name"], $target_file)) {
        $mod_link = $target_file;
    } else {
        echo "Error uploading mod file.";
        exit;
    }

    // Check if the user has a pending mod
    $sql_check_pending = "SELECT COUNT(*) AS count FROM modlist WHERE user_ip='$user_ip' AND approved=0";
    $result = $conn->query($sql_check_pending);
    $row = $result->fetch_assoc();
    if ($row['count'] > 0) {
        echo "You already have a pending mod. Please wait for approval.";
        exit;
    }

    // Proceed with mod submission
    $sql = "INSERT INTO modlist (name, author, description, category, image_url, download_link, user_ip) VALUES ('$mod_name', '$mod_author', '$mod_description', '$mod_category', '$image_url', '$mod_link', '$user_ip')";

    if ($conn->query($sql) === TRUE) {
        $mod_uploaded = true;
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Submit Mod</title>
    <link rel="stylesheet" type="text/css" href="submit_mod.css">
</head>
<body>
    <div class="container">
        <h2>Submit a Mod</h2>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
            <label for="mod_name">Mod Name:</label>
            <input type="text" id="mod_name" name="mod_name" required><br>

            <label for="mod_description">Mod Description:</label>
            <textarea id="mod_description" name="mod_description" rows="4" required></textarea><br>

            <label for="mod_category">Mod Category:</label>
            <select id="mod_category" name="mod_category" required>
                <option value="weapons">Weapons</option>
                <option value="maps">Maps</option>
                <option value="skins">Skins</option>
                <option value="addons">Addons</option>
                <option value="misc">Misc</option>
                <option value="server">Server</option>
                <option value="prefabs">Prefabs</option>
            </select><br>

            <!--<label for="mod_link">Mod Link:</label>
            <input type="hidden" id="mod_link" name="mod_link" required><br> -->

            <label for="image_url">Image Link:</label>
            <p>You can upload at <a href="https://postimages.org/" target="_blank">postimages.org</a> and copy the direct link.</p>
            <input type="text" id="image_url" name="image_url" required><br>
            
            <label for="mod_file">Mod File:</label>
            <input type="file" id="mod_file" name="mod_file" required>

            <div class="all-done">All done?</div>
            <input type="submit" value="Submit Mod">
        </form>
    </div>

    <?php if ($mod_uploaded): ?>
    <div class="popup" id="popup">
        Mod submitted successfully. It will be reviewed by an admin.
    </div>
    <script>
        // Show the popup
        document.getElementById('popup').style.display = 'block';
        
        // Hide the popup after 5 seconds
        setTimeout(function() {
            //document.getElementById('popup').style.display = 'none';
            window.location.href = 'index.html';
        }, 5000);
    </script>
    <?php endif; ?>
</body>
</html>
