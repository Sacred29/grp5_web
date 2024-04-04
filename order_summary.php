<!DOCTYPE html>
<?php
include "inc/head.inc.php";
?>

<body>
    <?php
    include "inc/nav.inc.php";
    session_start();
    // Check if the user is logged in
    if (!isset($_SESSION['user_privilege'])) {
        // Redirect to login page if not logged in
        header('Location: /login/login.php');
        exit;
    }
    ?>
    <main class="container">
        <h1> Order summary </h1>
        <?php
        $total_quantity = 0;
        $total_price = 0;

        $order_books = [];
        if (!empty($_SESSION["cart_item"])) {
            // Loop through the rows and fetch the data
            foreach ($_SESSION["cart_item"] as $item) {
                $order_books[] = $item;
            }
        } else {
            echo "The cart is empty";
        }

        ?>
        <div class="overflow-auto">
            <table class="tbl-cart" cellpadding="10" cellspacing="1">
                <tbody>
                    <tr>
                        <th style="text-align:left;">Name</th>
                        <!--<th style="text-align:center;">Code</th>-->
                        <th style="text-align:center;" width="10%">Unit Price</th>
                        <th style="text-align:center;" width="10%">Quantity ordered</th>
                        <th style="text-align:center;" width="10%">Price</th>
                    </tr>
                    <?php
                    foreach ($order_books as $item) {
                        $item_price = $item["quantity"] * $item["price"];
                    ?>
                        <tr>
                            <td style="border-top: 1px solid black; border-bottom: 1px solid black;">
                                <img src="<?php echo $item["productImage"]; ?>" class="cart-item-image" /><?php echo $item["productName"]; ?>
                            </td>
                            <td style="text-align:center; border-top: 1px solid black; border-bottom: 1px solid black;"><?php echo "$ " . $item["price"]; ?></td>
                            <td style="text-align:center; border-top: 1px solid black; border-bottom: 1px solid black;"><?php echo $item["quantity"]; ?></td>

                            <td style="text-align:center; border-top: 1px solid black; border-bottom: 1px solid black;"><?php echo "$ " . number_format($item_price, 2); ?></td>

                        </tr>

                    <?php
                        $total_quantity += $item["quantity"];
                        $total_price += ($item["price"] * $item["quantity"]);
                    } //end of foreach
                    ?>
                    <tr>
                        <td colspan="2" align="right" style="display:table-cell; font-weight:bold; padding-right:30px; padding-top:10px;">Total:</td>
                        <td align="right" style="display:table-cell; font-weight:bold; padding-right:50px; padding-top:10px;"><?php echo $total_quantity; ?></td>
                        <td align="right" colspan="2" style="display:table-cell; font-weight:bold; padding-right:75px; padding-top:10px;"><strong><?php echo "$ " . number_format($total_price, 2); ?></strong></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <br>
        <?php
        if (!empty($_SESSION["cart_item"])) {
        ?>
            <form action="/payment/create-checkout-session.php" method="post">
                <div class="mb-3" style="padding-bottom:20px;">
                    <button type="submit" style="float:right;">Proceed to Payment</button>
                </div>
            </form>
        <?php
        }
        ?>

    </main>
    <?php
    include "inc/footer.inc.php";
    ?>
</body>
<style>
    .overflow-auto {
        overflow: auto;
        max-height: 50%;
    }
</style>
</html>