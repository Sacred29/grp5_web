<?php
session_start();
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
if ($conn->connect_error) {
    $_SESSION["user_privilege"] = "admin"; // Augment here
        $_SESSION["email"] = "admin@thedaniel.life";
        $_SESSION["fName"] = "admin";
        $_SESSION["lName"] = "admin";
        $_SESSION["userID"] = 1;
} else {
    $email = "admin@thedaniel.life"; // need to implement in db.
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
    }
    else {
        $_SESSION["user_privilege"] = "admin"; // Augment here
        $_SESSION["email"] = "admin@thedaniel.life";
        $_SESSION["fName"] = "admin";
        $_SESSION["lName"] = "admin";
        $_SESSION["userID"] = 3;
    }
}


    header("Location: /index.php");

?>