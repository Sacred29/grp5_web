<?php
    include "inc/head.inc.php";
?>

<body>
    <?php
        session_start();
        include "inc/nav.inc.php";
    ?>
    <main class="container">
        <h1> Checkout </h1>
        <?php
            if (isset($_SESSION["cart_item"])){
                $cartItems = $_SESSION["cart_item"];
                $total_quantity = 0;
                $total_price = 0;
                echo "<script> console.log('Cart Items: " . json_encode($cartItems) . "');  </script>";
                
                ?>

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
                    foreach ($cartItems as $uen => $item){
                        $item_price = $item["quantity"]*$item["price"];

                 
                ?>

            <tr>
				<td style="border-top: 1px solid black; border-bottom: 1px solid black;"><img src="<?php echo $item["productImage"]; ?>" class="cart-item-image" /><?php echo $item["productName"]; ?></td>
                <td  style="text-align:center; border-top: 1px solid black; border-bottom: 1px solid black;"><?php echo "$ ". $item["price"]; ?></td>
				<td style="text-align:center; border-top: 1px solid black; border-bottom: 1px solid black;"><?php echo $item["quantity"]; ?></td>
				<td  style="text-align:center; border-top: 1px solid black; border-bottom: 1px solid black;"><?php echo "$ ". number_format($item_price,2); ?></td>
				<td style="text-align:center; border-top: 1px solid black; border-bottom: 1px solid black;"><a href="cart.php?action=remove&uen=<?php echo $item["bookUEN"]; ?>" class="btnRemoveAction"><img src="images/icon-delete.png" alt="Remove Item" /></a></td>
			</tr>

            <?php
                $total_quantity += $item["quantity"];
                $total_price += ($item["price"]*$item["quantity"]);
                } //end of foreach
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


            }
        ?>
        <br>
        <form action="process_product.php" method="post">
        <div class="mb-3" style="padding-bottom:20px;">
            <button type="submit" style="float:right;">Confirm Checkout</button>
        </div>
        </form>
    </main>
    <?php
    include "inc/footer.inc.php";
    ?>
</body>
