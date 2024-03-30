<!DOCTYPE html>
<html lang="en">
<?php
include "inc/head.inc.php";
//create db connection
$config_file = parse_ini_file('/var/www/private/db-config.ini');
if (file_exists($config_file)) {
    // Parse the INI file
    echo ("hellooo");
    $config = parse_ini_file($config_file);
} else {
    // Get configuration from environment variables
    echo ("hello");
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
                echo "ID: " . $row["productID"] . " - Name: " . $row["productName"] . "<br>";
                // Adjust column names as per your table structure
            }
        } else {
            echo "0 results";
        }
    }
    $conn->close();
}

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
                        <div class="section-heading">
                            <!-- <span>Featured Products</span> -->
                            <h2>New Releases</h2>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <?php foreach ($books as $book) { ?>
                        <div class="col-md-4 col-sm-6 col-xs-12">
                            <div class="featured-item">
                                <div class="thumb">
                                    <img src="images/tabby_small.jpg" alt="">
                                </div>
                                <div class="down-content">
                                    <h4><?php echo htmlspecialchars("Book Name:" + $book['productName']); ?></h4>
                                    <div><?php echo htmlspecialchars($book['arrivalDate']); ?></div>
                                    <div><?php echo htmlspecialchars($book['productGenre']); ?></div>
                                    <div><?php echo htmlspecialchars($book['price']); ?></div>
                                    <div><?php echo htmlspecialchars($book['bookAuthor']); ?></div>



                                    <p>This is an image about tabby cats.</p>

                                    <div class="text-button">
                                        <a href="product-details.html">View More</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                    <div class="col-md-4 col-sm-6 col-xs-12">
                        <div class="featured-item">
                            <div class="thumb">
                                <img src="images/chihuahua_small.jpg" alt="Chihuahua">
                            </div>
                            <div class="down-content">
                                <h4>Chihuahua</h4>

                                <span><del><sup>$</sup>999.00 </del> <strong><sup>$</sup>779.00</strong></span>

                                <p>This is a book about Chihuahuas.</p>

                                <div class="text-button">
                                    <a href="product-details.html">View More</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-6 col-xs-12">
                        <div class="featured-item">
                            <div class="thumb">
                                <img src="images/tabby_small.jpg" alt="">
                            </div>
                            <div class="down-content">
                                <h4>Tabby Cats</h4>

                                <span><del><sup>$</sup>1999.00 </del> <strong><sup>$</sup>1779.00</strong></span>

                                <p>This is an image about tabby cats.</p>

                                <div class="text-button">
                                    <a href="product-details.html">View More</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-6 col-xs-12">
                        <div class="featured-item">
                            <div class="thumb">
                                <img src="images/tabby_small.jpg" alt="">
                            </div>
                            <div class="down-content">
                                <h4>Tabby Cats</h4>

                                <span><del><sup>$</sup>99.00 </del> <strong><sup>$</sup>79.00</strong></span>

                                <p>This is a book about tabbies.</p>

                                <div class="text-button">
                                    <a href="product-details.html">View More</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-6 col-xs-12">
                        <div class="featured-item">
                            <div class="thumb">
                                <img src="images/chihuahua_small.jpg" alt="">
                            </div>
                            <div class="down-content">
                                <h4>Chihuahua.</h4>

                                <span><del><sup>$</sup>999.00 </del> <strong><sup>$</sup>779.00</strong></span>

                                <p>This is a book about Chihuahuas</p>

                                <div class="text-button">
                                    <a href="product-details.html">View More</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-6 col-xs-12">
                        <div class="featured-item">
                            <div class="thumb">
                                <img src="images/tabby_small.jpg" alt="">
                            </div>
                            <div class="down-content">
                                <h4>Tabby Cats</h4>

                                <span><del><sup>$</sup>1999.00 </del> <strong><sup>$</sup>1779.00</strong></span>

                                <p>This is an image about tabby cats.</p>

                                <div class="text-button">
                                    <a href="product-details.html">View More</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </main>
    <?php
    include "inc/footer.inc.php";
    ?>


    <!--Modal-->
    <div id="imgModal" class="imgModal">
        <span class="close">&times;</span>
        <img class="modal-content" id="img01">
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js" type="text/javascript"></script>
    <script>
        window.jQuery || document.write('<script src="js/vendor/jquery-1.11.2.min.js"><\/script>')
    </script>x
    <script src="js/vendor/bootstrap.min.js"></script>
    <script src="js/datepicker.js"></script>
    <script src="js/plugins.js"></script>
</body>

</html>