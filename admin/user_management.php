<!DOCTYPE html>
<html lang="en">
<?php
session_start();

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

$userPrivilege = $_SESSION['user_privilege'] ?? ''; // Assuming this is stored in the session on login

if ($userPrivilege == 'admin') {
    // For admin, fetch all non-admin users
    $sql = "SELECT * FROM bookStore.userTable WHERE userPrivilege != 'admin'";
} elseif ($userPrivilege == 'staff') {
    // For staff, fetch only user-level users
    $sql = "SELECT * FROM bookStore.userTable WHERE userPrivilege = 'user'";
} else {
    // If it's neither, perhaps you don't want to show any data or handle this differently
    $sql = ""; // No SQL query, or a query that returns nothing/an error
}
    if($sql){
        $result = $conn->query($sql);
    }
?>

  
<div class="container">
    
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
                                <form action="/user/user_edit.php" method="post">
                                    <input type="hidden" name="userID" value="<?php echo $row["userID"]; ?>">
                                    <input type="submit" value="Edit">
                                </form>
                            </td>
                            <td>
                                <form action="/user/user_delete.php" method="post">
                                    <input type="hidden" name="userID" value="<?php echo $row["userID"]; ?>">
                                    <input type="submit" value="Delete" onclick="return confirm('Are you sure you want to delete this user?');">
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
</div>
</html>  