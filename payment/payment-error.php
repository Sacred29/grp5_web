<?php
include "../inc/head.inc.php";
?>

<?php
session_start();

include "../inc/nav.inc.php";

require './../vendor/autoload.php';

// Check if the user is logged in
if (!isset($_SESSION['user_privilege'])) {
  // Redirect to login page if not logged in
  header('Location: login.php');
  exit;
}
?>

<head>
  <title>Payment error</title>
</head>

<body>
  <h1>Oops! An error occurred during payment.</h1>
  <p>
    Please try again later.
  </p>
</body>
<?php
include "../inc/footer.inc.php";
?>

</html>