<?php
session_start();

// Check if the user is logged in
if (isset($_SESSION['username'])) {
    header("Location: ../index.html"); // Redirect to unauthorized page
    exit();
}

// Include your database connection
include '../db_connection.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT id, password, admin FROM users WHERE username='$username'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $hashed_password = $row['password'];
        $admin = $row['admin'];

        if (password_verify($password, $hashed_password)) {
            // Password is correct
            $_SESSION['username'] = $username;
            $_SESSION['admin'] = $admin;
            $_SESSION['success_message'] = "Login successful. Welcome, " . htmlspecialchars($username) . "!";
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            $message = "Invalid password.";
        }
    } else {
        $message = "Invalid username.";
    }

    if (!empty($message)) {
        $_SESSION['error_message'] = $message;
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Get the messages from the session and then clear them
if (isset($_SESSION['error_message'])) {
    $message = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
} elseif (isset($_SESSION['success_message'])) {
    $message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Login</title>
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
        <h2>User Login</h2>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required><br>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required><br>

            <input type="submit" value="Login">
        </form>
        <p>No Account? <a href="../register/index.php">Register here</a></p>
    </div>

    <div class="popup" id="messagePopup"><?php echo $message; ?></div>

    <script>
        function showPopup() {
            const popup = document.getElementById('messagePopup');
            if (popup.innerText.trim() !== "") {
                popup.style.display = 'block';
                setTimeout(() => {
                    popup.style.display = 'none';
                    <?php if (isset($_SESSION['username'])): ?>
                        window.location.href = '../index.html';
                    <?php else: ?>
                        window.location.href = '<?php echo $_SERVER['PHP_SELF']; ?>';
                    <?php endif; ?>
                }, 3000);
            }
        }

        // Show the popup if there's a message
        document.addEventListener('DOMContentLoaded', showPopup);
    </script>
</body>
</html>
