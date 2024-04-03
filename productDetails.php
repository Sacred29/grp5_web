<!DOCTYPE html>
<html lang="en">
<?php
include "inc/head.inc.php";
session_start();

?>

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
                    echo "<div class='input-group w-auto justify-content-end align-items-center'>";
                    echo "<input type='number' step='1' max='10' value='1' name='quantity' class='quantity-field text-center w-25'>";
                    echo "</div>";
                    echo "</div>";

                    //Add to cart button
                    echo "<button class='btn btn-primary'>Add to Cart</button>";
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
            <div class="card">
                <form action="process_productReview.php?=<?php echo $book['productID'] ?>">
                    <div class="mb-3">
                        <label for="productReview" class="form-label">Leave your review here: </label>
                        <input maxlength="45" type="text" id="productReview" name="productReview" class="form-control" placeholder="Input Product Review">
                    </div>
                    <div class="mb-3">
                        <label for="productRating" class="form-label">Product Rating</label>
                        <input maxlength="2" type="number" id="productRating" name="productRating" class="form-control" placeholder="Input Product Ratings">
                    </div>
                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>

                </form>
            </div>
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