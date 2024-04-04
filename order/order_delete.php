<?php

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['transactionID']) && isset($_POST["productID"])) {
    $transactionID = $_POST['transactionID'];
    $productID = $_POST["productID"];

    $config_file = '/var/www/private/db-config.ini';
    if (file_exists($config_file)) {
        // Parse the INI file

        $config = parse_ini_file($config_file);
    } else {
        // Get configuration from environment variables
        $config['servername'] = getenv('SERVERNAME');
        $config['username'] = getenv('DB_USERNAME');
        $config['password'] = getenv('DB_PASSWORD');
        $config['dbname'] = getenv('DBNAME');
    }

    $conn = new mysqli(
        $config['servername'],
        $config['username'],
        $config['password'],
        $config['dbname']
    );

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare and bind
    $stmt = $conn->prepare("DELETE FROM bookStore.orderTable WHERE transactionID = ? and productID = ?");
    $stmt->bind_param("ii", $transactionID, $productID);

    if ($stmt->execute()) {
        echo "Record deleted successfully";
    } else {
        echo "Error deleting record: " . $conn->error;
    }

    $stmt->close();
    $conn->close();

    // Redirect back to the admin page or inform the user
    header('Location: /admin/management.php');
    exit;
} else {
    // Redirect them to admin page or show an error
    echo "Invalid request.";
}
