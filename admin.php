<?php
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_privilege']) || $_SESSION['user_privilege'] !== 'admin' && $_SESSION['user_privilege'] !== 'staff') {
    // Redirect to login page if not logged in or not an admin
    header('Location: login.php');
    exit;
}

// Database configuration
$config; //parse_ini_file('/var/www/private/db-config.ini');

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

// Query to select all columns for users with user_privilege = 'user'
$sql = "SELECT * FROM bookStore.userTable WHERE userPrivilege != 'admin'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>BookStore - User Management</title>
    <?php
    include "inc/head.inc.php";
    ?>
</head>

<body>
    <?php
    include "inc/nav.inc.php";
    ?>
    <!-- <?php
            include "inc/header.inc.php";
            ?> -->
    <main class="container">
        <h1>User Management</h1>
        <div style="margin-top: 20px;">
            <a href="/register/register.php" class="btn btn-primary">Register New User</a>
        </div>
        <?php if ($result->num_rows > 0) : ?>
            <table>
                <thead>
                    <tr>
                        <th>Member ID</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>User Privilege</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()) : ?>
                        <tr>
                            <td><?php echo $row["userID"]; ?></td>
                            <td><?php echo $row["fName"]; ?></td>
                            <td><?php echo $row["lName"]; ?></td>
                            <td><?php echo $row["email"]; ?></td>
                            <td><?php echo $row["userPrivilege"]; ?></td>
                            <td>
                                <form action="process_edit.php" method="post">
                                    <input type="hidden" name="userID" value="<?php echo $row["userID"]; ?>">
                                    <input type="submit" value="Edit">
                                </form>
                            </td>
                            <td>
                                <form action="process_delete.php" method="post">
                                    <input type="hidden" name="userID" value="<?php echo $row["userID"]; ?>">
                                    <input type="submit" value="Delete">
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else : ?>
            <p>No user data found.</p>
        <?php endif; ?>

        <?php $conn->close(); ?>
    </main>
    <?php
    include "inc/footer.inc.php";
    ?>
</body>

</html>