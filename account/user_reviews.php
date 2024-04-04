<?php
session_start();

$reviews = [];
$errorMsg = '';
$success = true;

include_once "./../db_config.php";

// Check if user is logged in and get their userID
if ($success && isset($_SESSION['userID'])) {
    $userID = $_SESSION['userID'];

    // Prepare the SQL query to fetch all reviews by the logged-in user
    $stmt = $conn->prepare("SELECT rt.reviewID, rt.productID, rt.userReview, rt.userRating, pt.productName FROM reviewTable rt INNER JOIN productTable pt ON rt.productID = pt.productID WHERE rt.userID = ?");
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch all reviews
    if ($result->num_rows > 0) {
        while ($review = $result->fetch_assoc()) {
            $reviews[] = $review;
        }
    } else {
        $errorMsg = "You haven't reviewed any products yet.";
        $success = false;
    }
    $stmt->close();
} else {
    $errorMsg = "You must be logged in to view your reviews.";
    $success = false;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Your Reviews</title>
</head>
<body>
    <main class="container">
        <!-- <h2>Your Reviews</h2> -->
        <?php if ($success): ?>
            <?php foreach ($reviews as $review): ?>
                <div class="review">
                    <h3><?= htmlspecialchars($review['productName']); ?></h3>
                    <p>Rating: <?= htmlspecialchars($review['userRating']); ?>/5</p>
                    <p><?= htmlspecialchars($review['userReview']); ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p><?= $errorMsg; ?></p>
        <?php endif; ?>
    </main>
</body>
</html>
