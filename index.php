<?php
session_start();


if (isset($_SESSION['user_privilege']) && $_SESSION['user_privilege'] == 'user') {

    $userID = $_SESSION["userID"];
    $config_file = '/var/www/private/db-config.ini';

    //check if session is set --> otherwise initialize cart_item as array
    if (!isset($_SESSION["cart_item"])) {
        $_SESSION["cart_item"] = array();
        $cartItems = $_SESSION["cart_item"];
        //if cart is not set --> set up cart_item session
    }

    if (file_exists($config_file)) {
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
    //check connection
    if ($conn->connect_error) {
        $errorMsg = "Connection failed: " . $conn->connect_error;
        $success = false;
    } else {
        $productIdArray = array(); // Declare an empty array

        $stmt = $conn->prepare("SELECT productID, quantity FROM bookStore.cartTable where userID=?");
        $stmt->bind_param("s", $userID);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $productID = $row["productID"];
                $quantity = $row["quantity"];
                $productIdArray[] = $productID; // Append each productID to the array
            }
        }
        $productIdString = "";
        $productIdString = implode(", ", $productIdArray);
        if (!empty($productIdString)) {
            $stmt2 = $conn->prepare("SELECT * FROM bookStore.productTable where productID in ($productIdString)");
            // $stmt2->bind_param("s",$productIDString);
            $stmt2->execute();
            $result2 = $stmt2->get_result();
            if ($result2) {
                while ($row2 = $result2->fetch_assoc()) {
                    $itemArray = array(
                        $row2["bookUEN"] => array(
                            'productName' => $row2["productName"],
                            'bookUEN' => $row2["bookUEN"],
                            'price' => $row2["price"],
                            'productImage' => $row2["productImage"],
                            'bookAuthor' => $row2["bookAuthor"],
                            'quantity' => $quantity
                        )
                    );
                    $productName = $row2["productName"];
                    $price = $row2["price"];
                    $bookUEN = $row2["bookUEN"];
                    $bookAuthor = $row2["bookAuthor"];
                    $image = $row2["productImage"];

                    //while fetching results --> check if cart is empty here
                    if (empty($_SESSION["cart_item"])) {
                        //since cart is empty --> append the first item into it
                        $_SESSION["cart_item"] = $itemArray;
                    } else {
                        $matchFound = false;
                        //if cart is not empty --> loop through all cart_items --> check if UEN already exists --> if it does not exist
                        foreach ($_SESSION["cart_item"] as $key => $item) {

                            $cartUEN = $item["bookUEN"];
                            $_SESSION["cart_item"][$key]["quantity"] = $quantity;

                            //check if UEN exists in cart --> if exists ignore
                            if ($bookUEN == $cartUEN) {
                                $matchFound = true;
                                break;
                            }
                        }

                        if (!$matchFound) {
                            $_SESSION["cart_item"] = array_merge($_SESSION["cart_item"], $itemArray);

                        }
                    }
                }
            } //end of result2


            $stmt3 = $conn->prepare("DELETE FROM bookStore.cartTable where userID=?");
            $stmt3->bind_param("s", $userID);
            $stmt3->execute();




            $stmt->close();
            $stmt2->close();

            $stmt3->close();
            $conn->close();
        }
    } //while user is logged in
}

?>

<!DOCTYPE html>
<html lang="en">
<?php
include "inc/head.inc.php";

?>

<body>
    <?php
    if (isset($_SESSION["user_privilege"])) {
        "<strong>Well done!</strong> You successfully read this important alert message.";
        $message =  "you are logged in as " . $_SESSION["user_privilege"];
        include "inc/success-alert.inc.php";
    }
    ?>
    <!-- Collapsible Top Navbar -->
    <?php
    include "inc/nav.inc.php";
    ?>
    <?php
    include "inc/header.inc.php";
    ?>

    <main class="container">
        <section id="gallery-carousell">
            <div class="container">
                <div class="row">
                        <div class="col">
                            <div class="section-heading">
                                <!-- <span>Featured Products</span> -->
                                <h2>Featured Products</h2>
                            </div>
                    </div> 
                </div> 
            </div>
            <div class="row">
                <div class="col">
                    <?php include "inc/gallery.inc.php"; ?>
                </div>
            </div>
        </section>
        <section class="featured-places">
            
            <div class="row">
                <div class="col-md-12">
                    <div class="section-heading">
                        <h2>Latest Arrivals</h2>
                    </div>
                </div>
            </div>
            <div class="row">
                <?php
                $books = [];
                $config_file = '/var/www/private/db-config.ini';
                if (file_exists($config_file)) {
                    $config = parse_ini_file($config_file);
                } else {
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
                    $stmt5 = $conn->query("SELECT * FROM productTable ORDER BY arrivalDate DESC LIMIT 3");
                    if ($stmt5->num_rows > 0) {
                        while ($row = $stmt5->fetch_assoc()) {
                            $books[] = $row;
                        }
                    } else {
                        echo "0 results";
                    }
                    $conn->close();
                }
                ?>
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
            </div>
        </section>

    </main>
    <?php
    include "inc/footer.inc.php";
    ?>


    <!--Modal
    <div id="imgModal" class="imgModal">
        <span class="close">&times;</span>
        <img class="modal-content" id="img01">
    </div>-->

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js" type="text/javascript"></script>
    <script>
        window.jQuery || document.write('<script src="js/vendor/jquery-1.11.2.min.js"><\/script>')
    </script>
    <script src="js/vendor/bootstrap.min.js"></script>
    <script src="js/datepicker.js"></script>
    <script src="js/plugins.js"></script>
</body>

</html>