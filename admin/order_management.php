<?php
session_start();

// Database configuration
$config; //parse_ini_file('/var/www/private/db-config.ini');

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

// Query to select all columns for users with user_privilege = 'user'
$sql = "
SELECT ord.transactionID, user.userID, user.email, ord.productID, ord.quantity, ord.price 
FROM bookStore.cartTable AS cart
INNER JOIN bookStore.userTable AS user ON cart.userID = user.userID
INNER JOIN bookStore.orderTable AS ord ON cart.productID = ord.productID;";
$result = $conn->query($sql);
?>

  
<div class="container">
        <?php if ($result->num_rows > 0) : ?>
            <table>
                <thead>
                    <tr>
                        <th>Transaction ID</th>
                        <th>Member ID</th>
                        <th>Email</th>                        
                        <th>Product ID</th>
                        <th>Quantity</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()) : ?>
                        <tr>
                            <td><?php echo $row["transactionID"]; ?></td>
                            <td><?php echo $row["userID"]; ?></td>
                            <td><?php echo $row["email"]; ?></td>
                            <td><?php echo $row["productID"]; ?></td>
                            <td><?php echo $row["quantity"]; ?></td>
                            <td><?php echo $row["price"]; ?></td>
                            <td>
                                <form action="/order/order_update.php" method="post">
                                <input type="hidden" name="transactionID" value="<?php echo $row["transactionID"]; ?>">
                                    <input type="hidden" name="productID" value="<?php echo $row["productID"]; ?>">
                                    <input type="submit" value="Edit">
                                </form>
                            </td>
                            <td>
                                <form action="/order/order_delete.php" method="post">
                                    <input type="hidden" name="transactionID" value="<?php echo $row["transactionID"]; ?>">
                                    <input type="hidden" name="productID" value="<?php echo $row["productID"]; ?>">
                                    <input type="submit" value="Delete">
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else : ?>
            <p>No user data found.</p>
    <?php endif; ?>

    <?php $conn->close(); ?>
</div>
  
