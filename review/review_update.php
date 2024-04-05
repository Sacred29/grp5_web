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

function sanitize_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

$reviewID = isset($_POST['reviewID']) ? $conn->real_escape_string($_POST['reviewID']) : null;

// Fetch review data if reviewID is set
$review = null;
if ($reviewID) {
    $sql = "SELECT * FROM bookStore.reviewTable WHERE reviewID = '$reviewID'";
    $result = $conn->query($sql);
    if ($result) {
        $review = $result->fetch_assoc();
    }
}

// Check if form has been submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    // Get form data
    $userReview = sanitize_input($_POST['userReview']);
    $userRating = sanitize_input($_POST['userRating']);

    // Update review data
    $updateSql = "UPDATE bookStore.reviewTable SET userReview='$userReview', userRating='$userRating' WHERE reviewID='$reviewID'";

    if ($conn->query($updateSql) === TRUE) {
        $_SESSION['message'] = 'Review updated successfully.';
        header("Location: /admin/management.php");
        exit;
    } else {
        $_SESSION['error'] = "Error updating review: " . $conn->error;
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

    <?php include "./../inc/head.inc.php"; // Include Bootstrap CSS and other head elements ?>


    <?php include "./../inc/nav.inc.php"; // Navigation bar ?>
    <body>
    <main class="container">
        <h1>Edit Review</h1>
        <?php if ($review): ?>
            <div class="card">
                <div class="card-body">
                    <form action="#" method="post">
                        <input type="hidden" name="reviewID" value="<?php echo htmlspecialchars($review['reviewID']); ?>">

                        <div class="form-group mb-3">
                            <label for="userReview" class="form-label">Your Review:</label>
                            <textarea class="form-control" id="userReview" name="userReview" rows="3" required><?php echo htmlspecialchars($review['userReview']); ?></textarea>
                        </div>

                        <div class="form-group mb-3">
                            <label for="userRating" class="form-label">Your Rating:</label>
                            <select class="form-control" id="userRating" name="userRating" required>
                                <option value="1" <?php echo $review['userRating'] == 1 ? 'selected' : ''; ?>>1 - Poor</option>
                                <option value="2" <?php echo $review['userRating'] == 2 ? 'selected' : ''; ?>>2 - Fair</option>
                                <option value="3" <?php echo $review['userRating'] == 3 ? 'selected' : ''; ?>>3 - Average</option>
                                <option value="4" <?php echo $review['userRating'] == 4 ? 'selected' : ''; ?>>4 - Good</option>
                                <option value="5" <?php echo $review['userRating'] == 5 ? 'selected' : ''; ?>>5 - Excellent</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary" name="update">Update Review</button>
                        <a href="./../admin/management.php" class="btn btn-secondary">Back</a>
                    </form>
                </div>
            </div>
        <?php else: ?>
            <p class="alert alert-warning">No review found with the provided ID.</p>
        <?php endif; ?>
        </main>
</body>

    <?php include "./../inc/footer.inc.php"; // Footer ?>


