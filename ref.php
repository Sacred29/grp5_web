@ -1,45 +1,59 @@
<?php
// ini_set('display_errors', 1);
// error_reporting(E_ALL);

session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_privilege']) || $_SESSION['user_privilege'] !== 'admin' && $_SESSION['user_privilege'] !== 'staff') {
    // Redirect to login page if not logged in or not an admin
    header('Location: login.php');
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['userID'])) {
    $memberId = $_POST['userID'];

    // Database configuration
    $config = true; //parse_ini_file('/var/www/private/db-config.ini');
    $conn = new mysqli('35.212.243.22', 'inf1005-sqldev', 'p1_5', 'bookStore');
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
    $stmt = $conn->prepare("DELETE FROM bookStore.userTable WHERE userID = ?");
    $stmt->bind_param("i", $memberId);

    if ($stmt->execute()) {
        echo "Record deleted successfully";
    } else {
        echo "Error deleting record: " . $conn->error;
    }

    $stmt->close();
    $conn->close();

    // Redirect back to the admin page or inform the user
    header('Location: admin.php');
    exit;
} else {
    // Redirect them to admin page or show an error
    echo "Invalid request.";
}
?>