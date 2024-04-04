<?php
session_start();
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

$cartID = isset($_POST['cartID']) ? $conn->real_escape_string($_POST['cartID']) : null;

// Fetch user data if member_id is set
if ($cartID) {
    $sql = "SELECT * FROM bookStore.cartTable WHERE cartID = '$cartID'";
    $result = $conn->query($sql);
    $cart = $result->fetch_assoc();
}

// Check if form has been submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    // Get form data
    $productID = $conn->real_escape_string($_POST['productID']);
    $quantity = $conn->real_escape_string($_POST['quantity']);
    $price = $conn->real_escape_string($_POST['price']);
    

    // Update user data
    $updateSql = "UPDATE bookStore.cartTable SET productID='$productID', quantity='$quantity', price='$price' WHERE cartID='$cartID'";

    if ($conn->query($updateSql) === TRUE) {
        header("Location: /admin/management.php");
        exit;
    } else {
        echo "Error updating user: " . $conn->error;
        header("Location: /admin/management.php");
        exit;
    }
} else {
    // Display form
    if (isset($cart)) {
?>

        <!DOCTYPE html>
        <html lang="en">

        <head>
            <title>Edit User</title>
        </head>

        <body>
            <h1>Edit User</h1>
            <form action="" method="post">
                <input type="hidden" name="cartID" value="<?php echo $cart['cartID']; ?>">
                <div>
                    <label>ProductID</label>
                    <input type="number" name="productID" value="<?php echo $cart['productID']; ?>">
                </div>
                <div>
                    <label>Quantity</label>
                    <input type="number" name="quantity" value="<?php echo $cart['quantity']; ?>">
                </div>
                <div>
                    <label>Price</label>
                    <input type="number" step="any" name="price" value="<?php echo $cart['price']; ?>">
                </div>
                <button type="submit" name="update">Update Cart</button>
                <button type="button" onclick="location.href='/admin/management.php'">Back</button>
            </form>
        </body>

        </html>
<?php
    } else {
        echo "No user found with the provided ID.";
    }
}
$conn->close();
?>