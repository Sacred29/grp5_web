<?php
session_start();
?>

<?php
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
//check connection
if ($conn->connect_error) {
    $errorMsg = "Connection failed: " . $conn->connect_error;
} else {
    $email = $_POST["email"];
    $stmt = $conn->prepare("SELECT * FROM userTable WHERE email=?");
    //bind and execute query statement
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        // fetch one value as email is unique.
        $row = $result->fetch_assoc();
        // Add session states
        $_SESSION["user_privilege"] = $row["userPrivilege"];
        $_SESSION["email"] = $row["email"];
        $_SESSION["fName"] = $row["fName"];
        $_SESSION["lName"] = $row["lName"];
        $_SESSION["userID"] = $row["userID"];

        if ($_SESSION["user_privilege"] == 'admin') {
            header("Location: ../admin/management.php");
            exit;
        } else if ($_SESSION["user_privilege"] == 'staff') {
            header("Location: ../admin/management.php");
            exit;
        } else {
            header("Location: ../index.php");
            exit;
        }
    } else {
        header("Location: /login.php");
        exit;
    }
}
?>