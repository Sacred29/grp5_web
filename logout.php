<?php
    session_start();
    // Destroy all session data.
    //POST to cartTable the userID, productID, quantity, price
    $userID = $_SESSION["userID"];
    echo "<script> console.log('UserID: " . $_SESSION["userID"] . "');  </script>";
    foreach ($_SESSION["cart_item"] as $key => $item) {
        //foreach item in the cart --> if it matches i want to update the quantity
        //if it does not match --> its creating
        $bookUEN = $item["bookUEN"];
        $itemQuantity = $_SESSION["cart_item"][$key]["quantity"];
        $price = $_SESSION["cart_item"][$key]["price"];
        $totalItemPrice = $itemQuantity * $price;
        
        echo '<script>console.log("bookUEN: ' . $bookUEN . '");</script>';
        echo '<script>console.log("Item Quantity: ' . $itemQuantity . '");</script>';
        echo '<script>console.log("Total for item: ' . $totalItemPrice . '");</script>';

        $config_file = '/var/www/private/db-config.ini';
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
    if ($conn->connect_error){
        $errorMsg = "Connection failed: " .$conn->connect_error;
        $success = false;
    }

    else {
        //INSERT INTO orderTable as a record
        $productID = "";
        $stmt = $conn->prepare("SELECT productID From bookStore.productTable where bookUEN=?");
        $stmt->bind_param("s", $bookUEN);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result){
            while ($row = $result->fetch_assoc()){
                $productID = $row["productID"];
            }
        }
        echo '<script>console.log("Product ID: ' . $productID . '");</script>';

        $stmt2 = $conn->prepare("INSERT INTO cartTable (userID, productID, quantity, price) VALUES (?,?,?,?)");
        $stmt2->bind_param("ssss", $userID, $productID, $itemQuantity, $totalItemPrice);
        $stmt2->execute();

    }
    $stmt->close();
    $conn->close();

    }
    session_destroy();
    // Redirect to the homepage after logging out.
    header("Location: index.php");
    exit();
?>