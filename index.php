<?php
// Include the database connection file
include 'connection.php';

session_start();

// Logout logic
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}

// Check if the user is not logged in, redirect to the login page
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

require_once 'connection.php';
require_once 'phpqrcode/qrlib.php';

$path = 'images/';
$qrcode = $path . time() . ".png";
$qrimage = time() . ".png";

if (isset($_POST['sbt-btn'])) {
    $qrtext = $_POST['qrtext'];
    $query = mysqli_query($connection, "INSERT INTO qrcode (qrtext, qrimage) VALUES ('$qrtext', '$qrimage')");
    if ($query) {
        echo '<script>alert("Data saved successfully");</script>';
    }
}

// Initialize the scanTime variable to an empty string
$scanTime = '';

if (isset($qrtext)) {
    // Generate QR code
    QRcode::png($qrtext, $qrcode, 'H', 4, 4);

    // Fetch the scan time from the database
    $qrtext = mysqli_real_escape_string($connection, $qrtext);
    $query = "SELECT scan_time FROM qrcode WHERE qrtext = '$qrtext'";
    $result = mysqli_query($connection, $query);
    if ($row = mysqli_fetch_assoc($result)) {
        $scanTime = $row['scan_time'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Generator</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #ecf0f5; /* Light blue background color */
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        h1 {
            color: #3498db; /* Blue heading color */
        }

        form {
            margin-top: 20px;
        }

        input[type="text"] {
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #3498db; /* Blue border for input fields */
            border-radius: 4px;
        }

        input[type="submit"] {
            background-color: #3498db; /* Blue submit button color */
            color: #fff;
            padding: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        img {
            margin-top: 20px;
        }

        #result {
            margin-top: 10px;
            color: #3498db; /* Blue text color */
        }

        iframe {
            margin-top: 20px;
            border: 1px solid #3498db; /* Blue border for iframe */
        }

        .logout-box {
            margin-top: auto;
            padding: 10px;
            border: 1px solid #e74c3c; /* Red border for logout box */
            border-radius: 4px;
            background-color: #f9ebec; /* Light red background color */
        }

        .logout-link {
            color: #e74c3c; /* Red color for logout link */
            text-decoration: none;
            cursor: pointer;
        }
    </style>
    <script>
        function onScanComplete(data) {
            // Handle the scanned QR code data here
            document.getElementById('result').textContent = 'Scanned: ' + data;
        }
    </script>
</head>
<body>
    <h1>QR Code Generator and Scanner</h1>

    <!-- QR code generator form -->
    <form method="post" action="index.php">
        <input type="text" name="qrtext" placeholder="Enter QR Code Text">
        <input type="submit" name="sbt-btn" value="Generate QR Code">
    </form>

    <!-- Include the QR code scanner using an iframe -->
    <iframe src="qrcode_scanner.html" width="640" height="480" frameborder="0"></iframe>

    <!-- Display the generated QR code and scan time -->
    <?php
    if (isset($qrtext)) {
        echo '<img src="' . $qrcode . '">';
        echo "Scan Time: $scanTime";
    }
    ?>

    <!-- Logout box -->
    <div class="logout-box">
        <a class="logout-link" href="?logout=true">Logout</a>
    </div>

    <div id="result"></div>
</body>
</html>
