<?php
include "../inc/head.inc.php";
?>

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
if (isset($_GET['session_id']) && !empty($_SESSION["cart_item"])) {
  $stripe = new \Stripe\StripeClient(getenv("STRIPE_PRIV")); // private api key. need store in envvars.

  try {
    $session = $stripe->checkout->sessions->retrieve($_GET['session_id']);
    $transaction_id = $session->metadata->values()[0];
    $paid_amount = ($session->amount_total) / 100;

    //create db connection
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
      getenv('SERVERNAME'),
      getenv('DB_USERNAME'),
      getenv('DB_PASSWORD'),
      getenv('DBNAME')
    );

    if ($conn->connect_error) {
      $errorMsg = "Connection failed: " . $conn->connect_error;
      $success = false;
    } else {

      //check connection
      if ($conn->connect_error) {
      } else {
        $date = date('Y-m-d H:i:s');
        //Prepare statement

        // create transaction item
        $stmt = $conn->prepare("INSERT INTO transactionTable (userID, orderStatus, orderDate, totalPrice, orderPaid) VALUES(?, 'Pending', ?, ?, ?)");
        $stmt->bind_param("ssss", $_SESSION["userID"], $date, $paid_amount, $paid_amount);

        if (!$stmt->execute()) {
          $errorMsg = "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
          $success = false;
        } else {
          $transaction_id = $stmt->insert_id;

          // insert order items
          foreach ($_SESSION["cart_item"] as $item) {
            $stmt = $conn->prepare("INSERT INTO orderTable (productID, transactionID, price, quantity)" .
              " VALUES((SELECT productID from productTable WHERE bookUEN = ?), ?, ?, ?)");
            $stmt->bind_param("ssdd", $item["bookUEN"], $transaction_id, $item["price"], $item["quantity"]);

            if (!$stmt->execute()) {
              $errorMsg = "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
              $success = false;
            }
          }
          unset($_SESSION["cart_item"]); //clear session cart
        }
        $stmt->close();
      }
      $conn->close();
    }

    http_response_code(200);
  } catch (Error $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
  }
} else {
  echo "No checkout session ID was specified.";
}
?>


<head>
  <title>Thanks for your order!</title>
</head>

<body>
  <h1>Thanks for your order!</h1>
  <p>
    We appreciate your business!
    If you have any questions, please email
    <a href="mailto:admin@thedaniel.life">admin@thedaniel.life</a>.
  </p>
</body>
<?php
include "../inc/footer.inc.php";
?>

</html>