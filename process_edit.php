<?php
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_privilege']) || $_SESSION['user_privilege'] !== 'admin' && $_SESSION['user_privilege'] !== 'staff') {
    header('Location: /login/login.php');
    exit;
}

// Database configuration
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

$userID = isset($_POST['userID']) ? $conn->real_escape_string($_POST['userID']) : null;

// Fetch user data if member_id is set
if ($userID) {
    $sql = "SELECT * FROM bookStore.userTable WHERE userID = '$userID'";
    $result = $conn->query($sql);
    $user = $result->fetch_assoc();
}

// Check if form has been submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    // Get form data
    $fName = $conn->real_escape_string($_POST['fName']);
    $lName = $conn->real_escape_string($_POST['lName']);
    $email = $conn->real_escape_string($_POST['email']);
    $userPrivilege = $conn->real_escape_string($_POST['userPrivilege']);

    // Update user data
    $updateSql = "UPDATE bookStore.userTable SET fName='$fName', lName='$lName', email='$email', userPrivilege='$userPrivilege' WHERE userID='$userID'";

    if ($conn->query($updateSql) === TRUE) {
        if ($_SESSION['user_privilege'] == 'admin') {
            echo "User updated successfully. <a href='admin.php'>Return to User Management</a>";
        } else {
            echo "User updated successfully. <a href='staff.php'>Return to User Management</a>";
        }
    } else {
        echo "Error updating user: " . $conn->error;
    }
} else {
    // Display form
    if (isset($user)) {
?>

        <!DOCTYPE html>
        <html lang="en">

        <head>
            <title>Edit User</title>
        </head>

        <body>
            <h1>Edit User</h1>
            <form action="" method="post">
                <input type="hidden" name="userID" value="<?php echo $user['userID']; ?>">
                <div>
                    <label>First Name:</label>
                    <input type="text" name="fName" value="<?php echo $user['fName']; ?>">
                </div>
                <div>
                    <label>Last Name:</label>
                    <input type="text" name="lName" value="<?php echo $user['lName']; ?>">
                </div>
                <div>
                    <label>Email:</label>
                    <input type="email" name="email" value="<?php echo $user['email']; ?>">
                </div>
                <div>
                    <label>User Privilege:</label>
                    <select name="userPrivilege">
                        <option value="user" <?php echo ($user['userPrivilege'] == 'user') ? 'selected' : ''; ?>>User</option>
                        <option value="staff" <?php echo ($user['userPrivilege'] == 'staff') ? 'selected' : ''; ?>>Staff</option>
                    </select>
                </div>
                <button type="submit" name="update">Update User</button>
                <button type="button" onclick="location.href='/admin.php'">Back</button>
            </form>
        </body>

        </html>
<?php
    } else {
        echo "No user found with the provided ID.";
    }
}
$conn->close();
?>