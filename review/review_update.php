<?php
session_start();

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

$reviewID = isset($_POST['reviewID']) ? $conn->real_escape_string($_POST['reviewID']) : null;

// Fetch user data if member_id is set
if ($reviewID) {
    $sql = "SELECT * FROM bookStore.reviewTable WHERE reviewID = '$reviewID'";
    $result = $conn->query($sql);
    $review = $result->fetch_assoc();
}

// Check if form has been submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    // Get form data
    $userReview = $conn->real_escape_string($_POST['userReview']);
    $userRating = $conn->real_escape_string($_POST['userRating']);
    

    // Update user data
    $updateSql = "UPDATE bookStore.reviewTable SET userReview='$userReview', userRating='$userRating' WHERE reviewID='$reviewID'";

    if ($conn->query($updateSql) === TRUE) {
        header("Location: /admin/management.php");
        exit;
    } else {
        echo "Error updating user: " . $conn->error;
        header("Location: /admin/management.php");
        exit;
    }
} else {
    // Display form
    if (isset($review)) {
?>

        <!DOCTYPE html>
        <html lang="en">

        <body>
            <h1>Edit Review</h1>
            <form action="" method="post">
                <input type="hidden" name="reviewID" value="<?php echo $review['reviewID']; ?>">
                <div>
                    <label>Review</label>
                    <input type="text" name="userReview" value="<?php echo $review['userReview']; ?>">
                </div>
                <div>
                    <label>Rating</label>
                    <input type="number" min="1" max="5" name="userRating" value="<?php echo $review['userRating']; ?>">
                </div>
                <button type="submit" name="update">Update Review</button>
                <button type="button" onclick="location.href='/admin/management.php'">Back</button>
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