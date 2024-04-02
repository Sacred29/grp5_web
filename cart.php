<?php
    include "inc/head.inc.php";
?>
<body>
    <?php
    include "inc/nav.inc.php";
    ?>
<main class="container">
    <h1>Shopping Cart</h1>
    <div id="shopping-cart">
        <a id="btnEmpty" href="cart.php?action=empty">Empty Cart</a>
        <a id="btnRedirect" href="checkout.php">Check Out</a>
        <?php
            $quantity = 1;
            if (!isset($_SESSION["cart_item"])) {
                $_SESSION["cart_item"] = array(); 
                echo '<script>console.log("Setting up cart_item here");</script>';
                $total_quantity = 0;
                $total_price = 0;
            }

            else{
                $total_quantity = 0;
                $total_price = 0;
                echo '<script>console.log("running here");</script>';
        
                echo '<div class="container">';
                echo '<table class="table center-table" style="border: 1px black solid";>';
                echo '<tr><th>Image</th><th>Item Name</th><th>Quantity</th><th>Price</th><th>Remove Item</th></tr>';
    
                foreach ($_SESSION["cart_item"] as $item) {
                    $item_price = $item["quantity"]*$item["price"];
                    $cartItems = $_SESSION["cart_item"];
                    echo '<script>console.log("running 2here");</script>';
                    echo "<script> console.log('Cart Items: " . json_encode($cartItems) . "');  </script>";

                    $image = $item["productImage"];
                    $productName = $item["productName"];
                    $price = $item["price"];
                    $quantity = $item["quantity"];
                    $itemPrice = $item_price;
                    $bookUEN = $item["bookUEN"];
                    echo '<script>console.log("UEN 22: ' . $bookUEN . '");</script>';
                    echo '<script>console.log("UEN 22: ' . $image . '");</script>'; 
		
                    $total_quantity += $item["quantity"];
                    $total_price += ($item["price"]*$item["quantity"]);
              //close off foreach

            echo '<tr>';
            echo "<td><img src='{$image}' alt='{$productName}' style='width: 20%;'></td>";
            echo "<td>{$productName}</td>";

            echo "<td>
            <form method='POST' action=''>
                <input type='number' name='newQuantity' value='{$quantity}' min=1 max=10'>
                <input type='hidden' name='productID' value='{$bookUEN}'>
                <br>
                <button class='btn' type='submit' name='action' value='updateQuantity'>Update</button>
            </form>
        </td>";

        echo "<td><b>$" . $price . "</b></td>";

        echo "<td>
        <form method='POST' action=''>
            <input type='hidden' name='productID' value='{$bookUEN}'>
            <button class='btn' type='submit' name='action' value='removeItem'>Remove</button>
        </form>
    </td>";
echo '</tr>';
                }
        
        }
        
        if (empty($cartItems)) {
            echo '<h3>Cart is empty</h3>';
        }
        else {
            echo '<tr><td></td><td></td><td></td><td></td><td><b>Total: $' . $total_price . '</b></td></tr>';
            echo '<tr><td></td><td></td><td></td><td></td><td>
                    <form action="" method="POST">
                        <input type="hidden" name="totalPrice" value="' . $total_price . '">
                        <input type="submit" class="btn" name="action" value="Checkout">
                    </form>
                </td></tr>';
            echo '</table>';
            echo '</div>';
        }
    ?>
        

    <div class="container-fluid bg-3 text-center">    
    <h3 class="margin">Product Catalog</h3><br>

        
    <?php
    //create db connection
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
            //prepare statement
            $stmt = $conn->prepare("SELECT * FROM bookStore.productTable ORDER BY productID ASC");
            $stmt->execute();
            $result = $stmt->get_result();
            $resultArray = array();
            while ($row = $result->fetch_assoc()) {
                $resultArray[] = $row;
            }
            if(!empty($resultArray)){
                foreach($resultArray as $key=>$value){

                    echo '<div class="col-md-4">';
                    echo '<div class="product-item">';
                    echo '<form method="post" action="cart.php?action=add&uen=' . $resultArray[$key]["bookUEN"] . '">';
                    echo '<img src="' .$resultArray[$key]["productImage"] . '" class="img-responsive margin" style="width:100%" alt="Image">';
                    echo '<div class="product-title" style="text-align:center;">' . $resultArray[$key]["productName"] . '</div>';
                    echo '<div class="product-author" style="text-align:center; margin-bottom:5px;">"by" '.$resultArray[$key]["bookAuthor"] .'</div>';
                    echo '<div class="product-price" style="text-align:right; margin-left: 30px;"> $ '.$resultArray[$key]["price"] . '</div>';
                    echo '<div class="cart-action" style="text-align:left;">';
                    echo '<input type="text" class="product-quantity" name="quantity" value="1" size="1" style="text-align:center; margin-left:70px;"/>';
                    echo '<input type="submit" style="margin-right:20px;" value="Add to Cart" class="btnAddAction" />';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                    echo '</form>';

            }
                }
                $stmt->close();
            }
            $conn->close();
        

    ?>
</div> <!-- End of product-grid-->
</main>
    <?php
    include "inc/footer.inc.php";
    ?>
</body>



<?php
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
        else{
            if(!empty($_GET["action"])) {
                switch($_GET["action"]){
                    case "add":
                          
                        if(!empty($_POST["quantity"])){
                            $uen = $_GET["uen"];
                            //echo 'UEN: ' . $uen . '<script>console.log("UEN: ' . $uen . '");</script>';                           
                            $stmt = $conn->prepare("SELECT * FROM bookStore.productTable WHERE bookUEN='$uen';");
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
                                    ));
                                    $name = $row["productName"];
                                    $uen = $row["bookUEN"];
                                    $price = $row["price"];
                                    $image = $row["productImage"];
                                    $bookAuthor = $row["bookAuthor"];
                                //   echo '<script>console.log("Name: ' . $name . '");</script>';    
                                //   echo '<script>console.log("UEN: ' . $uen . '");</script>'; 
                                //   echo '<script>console.log("price: ' . $price . '");</script>';                           
                                //   echo '<script>console.log("image: ' . $image . '");</script>';
                                //   echo '<script>console.log("author: ' .$bookAuthor . '");</script>';                           
                          
                       
                                }
                              } else {
                               // echo '<script>console.log("No Result found");</script>';
                            }

                        //itemArray stores the result of the selected item
                        if(!empty($_SESSION["cart_item"])) {
                           // echo '<script>console.log("UEN 2: ' . $uen . '");</script>'; 
                            //currently session cart_item is empty
                            if(in_array($uen,array_keys($_SESSION["cart_item"]))) {
                               //echo '<script>console.log("item found inside cart");</script>';
                                foreach($_SESSION["cart_item"] as $k => $v){
                                    //echo '<script>console.log("ct2");</script>';
                                    if($uen == $k) {
                                        if(empty($_SESSION["cart_item"][$k]["quantity"])) {
                                            $_SESSION["cart_item"][$k]["quantity"] = 0;
                                        }
                                        $_SESSION["cart_item"][$k]["quantity"] += $quantity;
                                       // echo '<script>console.log("redirect");</script>';
                                    }
                                }
                                
                            } else {
                                $_SESSION["cart_item"] = array_merge($_SESSION["cart_item"],$itemArray);
                            }
                                            
                            } else {
                                $_SESSION["cart_item"] = $itemArray;
                                  // Print the contents of $_SESSION["cart_item"]
                                //   echo '<script>console.log("Cart Items:");</script>';
                                //   echo '<script>console.log(' . json_encode($_SESSION["cart_item"]) . ');</script>';
                            }
                        }//end of EMPTY POST QUANTITY
                    break;

                    case "remove":
                        if(!empty($_SESSION["cart_item"])) {
                            $uen = $_GET["uen"];
                            //echo "<script>console.log('bookUEN: " . $uen . "');</script>"; // Echo the variable to the console
                            foreach($_SESSION["cart_item"] as $item) {
                                $k = $item["bookUEN"];
                                $v = $item;
                               // echo "<script>console.log('k: " . $k . "');</script>"; // Echo the variable to the console
                                //echo "<script>console.log('v: " . $v . "');</script>"; // Echo the variable to the console
                                    if($uen == $k)
                                       // echo "<script>console.log('Removing item from cart');</script>"; // Echo the variable to the console
                                        unset($_SESSION["cart_item"][$k]);				
                                    if(empty($_SESSION["cart_item"]))
                                        unset($_SESSION["cart_item"]);
                            }
                        }
                    break;
                    case "empty":
                      //  echo '<script>console.log("Empty Clicked");</script>';
                        unset($_SESSION["cart_item"]);
                    break;
                                
                }//end of switch
            }//end of !empty GET ACTION
            else{
               // echo "<h4>it is empty here!</h4>";
                //echo '<script>console.log("still empty");</script>';
            }
            $stmt->close();
        }//end of conn else
        $conn->close();

    
?>
