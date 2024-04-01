<?php
    include "inc/head.inc.php";
?>

<body>
    <?php
        include "inc/nav.inc.php";
        session_start();
    ?>
    <main class="container">
        <h1> Checkout </h1>
        <?php
            if (isset($_SESSION["cart_item"])){
                $cartItems = $_SESSION["cart_item"];
                echo "<script> console.log('Cart Items: " . json_encode($cartItems) . "');  </script>";
                foreach ($cartItems as $uen => $item){

                ?>
                    <div class="container-md" id="checkout-item-container">
                    <table class="tbl-cart" cellpadding="10" cellspacing="1">
                        <tbody>
                            <tr>
                                <td><img src="<?php echo $item["productImage"]; ?>" class="checkout-item-image"/></td>
                                <td><strong><span class="checkout-item-title"><?php echo $item["productName"]; ?> By <?php echo $item["bookAuthor"];?></span></strong></td>;
                                <!-- // <td class="td-test"><span class="checkout-item-author">By <?php echo $item["bookAuthor"];?></span></td> -->
                                <td><span class="checkout-quantity"> Quantity: <?php echo $item["quantity"]; ?></td>
                                <td><span class="checkout-item-price"> Price: <?php echo $item["price"]; ?></td>
                                <br>

                            </tr>
                        </tbody>
                        </table>
                    </div>
                <?php

                }
            }
        ?>
        <br>
        <form action="process_product.php" method="post">
        <div class="mb-3" style="padding-bottom:20px;">
            <button type="submit" style="float:right;">Checkout</button>
        </div>
        </form>
    </main>
    <?php
    include "inc/footer.inc.php";
    ?>
</body>
