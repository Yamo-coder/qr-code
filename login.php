<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include the database connection file
include 'connection.php';

session_start();

// Database connection parameters
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "uni_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$errorMessage = ""; // Initialize error message variable

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['uname'];
    $password = $_POST['psw'];

    $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Create sessions
        $_SESSION['user'] = $row['username'];
        $_SESSION['usertype'] = $row['usertype'];
        $_SESSION['fullname'] = $row['fullname'];

        // Redirect to index page
        header('location:index.php');
        exit();
    } else {
        $errorMessage = 'Wrong Username/Password';
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            padding: 0;
            background: url('images/logincool.jpg') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        form {
            border: 3px solid #f1f1f1;
            max-width: 300px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.7);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        }

        input[type=text], input[type=password] {
            width: 100%;
            padding: 12px 20px;
            margin: 8px 0;
            display: inline-block;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }

        button {
            background-color: #3498db; /* Blue button color */
            color: white;
            padding: 14px 20px;
            margin: 8px 0;
            border: none;
            cursor: pointer;
            width: 100%;
        }

        button:hover {
            opacity: 0.8;
        }

        .container {
            padding: 16px;
            text-align: center;
        }

        .error-message {
            color: red;
            margin-top: 8px;
            text-align: center;
        }

        h2 {
            margin-bottom: 10px;
            color: white;
            margin-top: 0; /* Added to move it to the top */
        }
    </style>
</head>
<body>

    <form action="login.php" method="post">
        <div class="container">
            <h2>Login Account</h2> <!-- Moved the h2 above the container -->
            <label for="uname"><b>Username</b></label>
            <input type="text" placeholder="Enter Username" name="uname" required>

            <label for="psw"><b>Password</b></label>
            <input type="password" placeholder="Enter Password" name="psw" required>

            <button type="submit">Login</button>

            <p class="error-message"><?php echo $errorMessage; ?></p>
        </div>
    </form>

</body>
</html>
