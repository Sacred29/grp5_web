<!DOCTYPE html>
<html lang="en">
<?php
include "inc/head.inc.php";
session_start();
var_dump($_SESSION);
?>

<body>
    <!-- Collapsible Top Navbar -->
    <?php
    include "inc/nav.inc.php";
    ?>
    <?php
    include "inc/header.inc.php";
    ?>
    <main class="container">

        <section class="featured-places">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <?php //if statement for showing and hiding based on session 
                        if (isset($_SESSION['user_privilege']) && $_SESSION['user_privilege'] != 'user') {
                            echo "<a href='createProduct.php' target=''> <button class='btn btn-primary'>Register New Product</button></a>";
                        }
                        ?>
                        <div class="section-heading">
                            <!-- <span>Featured Products</span> -->
                            <h2>Product Catalogue</h2>
                        </div>
                    </div>
                </div>
                <div class="row">
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
                            $stmt = $conn->query("SELECT * FROM productTable");

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
                        }
                        $conn->close();
                    }



                    ?>

                    <section>
                        <?php foreach ($books as $book) : ?>

                            <div class='col-md-4'>
                                <div class='featured-item'>
                                    <div class='item-wrapper'>
                                        <div class='thumb'>
                                            <img src='<?= htmlspecialchars($book['productImage']) ?>' alt='Product Image' style='min-height: 400px; max-height: 400px'>

                                        </div>
                                        <div class='down-content'>
                                            <h4 style='min-height: 50px;'>Product Name: <?= htmlspecialchars($book['productName']) ?></h4>
                                            <span style='min-height: 20px;'><sup>Price: <?= htmlspecialchars(number_format((float)$book['price'], 2)) ?></sup></span>
                                            <p style='min-height: 20px;'>Product Author: <?= htmlspecialchars($book['bookAuthor']) ?></p>
                                            <p style='min-height: 20px;'>Product Publisher: <?= htmlspecialchars($book['bookPublisher']) ?></p>
                                            <div class='text-button'>
                                                <a href='productDetails.php?id=<?= $book['productID'] ?>'>View More</a>
                                            </div>
                                        </div>

                                    </div>

                                </div>
                            </div>

                        <?php endforeach; ?>
                    </section>

    </main>
    <?php
    include "inc/footer.inc.php";
    ?>



    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js" type="text/javascript"></script>
    <script>
        window.jQuery || document.write('<script src="js/vendor/jquery-1.11.2.min.js"><\/script>');
    </script>
    <script src="js/vendor/bootstrap.min.js"></script>
    <script src="js/datepicker.js"></script>
    <script src="js/plugins.js"></script>
</body>

</html>