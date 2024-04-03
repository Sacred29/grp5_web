<?php
    include "inc/head.inc.php";
?>

<body>
    <?php
    include "inc/nav.inc.php";
    session_start();
    echo "<script> console.log('UserID: " . $_SESSION["userID"] . "');  </script>";
    echo "<script> console.log('Final cart: " . json_encode($_SESSION["cart_item"]) . "'); </script>";


    ?>

    <?php //if statement for showing and hiding based on session 
        if (isset($_SESSION['user_privilege']) && $_SESSION['user_privilege'] == 'user') {
            echo "<script> console.log('Logged in as user');  </script>";
        }
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
                echo '<script>console.log("running here 2");</script>';
            }

            if (isset($_SESSION["cart_item"])){
                $total_quantity = 0;
                $total_price = 0;
                echo '<script>console.log("running here");</script>';
        ?>  <!-- used to check if cart_item is established in session-->

        <table class="tbl-cart" cellpadding="10" cellspacing="1">
        <tbody>
        <tr>
            <th style="text-align:left;">Name</th>
            <!--<th style="text-align:center;">Code</th>-->
            <th style="text-align:center;" width="10%">Unit Price</th>
            <th style="text-align:center;" width="10%">Quantity</th>
            <th style="text-align:center;" width="10%">Price</th>
            <th style="text-align:center;" width="5%">Remove</th>
        </tr>	

        <?php
            foreach ($_SESSION["cart_item"] as $item) {
                $item_price = $item["quantity"]*$item["price"];
                $cartItems = $_SESSION["cart_item"];
                echo "<script> console.log('Cart Items: " . json_encode($cartItems) . "');  </script>";

        ?>
            <tr>
				<td style="border-top: 1px solid black; border-bottom: 1px solid black;"><img src="<?php echo $item["productImage"]; ?>" class="cart-item-image" /><?php echo $item["productName"]; ?></td>
                <td  style="text-align:center; border-top: 1px solid black; border-bottom: 1px solid black;"><?php echo "$ ".$item["price"]; ?></td>
				<td style="text-align:center; border-top: 1px solid black; border-bottom: 1px solid black;"><?php echo $item["quantity"]; ?></td>
				<td  style="text-align:center; border-top: 1px solid black; border-bottom: 1px solid black;"><?php echo "$ ". number_format($item_price,2); ?></td>
				<td style="text-align:center; border-top: 1px solid black; border-bottom: 1px solid black;"><a href="cart.php?action=remove&uen=<?php echo $item["bookUEN"]; ?>" class="btnRemoveAction"><img src="images/icon-delete.png" alt="Remove Item" /></a></td>
			</tr>
		<?php
				$total_quantity += $item["quantity"];
				$total_price += ($item["price"]*$item["quantity"]);
            }  //close off foreach
        ?>

        <tr>
            <td colspan="2" align="right" style="display:table-cell; font-weight:bold; padding-right:30px; padding-top:10px;">Total:</td>
            <td align="right" style="display:table-cell; font-weight:bold; padding-right:50px; padding-top:10px;"><?php echo $total_quantity; ?></td>
            <td align="right" colspan="2" style="display:table-cell; font-weight:bold; padding-right:75px; padding-top:10px;"><strong><?php echo "$ ".number_format($total_price, 2); ?></strong></td>
            <td></td>
        </tr>
        </tbody>
        </table>	
        <?php
        } else {
        ?>
        <div class="no-records">Your Cart is Empty</div>
            <?php
        }
        ?>
    </div> <!-- End of shopping-cart div-->

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
                        // if ($_SERVER["REQUEST_METHOD"] == "POST") {
                        //     // Check if the "quantity" field is set in the $_POST array
                        //     if (isset($_POST["quantity"])) {
                        //       $quantity = $_POST["quantity"];
                        //       echo 'Quantity: ' . $quantity . '<script>console.log("Quantity: ' . $quantity . '");</script>';  
                    
                        //      }
                        //   }
                          
                        if(!empty($_POST["quantity"])){
                            $uen = $_GET["uen"];
                            echo 'UEN: ' . $uen . '<script>console.log("UEN: ' . $uen . '");</script>';                           
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
                                  echo '<script>console.log("Name: ' . $name . '");</script>';    
                                  echo '<script>console.log("UEN: ' . $uen . '");</script>'; 
                                  echo '<script>console.log("price: ' . $price . '");</script>';                           
                                  echo '<script>console.log("image: ' . $image . '");</script>';
                                  echo '<script>console.log("author: ' .$bookAuthor . '");</script>';                           
                          
                       
                                }
                              } else {
                                echo '<script>console.log("No Result found");</script>';
                            }

                        //itemArray stores the result of the selected item
                        if(!empty($_SESSION["cart_item"])) {
                            echo '<script>console.log("UEN 2: ' . $uen . '");</script>'; 
                            //currently session cart_item is empty
                            if(in_array($uen,array_keys($_SESSION["cart_item"]))) {
                               echo '<script>console.log("item found inside cart");</script>';
                                foreach($_SESSION["cart_item"] as $k => $v){
                                    echo '<script>console.log("ct2");</script>';
                                    if($uen == $k) {
                                        if(empty($_SESSION["cart_item"][$k]["quantity"])) {
                                            $_SESSION["cart_item"][$k]["quantity"] = 0;
                                        }
                                        $_SESSION["cart_item"][$k]["quantity"] += $quantity;
                                        echo '<script>console.log("redirect");</script>';
                                    }
                                }
                                
                            } else {
                                $_SESSION["cart_item"] = array_merge($_SESSION["cart_item"],$itemArray);
                            }
                                            
                            } else {
                                $_SESSION["cart_item"] = $itemArray;
                                  // Print the contents of $_SESSION["cart_item"]
                                  echo '<script>console.log("Cart Items:");</script>';
                                  echo '<script>console.log(' . json_encode($_SESSION["cart_item"]) . ');</script>';
                            }
                        }//end of EMPTY POST QUANTITY
                    break;

                    case "remove":
                        if(!empty($_SESSION["cart_item"])) {
                            $uen = $_GET["uen"];
                            echo "<script>console.log('bookUEN: " . $uen . "');</script>"; // Echo the variable to the console
                            foreach($_SESSION["cart_item"] as $item) {
                                $k = $item["bookUEN"];
                                $v = $item;
                                echo "<script>console.log('k: " . $k . "');</script>"; // Echo the variable to the console
                                echo "<script>console.log('v: " . $v . "');</script>"; // Echo the variable to the console
                                    if($uen == $k)
                                        echo "<script>console.log('Removing item from cart');</script>"; // Echo the variable to the console
                                        unset($_SESSION["cart_item"][$k]);				
                                    if(empty($_SESSION["cart_item"]))
                                        unset($_SESSION["cart_item"]);
                            }
                        }
                    break;
                    case "empty":
                        echo '<script>console.log("Empty Clicked");</script>';
                        unset($_SESSION["cart_item"]);
                    break;
                                
                }//end of switch
            }//end of !empty GET ACTION
            else{
            }
           // $stmt->close();
        }//end of conn else
        $conn->close();

    
?>