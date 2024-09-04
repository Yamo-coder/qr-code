<?php
require_once 'connection.php';
require_once 'phpqrcode/qrlib.php';

// Check if a POST request with scanned QR code data has been received
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Initialize the scanTime variable to an empty string
    $scanTime = '';

    // Get the scanned QR code data from the client
    $postData = json_decode(file_get_contents('php://input'));

    if ($postData && isset($postData->qrData)) {
        $qrtext = mysqli_real_escape_string($connection, $postData->qrData);

        // Insert the scanned QR code data into the database using prepared statements
        $stmt = mysqli_prepare($connection, "INSERT INTO qrcode (qrtext, qrimage) VALUES (?, '')");
        mysqli_stmt_bind_param($stmt, "s", $qrtext);
        $query = mysqli_stmt_execute($stmt);

        if ($query) {
            // Retrieve the scan time from the database
            $query = "SELECT scan_time FROM qrcode WHERE qrtext = '$qrtext'";
            $result = mysqli_query($connection, $query);

            if ($row = mysqli_fetch_assoc($result)) {
                $scanTime = $row['scan_time'];
            }

            // Send a success response to the client
            echo json_encode(['success' => true, 'scanTime' => $scanTime]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to insert data into the database.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid QR code data received.']);
    }
} else {
    // Handle other cases, e.g., displaying QR codes
    // ...
}
?>
