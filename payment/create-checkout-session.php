<?php
include "../inc/head.inc.php";
?>

<body>
  <?php
  include "../inc/nav.inc.php";
  require './../vendor/autoload.php';


  session_start();
  // Check if the user is logged in
  if (!isset($_SESSION['user_privilege'])) {
    // Redirect to login page if not logged in
    header('Location: login.php');
    exit;
  }

  $order_books = [];

  $lineItems = array();

  if (!empty($_SESSION["cart_item"])) {
    foreach ($_SESSION["cart_item"] as $item) {
      // Access data using column names

      $product_data = array(
        'name' => $item["productName"],
      );

      $price_data = array(
        'currency' => 'sgd',
        'unit_amount' => $item["price"] * 100,
        'product_data' => $product_data
      );

      $lineitem = array(
        'quantity' => $item["quantity"],
        'price_data' => $price_data
      );
      $lineItems[] = $lineitem;
    }

    $stripe = new \Stripe\StripeClient(getenv("STRIPE_PRIV")); // private api key. need store in envvars.

    $checkout_session = $stripe->checkout->sessions->create([
      'line_items' => $lineItems,
      'metadata' => ['transaction_id' => $_GET["transactionID"]],
      'mode' => 'payment',
      'success_url' => 'https://thedaniel.life/payment/payment-success.php?session_id={CHECKOUT_SESSION_ID}', // these need to be live on vm. Does not work on local
      'cancel_url' => 'https://thedaniel.life/payment-cancel.php', // these need to be live on vm. Does not work on local
    ]);

    header("HTTP/1.1 303 See Other");
    header("Location: " . $checkout_session->url);
  } else {
    echo "There are no items in the cart for payment!";
  }


  $conn->close();

  ?>
</body>