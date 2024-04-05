<?php
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_privilege']) || ($_SESSION['user_privilege'] !== 'admin' && $_SESSION['user_privilege'] !== 'staff')) {
    header('Location: ./login/login.php');
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

// Fetch user data if userID is set
$user = null;
if ($userID) {
    $sql = "SELECT * FROM bookStore.userTable WHERE userID = '$userID'";
    $result = $conn->query($sql);
    if ($result) {
        $user = $result->fetch_assoc();
    }
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
        header("Location: ./../admin/management.php");
    } else {
        echo "Error updating user: " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit User</title>
    <?php include "./../inc/head.inc.php"; ?>
</head>
<body>
    <?php include "./../inc/nav.inc.php"; ?>

    <main class="container">
        <h1>Edit User</h1>
        <?php if ($user): ?>
            <form action="#" method="post">
                <input type="hidden" name="userID" value="<?php echo htmlspecialchars($user['userID']); ?>">

                <div class="mb-3">
                    <label for="fName" class="form-label">First Name:</label>
                    <input type="text" id="fName" name="fName" class="form-control" value="<?php echo htmlspecialchars($user['fName']); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="lName" class="form-label">Last Name:</label>
                    <input type="text" id="lName" name="lName" class="form-control" value="<?php echo htmlspecialchars($user['lName']); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email:</label>
                    <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="userPrivilege" class="form-label">User Privilege:</label>
                    <select id="userPrivilege" name="userPrivilege" class="form-select" required>
                        <option value="user" <?php echo ($user['userPrivilege'] == 'user') ? 'selected' : ''; ?>>User</option>
                        <option value="staff" <?php echo ($user['userPrivilege'] == 'staff') ? 'selected' : ''; ?>>Staff</option>
                    </select>
                </div>

                <button type="submit" name="update" class="btn btn-primary">Update User</button>
                <a href="./../admin/management.php" class="btn btn-secondary">Back</a>
            </form>
        <?php else: ?>
            <p>No user found with the provided ID.</p>
        <?php endif; ?>
    </main>

    <?php include "./../inc/footer.inc.php"; ?>
</body>
</html>
