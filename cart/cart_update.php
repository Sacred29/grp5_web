<?php
session_start();
include "./../inc/head.inc.php";
include "./../inc/nav.inc.php";
// Database configuration
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

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle actions
if (!empty($_GET["action"])) {
    switch ($_GET["action"]) {
        // Update item quantity in cart
        case "update":
            if (!empty($_POST["quantity"]) && isset($_SESSION["cart_item"][$_GET["uen"]])) {
                $_SESSION["cart_item"][$_GET["uen"]]["quantity"] = $_POST["quantity"];
            }
            break;
        // Remove item from cart
        case "remove":
            if (!empty($_SESSION["cart_item"]) && isset($_SESSION["cart_item"][$_GET["uen"]])) {
                unset($_SESSION["cart_item"][$_GET["uen"]]);
            }
            break;
    }
    header("Location: cart_update.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Cart</title>
    <!-- Add any required CSS or JS links here -->
</head>
<body>
<main class="container">
        <h1>Shopping Cart</h1>
        <div id="shopping-cart">
            <?php
            // Assuming $_SESSION["cart_item"] has been initialized and managed appropriately
            if (!empty($_SESSION["cart_item"])) {
                $total_quantity = 0;
                $total_price = 0;
                ?>
                <table class="tbl-cart" cellpadding="10" cellspacing="1">
                    <tbody>
                        <tr>
                            <th style="text-align:left;">Name</th>
                            <th style="text-align:center;" width="10%">Unit Price</th>
                            <th style="text-align:center;" width="10%">Quantity</th>
                            <th style="text-align:center;" width="10%">Price</th>
                            <th style="text-align:center;" width="5%">Remove</th>
                        </tr>
                        <?php
                        foreach ($_SESSION["cart_item"] as $item) {
                            $item_price = $item["quantity"] * $item["price"];
                            ?>
                            <tr>
                                <td>
                                    <img src="<?php echo $item["productImage"]; ?>" class="cart-item-image"/>&nbsp;
                                    <?php echo $item["productName"]; ?>
                                </td>
                                <td style="text-align:center;">
                                    <?php echo "$ " . $item["price"]; ?>
                                </td>
                                <td style="text-align:center;">
                                    <form action="cart_update.php?action=update&uen=<?php echo $item["bookUEN"]; ?>" method="post" class="form-update">
                                        <input type="number" name="quantity" value="<?php echo $item["quantity"]; ?>" min="1" class="quantity-field narrow" />
                                        <input type="submit" value="Update" class="btn-update" />
                                    </form>
                                </td>
                                <td style="text-align:center;">
                                    <?php echo "$ " . number_format($item["quantity"] * $item["price"], 2); ?>
                                </td>
                                <td style="text-align:center;">
                                    <a href="cart_update.php?action=remove&uen=<?php echo $item["bookUEN"]; ?>" class="btn-remove">
                                        <img src="images/icon-delete.png" alt="Remove Item" />
                                    </a>
                                </td>
                            </tr>
                            <?php
                            $total_quantity += $item["quantity"];
                            $total_price += ($item["price"] * $item["quantity"]);
                        }
                        ?>
                        <tr>
                            <td colspan="2" align="right" style="font-weight:bold; padding-right:30px; padding-top:10px;">Total:</td>
                            <td align="right" style="font-weight:bold; padding-right:50px; padding-top:10px;"><?php echo $total_quantity; ?></td>
                            <td align="right" colspan="2" style="font-weight:bold; padding-right:75px; padding-top:10px;"><strong><?php echo "$ " . number_format($total_price, 2); ?></strong></td>
                        </tr>
                    </tbody>
                </table>
                <?php
            } else {
                echo "<div>Your Cart is Empty</div>";
            }
            ?>
            <a id="btnRedirect" class="btn btn-primary" onclick="confirmCheckout()">Check Out</a>
            <a id="btnEmpty" class="btn btn-danger" href="/cart_update.php?action=empty">Empty Cart</a>
        </div>
    </main>
<div class="cart-action">
    <a href="index.php" class="btnContinue">Continue Shopping</a>
</div>
</body>
</html>
