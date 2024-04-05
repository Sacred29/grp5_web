<!DOCTYPE html>
<html lang="en">
<?php
include "inc/head.inc.php";
session_start();

$books = [];
$reviews = [];
$errorMsg = '';
$success = true;


// Create db connection
$config_file = '/var/www/private/db-config.ini';
if (file_exists($config_file)) {
    $config = parse_ini_file($config_file);
} else {
    $config['servername'] = getenv('SERVERNAME');
    $config['username'] = getenv('DB_USERNAME');
    $config['password'] = getenv('DB_PASSWORD');
    $config['dbname'] = getenv('DBNAME');
}

$conn = new mysqli($config['servername'], $config['username'], $config['password'], $config['dbname']);
if ($conn->connect_error) {
    $errorMsg = "Connection failed: " . $conn->connect_error;
    $success = false;
}


if (!empty($_GET["action"])) {

    switch ($_GET["action"]) {
        case "add":

            $quantity = $_POST["quantity"];

            if (!empty($_POST["quantity"])) {

                $qty = $_POST["quantity"];
                $id = $_GET["id"];


                //now that i have product n qty --> i need to check if id matches any existing product
                $cartItems = $_SESSION["cart_item"];
                $stmt = $conn->prepare("SELECT * FROM bookStore.productTable WHERE productID='$id';");
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $itemArray = array(
                            $row["bookUEN"] => array(
                                'productName' => $row["productName"],
                                'bookUEN' => $row["bookUEN"],
                                'quantity' => $_POST["quantity"],
                                'price' => $row["price"],
                                'productImage' => $row["productImage"],
                                'bookAuthor' => $row["bookAuthor"]
                            )
                        );
                        $name = $row["productName"];
                        $uen = $row["bookUEN"];
                        $price = $row["price"];
                        $image = $row["productImage"];
                        $bookAuthor = $row["bookAuthor"];

                    }
                } //end of result

                if (!empty($_SESSION["cart_item"])) {
                    //if session is not empty
                    $matchFound = false;
                    foreach ($_SESSION["cart_item"] as $key => $item) {
                        $bookUEN = $item["bookUEN"];


                        if ($uen == $bookUEN) {

                            $matchFound = true;
                            if (!empty($item["quantity"]) && $matchFound) {
                                $_SESSION["cart_item"][$key]["quantity"] += $qty;


                            }
                        }
                    }

                    if (!$matchFound) {

                        $_SESSION["cart_item"] = array_merge($_SESSION["cart_item"], $itemArray);
                    }
                } else {
                    $_SESSION["cart_item"] = $itemArray;

                }
            } else {

            }
            $stmt->close();
    }
}

// Assuming below logic is executed only if $success is true
// Fetch product details
if ($success && isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $conn->real_escape_string($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM productTable WHERE productID = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $books[] = $row;
        }
    } else {
        $errorMsg = "0 results";
        $success = false;
    }
    $stmt->close();

    // Fetch reviews if product exists
    if ($success && isset($books[0])) {
        $productID = $books[0]['productID'];

        $reviewQuery = "
            SELECT rt.reviewID, rt.userReview, rt.userRating, ut.fName, ut.lName
            FROM reviewTable rt
            INNER JOIN userTable ut ON rt.userID = ut.userID
            WHERE rt.productID = ?";

        $reviewStmt = $conn->prepare($reviewQuery);
        $reviewStmt->bind_param("i", $productID);
        $reviewStmt->execute();
        $reviewsResult = $reviewStmt->get_result();


        $reviews = [];
        if ($reviewsResult->num_rows > 0) {
            while ($review = $reviewsResult->fetch_assoc()) {
                $reviews[$productID][] = $review;
            }
        }
        $reviewStmt->close();
    }
}

// Handle product deletion
if ($success && isset($_POST['deleteProduct'])) {
    $deleteProductID = $_POST['deleteProductID'];
    $stmt = $conn->prepare("DELETE FROM productTable WHERE productID = ?");
    $stmt->bind_param("i", $deleteProductID);
    if ($stmt->execute()) {
        $_SESSION['message'] = "Record deleted successfully";
        header('Location: /products.php');
        exit;
    } else {
        $errorMsg = "Error deleting record: " . $conn->error;
    }
    $stmt->close();
}

$conn->close();
?>



<body>
    <?php include "inc/nav.inc.php"; ?>
    <main class="container">
        <section class="featured-places">
            <div class="container">
                <?php if ($success && count($books) > 0) : ?>
                    <?php foreach ($books as $book) : ?>
                        <div class='card mb-3'>
                            <div class="row no-gutters">
                                <div class="col-md-4">
                                    <img src='<?= htmlspecialchars($book['productImage']) ?>' alt='Product Image' class="img-fluid">
                                </div>
                                <div class="col-md-8">
                                    <div class="card-body">
                                        <h4 class="card-title">Product Name: <?= htmlspecialchars($book['productName']) ?></h4> <span class="card-text">Price: <?= htmlspecialchars(number_format((float)$book['price'], 2)) ?></span>
                                        <p class="card-text">Product Author: <?= htmlspecialchars($book['bookAuthor']) ?></p>
                                        <p class="card-text">Product Publisher: <?= htmlspecialchars($book['bookPublisher']) ?></p>
                                        <p class="card-text">Product Genre: <?= htmlspecialchars($book['productGenre']) ?></p>
                                        <p class="card-text">Product UEN: <?= htmlspecialchars($book['bookUEN']) ?></p>

                                        <?php //if statement for showing and hiding based on session 
                                        if (isset($_SESSION['user_privilege']) && $_SESSION['user_privilege'] != 'staff' && $_SESSION['user_privilege'] != 'admin') {
                                        ?>
                                            <form action='productDetails.php?action=add&id=<?php echo $book['productID']; ?>' method='POST' class='d-flex align-items-center'>
                                                <div class='d-flex justify-content-between'>
                                                    <div>
                                                        <p class='text-dark'>Quantity</p>
                                                    </div>
                                                    <div class='input-group w-auto justify-content-end align-items-center'>
                                                        <input type='number' step='1' max='10' value='1' name='quantity' id='quantity' class='quantity-field text-center w-25'>
                                                    </div>
                                                </div>
                                                <div class='d-flex justify-content-between align-items-center mb-3'>

                                                    <input type='submit' name='addtoCart' id='addtoCart' onclick="confirmAddtoCart()" value='Add to Cart' class='btn btn-primary mr-2'>
                                            </form>
                                        <?php
                                        } ?>

                                        <div class='d-flex'>
                                            <?php //if statement for showing and hiding based on session 
                                            if (isset($_SESSION['user_privilege']) && $_SESSION['user_privilege'] != 'user') {
                                            ?>
                                                <form action="productDetails.php" method="POST">
                                                    <input type="hidden" name="deleteProductID" id="deleteProductID" value="<?php echo $book['productID'] ?>">
                                                    <input type="submit" onclick="confirmDelete()" name="deleteProduct" id="deleteProduct" value="Delete" class="btn btn-danger">
                                                </form>
                                                &nbsp;
                                                <form action="updateProduct.php?id=<?php echo $book['productID'] ?>" method="POST">
                                                    <input type="hidden" name="updateProductID" id="updateProductID" value="<?php echo $book['productID'] ?>">
                                                    <input type="submit" name="updateProduct" id="updateProduct" value="Update Product" class="btn btn-primary">
                                                </form>


                                            <?php
                                            } ?>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
            </div>


            <?php
                        if (isset($_SESSION['message'])) {
                            echo "<script>alert('" . $_SESSION['message'] . "');</script>";
                            unset($_SESSION['message']); // Clear the message after displaying it
                        }
            ?>

            <!--
                        <button class='btn btn-secondary' onclick='showReviewBox("reviewBox<?= $book['productID'] ?>")'>Add Review</button>

                        <div id='reviewBox<?= $book['productID'] ?>' class="review-box">
                            <form action='process_productReview.php' method='post'>
                                <textarea name='userReview' placeholder='Enter your review here...' required></textarea>
                                <input type='hidden' name='productID' value='<?= $book['productID'] ?>'>
                                <input type='submit' name='submitReview' value='Submit Review' class='btn btn-success'>
                            </form>
                        </div>
                        !-->



            <div class="container">
                <?php if (isset($_SESSION['user_privilege']) && $_SESSION['user_privilege'] == 'user') { ?>
                    <h1 class="mt-5">Leave your review here!</h1>
                    <div class="card">
                        <div class="card-body">
                            <form action="process_productReview.php" method="POST">
                                <div class="form-group">
                                    <label for="review">Your Review:</label>
                                    <textarea required class="form-control" id="userReview" name="userReview" placeholder="Enter your review here"></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="userRating">Your Rating:</label>
                                    <select required class="form-control" id="userRating" name="userRating">
                                        <option value="1">1 - Poor</option>
                                        <option value="2">2 - Fair</option>
                                        <option value="3">3 - Average</option>
                                        <option value="4">4 - Good</option>
                                        <option value="5">5 - Excellent</option>
                                    </select>
                                </div>
                                <input type="hidden" name="productID" value="<?= htmlspecialchars($book['productID']) ?>">
                                <input type="submit" class="btn btn-primary" onclick="validateReview()" value="Submit Review" name="submitReview" id="submitReview">
                            </form>
                        </div>
                    </div>
                <?php
                        } ?>


                <div class='reviews-section'>
                    <?php if (!empty($reviews) && isset($reviews[$book['productID']])) : ?>
                        <h1 class="mt-5">Reviews on Book:</h1>
                        <?php foreach ($reviews[$book['productID']] as $review) : ?>
                            <div class='card'>
                                <div class="card-body">
                                    <strong><?= htmlspecialchars($review['fName'] ?? '') ?> <?= htmlspecialchars($review['lName'] ?? '') ?></strong>
                                    <p>Rating: <?= htmlspecialchars($review['userRating'] ?? '') ?>/5</p>
                                    <p><?= htmlspecialchars($review['userReview'] ?? '') ?></p>

                                    <?php if (isset($_SESSION['user_privilege']) && ($_SESSION['user_privilege'] != 'user')) : ?>
                                        <form method="post" action="process_deletereview.php" class="delete-review-form">
                                            <input type="hidden" name="reviewID" value="<?= $review['reviewID'] ?>">
                                            <input type="hidden" name="productID" value="<?= $book['productID'] ?>">
                                            <input type="submit" onclick=deleteReview() value="Delete" class="fa fa-trash">
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>




                        <?php endforeach; ?>
                </div>
            </div>




        <?php else : ?>
            <p>No reviews yet. Be the first to review!</p>
        <?php endif; ?>
        </div>

    <?php endforeach; ?>

<?php else : ?>
    <p><?= $errorMsg ?></p>
<?php endif; ?>

        </section>
    </main>

    <script>
        function confirmDelete() {
            var result = confirm("Are you sure you want to delete this listing?");
            if (result == false) {
                event.preventDefault();
            }
            if (result == true) {
                alert("Your listing has been deleted successfully!");
            }
        }

        function confirmAddtoCart() {
            var result = confirm("Are you sure you want to add to cart?");
            if (result == false) {
                event.preventDefault();
            }
            if (result == true) {
                alert("Added to Cart Successfully!");
            }
        }

        function deleteReview() {
            var result = confirm("Are you sure you want to delete this review?");
            if (result == false) {
                event.preventDefault();
            }
            if (result == true) {
                alert("Your review has been deleted successfully!");
            }
        }

        function showReviewBox(reviewBoxId) {
            var reviewBox = document.getElementById(reviewBoxId);
            if (reviewBox.style.display === 'none') {
                reviewBox.style.display = 'block';
            } else {
                reviewBox.style.display = 'none';
            }
        }
    </script>
</body>

</html>