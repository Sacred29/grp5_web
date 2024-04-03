<!DOCTYPE html>
<html lang="en">

<head>
<?php
include "inc/head.inc.php";
session_start();
var_dump($_SESSION);

?>

</head>
<body>
    <?php
    include "inc/nav.inc.php";
    ?>
    <main class="container">

        <section class="featured-places">
            <div class="container">


                <?php

                $books = [];



                //create db connection
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
                    $errorMsg = "Connection failed: " . $conn->connect_error;
                    $success = false;
                } else {



                    //check connection
                    if ($conn->connect_error) {
                        $errorMsg = "Connection failed: " . $conn->connect_error;
                        $success = false;
                    } else {
                        //Prepare statement
                        //Bind and execute query statement
                        if (isset($_GET['id'])) {
                            $id = $_GET['id'];
                            $stmt = $conn->query("SELECT * FROM productTable WHERE productID = $id");

                            //$stmt = $conn->prepare("INSERT INTO world_of_pets_members (fname, lname, email, password) VALUES ('jane','doe','jane@abc.com','123')");



                            // Check if there are rows returned
                            if ($stmt->num_rows > 0) {
                                // Loop through the rows and fetch the data
                                while ($row = $stmt->fetch_assoc()) {
                                    // Access data using column names
                                    $books[] = $row;
                                    // Adjust column names as per your table structure
                                }
                            } else {
                                echo "0 results";
                            }

                            //Delete Product Function
                        } else if (isset($_POST['deleteProduct'])) {

                            $deleteProductID = $_POST['deleteProductID'];
                            $stmt = $conn->prepare("DELETE FROM productTable WHERE productID = ?");
                            $stmt->bind_param("i", $deleteProductID);

                            if ($stmt->execute()) {
                                echo "Record deleted successfully";
                            } else {
                                echo "Error deleting record: " . $conn->error;
                            }

                            $stmt->close();
                            $conn->close();

                            // Redirect back to the admin page or inform the user

                            exit;
                        } else if (isset($_GET['id']) && isset($_SESSION['userID'])) {

                            $productID = $_GET['id'];
                            $userID = $_SESSION['userID'];
                            $quantity = $_POST['quantity'];
                            echo "<script>console.log($productID)</script>";
                            echo "<script>console.log($userID)</script>";
                            echo "<script>console.log($quantity)</script>";
                            $stmt = $conn->prepare("INSERT INTO cartID, VALUES(?, ?, ?, ?)");
                            $stmt->bind_param("i,i,i,d", $userID, $productID, $quantity, $bookPrice);

                            if ($stmt->execute()) {
                                echo "Record deleted successfully";
                            } else {
                                echo "Error deleting record: " . $conn->error;
                            }

                            $stmt->close();
                            $conn->close();

                            // Redirect back to the admin page or inform the user

                            exit;
                        } else {
                            // Redirect them to admin page or show an error
                            echo "Invalid request.";
                        }



                        //$stmt = $conn->prepare("INSERT INTO world_of_pets_members (fname, lname, email, password) VALUES ('jane','doe','jane@abc.com','123')");



                        // Check if there are rows returned

                    }
                    $conn->close();
                }







                foreach ($books as $book) {
                    $bookPrice = number_format((float)$book['price'], 2);
                    echo "<div class='card mb-3'>";
                    echo "<div class='row no-gutters'>";
                    echo "<div class='col-md-4'>";
                    echo "<img src='{$book['productImage']}' class='card-img' alt=''>";
                    echo "</div>";
                    echo "<div class='col-md-8'>";
                    echo "<div class='card-body'>";

                    //display product details
                    echo "<h3 class='card-title'>Product Name: " . $book['productName'] . "</h5>";
                    echo "<p class='card-text'>Price: " . $bookPrice . "</p>";
                    echo "<p class='card-text'>Author: " . $book['bookAuthor'] . "</p>";
                    echo "<p class='card-text'>Publisher: " . $book['bookPublisher'] . "</p>";
                    echo "<p class='card-text'>Book Arrival: " . $book['arrivalDate'] . "</p>";
                    echo "<p class='card-text'>Genre: " . $book['productGenre'] . "</p>";
                    echo "<p class='card-text'>Book UEN: " . $book['bookUEN'] . "</p>";

                    //Quantity Selector Code
                    echo "<div class='d-flex justify-content-between'>";
                    echo "<div>";
                    echo "<p class='text-dark'>Quantity</p>";
                    echo "</div>";
                    echo "<form action='productDetails.php?={$book['productID']}' method='POST'>";
                    echo "<div class='input-group w-auto justify-content-end align-items-center'>";
                    echo "<input type='number' step='1' max='10' value='1' name='quantity' id='quantity' class='quantity-field text-center w-25'>";
                    echo "</div>";
                    echo "</div>";

                    //Add to cart button
                    echo "<input type='submit' name='addtoCart' id='addtoCart' value='Add to Cart' class='btn btn-primary'>";
                    echo "</form>";
                    echo "</div>"; // Close card-body
                    echo "</div>"; // Close col-md-8
                    echo "</div>"; // Close row
                    echo "</div>"; // Close card

                }


                ?>

                <form action="productDetails.php" method="POST">
                    <input type="hidden" name="deleteProductID" id="deleteProductID" value="<?php echo $book['productID'] ?>">
                    <input type="submit" onclick="confirmDelete()" name="deleteProduct" id="deleteProduct" value="Delete" class="btn btn-danger">
                </form>

                <form action="updateProduct.php?id=<?php echo $book['productID'] ?>" method="POST">
                    <input type="hidden" name="updateProductID" id="updateProductID" value="<?php echo $book['productID'] ?>">
                    <input type="submit" name="updateProduct" id="updateProduct" value="Update Product" class="btn btn-primary">
                </form>


            </div>

        </section>
        <div class="container">
            <h1 class="mt-5">Leave your review here!</h1>
            <div class="card">
                <div class="card-body">
                    <form action="process_productReview.php" method="POST">
                        <div class="form-group">
                            <label for="review">Your Review:</label>
                            <textarea class="form-control" id="review" name="review" rows="4" placeholder="Enter your review here"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="rating">Your Rating:</label>
                            <select class="form-control" id="rating" name="rating">
                                <option value="1">1 - Poor</option>
                                <option value="2">2 - Fair</option>
                                <option value="3">3 - Average</option>
                                <option value="4">4 - Good</option>
                                <option value="5">5 - Excellent</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit Review</button>
                    </form>
                </div>
            </div>
            </br>
            <h1 class="mt-5">Reviews on Book:</h1>
            <?php
            $reviews = [];



            //create db connection
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
                $errorMsg = "Connection failed: " . $conn->connect_error;
                $success = false;
            } else {



                //check connection
                if ($conn->connect_error) {
                    $errorMsg = "Connection failed: " . $conn->connect_error;
                    $success = false;
                } else {
                    //Prepare statement
                    //Bind and execute query statement
                    if (isset($_GET['id'])) {
                        $id = $_GET['id'];
                        $stmt = $conn->query("SELECT * FROM reviewTable WHERE productID = $id");

                        //$stmt = $conn->prepare("INSERT INTO world_of_pets_members (fname, lname, email, password) VALUES ('jane','doe','jane@abc.com','123')");



                        // Check if there are rows returned
                        if ($stmt->num_rows > 0) {
                            // Loop through the rows and fetch the data
                            while ($row = $stmt->fetch_assoc()) {
                                // Access data using column names
                                $reviews[] = $row;
                                // Adjust column names as per your table structure
                            }
                        } else {
                            echo "<h3>No reviews yet! Leave your review now!</h3>";
                        }
                    } else {
                        // Redirect them to admin page or show an error
                        echo "Invalid request.";
                    }



                    //$stmt = $conn->prepare("INSERT INTO world_of_pets_members (fname, lname, email, password) VALUES ('jane','doe','jane@abc.com','123')");



                    // Check if there are rows returned

                }
            }
            $conn->close();
            foreach ($reviews as $review) {

                echo "<div class='card'>";
                echo "<div class='col-md-8'>";
                echo "<div class='card-body'>";

                //display product details
                echo "<h3 class='card-title'>User: " . $_SESSION['fName'] . $_SESSION['lName'] . "</h3>";
                echo "<p class='card-text'>Review: " . $review['userReview'] . "</p>";
                echo "<p class='card-text'>Rating: " . $review['userRating'] . "</p>";

                echo "</div>"; // Close col-md-8
                echo "</div>"; // Close row
                echo "</div>"; // Close card
            }
            ?>
        </div>




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
    </script>


</body>