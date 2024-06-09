<?php
session_start();

// Check if the user is logged in
if (isset($_SESSION['username'])) {
    header("Location: ../index.html"); // Redirect to unauthorized page
    exit();
}

// Include your database connection
include '../db_connection.php';

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Hash the password
    $email = $_POST['email'];
    $ip_address = $_SERVER['REMOTE_ADDR']; // Get the user's IP address

    // Check if the email is already registered
    $sql_check_email = "SELECT * FROM users WHERE email='$email'";
    $result_email = $conn->query($sql_check_email);

    if ($result_email->num_rows > 0) {
        $error_message = "Email is already registered.";
    } else {
        // Check if the username is already taken
        $sql_check_username = "SELECT * FROM users WHERE username='$username'";
        $result_username = $conn->query($sql_check_username);

        if ($result_username->num_rows > 0) {
            $error_message = "Username is already taken.";
        } else {
            // Check if the user's IP address is associated with more than two accounts
            $sql_check_ip = "SELECT COUNT(*) AS count FROM users WHERE ip_address='$ip_address'";
            $result = $conn->query($sql_check_ip);
            $row = $result->fetch_assoc();
            if ($row['count'] >= 2) {
                $error_message = "You are not allowed to register more than two accounts.";
            } else {
                // Check if the email domain is from a trusted provider
                $allowed_domains = ['gmail.com', 'yahoo.com', 'outlook.com']; // Add more trusted domains if needed
                $email_domain = explode('@', $email)[1];
                if (!in_array($email_domain, $allowed_domains)) {
                    $error_message = "Only registration from trusted email providers is allowed.";
                } else {
                    // Proceed with registration
                    $sql = "INSERT INTO users (username, password, email, ip_address) VALUES ('$username', '$hashed_password', '$email', '$ip_address')";

                    if ($conn->query($sql) === TRUE) {
                        $_SESSION['username'] = $username;
                        $_SESSION['admin'] = 0;
                        header("Location: ../index.html");
                        exit();
                    } else {
                        $error_message = "Error: " . $sql . "<br>" . $conn->error;
                    }
                }
            }
        }
    }
    
    // Store the error message in the session
    if (!empty($error_message)) {
        $_SESSION['error_message'] = $error_message;
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Get the error message from the session and then clear it
if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Registration</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30;
            padding: 0;
            background-color: #1f2b0d;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            width: 80%;
            padding: 20px;
            color: #b4d934;
            background-color: #7a58a4;
            border: 1px solid #b4d934;
            border-radius: 10px;
            font-weight: bold;
            box-sizing: border-box;
            margin-top: 40px auto;
        }
        h2 {
            text-align: center;
        }
        p {
            font-size: 10px;
        }
        a {
            font-size: 10px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #b4d934;
            border-radius: 4px;
            box-sizing: border-box;
        }
        input[type="submit"] {
            background-color: #b4d934;
            color: white;
            padding: 10px 20px;
            text-align: center;
            display: inline-block;
            text-decoration: none;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
            border-radius: 8px;
        }
        input[type="submit"]:hover {
            border: 4px #7a58a4;
        }
        .popup {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            padding: 20px;
            color: #b4d934;
            background-color: #7a58a4;
            border: 1px solid #b4d934;
            border-radius: 10px;
            box-sizing: border-box;
            font-weight: bold;
            font-size: 24px;
            display: none; /* Hidden by default */
            z-index: 1000; /* Ensure it appears above other content */
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>User Registration</h2>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required><br>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required><br>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required><br>

            <input type="submit" value="Register">
        </form>
    <p>Already have an account? <a href="../login/index.php">Login here</a></p>
    </div>

    <div class="popup" id="errorPopup"><?php echo $error_message; ?></div>

    <script>
        function showPopup() {
            const popup = document.getElementById('errorPopup');
            if (popup.innerText.trim() !== "") {
                popup.style.display = 'block';
                setTimeout(() => {
                    popup.style.display = 'none';
                }, 3000);
            }
        }

        // Show the popup if there's an error message
        document.addEventListener('DOMContentLoaded', showPopup);
    </script>
</body>
</html>
