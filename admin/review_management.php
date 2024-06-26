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

// Query to select all columns for users with user_privilege = 'user'
$sql = "
SELECT rev.reviewID, user.userID, user.email, rev.productID, rev.userReview, rev.userRating
from bookStore.userTable as user
inner join bookStore.reviewTable  as rev on user.userID = rev.userID
";
$result = $conn->query($sql);
?>

  
<div class="container">
        <?php if ($result->num_rows > 0) : ?>
            <table>
                <thead>
                    <tr>
                        <th>Review ID</th>
                        <th>Member ID</th>
                        <th>Email</th>                        
                        <th>Product ID</th>
                        <th>Review</th>
                        <th>Rating</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()) : ?>
                        <tr>
                            <td><?php echo $row["reviewID"]; ?></td>
                            <td><?php echo $row["userID"]; ?></td>
                            <td><?php echo $row["email"]; ?></td>
                            <td><?php echo $row["productID"]; ?></td>
                            <td><?php echo $row["userReview"]; ?></td>
                            <td><?php echo $row["userRating"]; ?></td>
                            <td>
                                <form action="/review/review_update.php" method="post">
                                    <input type="hidden" name="reviewID" value="<?php echo $row["reviewID"]; ?>">
                                    <input type="submit" value="Edit">
                                </form>
                            </td>
                            <td>
                                <form action="/review/process_deleteReview.php" method="post">
                                    <input type="hidden" name="reviewID" value="<?php echo $row["reviewID"]; ?>">
                                    <input type="submit" value="Delete" onclick="return confirm('Are you sure you want to delete this review?');">
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
