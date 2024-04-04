<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("location: /login/login.php");
    exit;
}

if ($_SESSION['userPrivilege'] != 'staff' && $_SESSION['userPrivilege'] != 'admin') {
    header("location: /index.php"); 
}

$config_file = '/var/www/private/db-config.ini';
if (file_exists($config_file)) {
    $config = parse_ini_file($config_file);
} else {
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

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Process delete operation after confirmation
if (isset($_POST['reviewID'])) {
    $reviewID = $_POST['reviewID'];
    
    // Prepare a delete statement
    $stmt = $conn->prepare("DELETE FROM bookStore.reviewTable WHERE reviewID = ?");
    $stmt->bind_param("i", $reviewID);

    // Attempt to execute the prepared statement
    if ($stmt->execute()) {
    if ($conn->affected_rows > 0) {
        $_SESSION['message'] = "Review deleted successfully.";
        } else {
            $_SESSION['message'] = "No review found with the specified ID or deletion was not needed.";
        }
    } else {
        $_SESSION['message'] = "Error deleting review: " . $conn->error;
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
} else {
    $_SESSION['message'] = "No review ID provided for deletion.";
}

// Redirect to product details page, or wherever you would like the user to go after deletion
$productID = $_POST['productID'] ?? 'defaultProductID';
header('Location: /productDetails.php?id=' . $productID);
exit;
?>