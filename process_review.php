<?php
session_start();

// Check if the user is logged in, if not then redirect to login page
if (!isset($_SESSION['email'])) {
    header("location: login.php");
    exit;
}

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
        $config['servername'] ,
        $config['username'] , 
        $config['password'] , 
        $config['dbname'] 
    ); 

    if ($conn->connect_error) {
        $errorMsg = "Connection failed: " .$conn->connect_error;
        $success = false;
    }

// Process the review form when it is submitted
if (isset($_POST['submitReview']) && isset($_SESSION['userID'])) {
    $userReview = $_POST['userReview'];
    $productID = $_POST['productID'];
    $userRating = $_POST['userRating'];
    $userID = $_SESSION['userID'];
    //$userRating = 5; // Static value for now, you can change this to be dynamic

    $stmt = $conn->prepare("INSERT INTO reviewTable (productID, userID, userReview, userRating) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iisi", $productID, $userID, $userReview, $userRating);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Review added successfully";
    } else {
        $_SESSION['message'] = "Error adding review: " . $conn->error;
    }

    $stmt->close();
    $conn->close();

    // Redirect back to the product details page
    header('Location: productDetails.php?id=' . $productID);
    exit;
} else {
    // Redirect them to the homepage or display an error message
    header('Location: index.php');
    exit;
}
?>
