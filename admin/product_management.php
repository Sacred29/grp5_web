<!DOCTYPE html>
<html lang="en">
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
$sql = "SELECT * FROM bookStore.productTable;";
$result = $conn->query($sql);
?>

  
<div class="container">
        <?php if ($result->num_rows > 0) : ?>
            <table>
                <thead>
                    <tr>                        
                        <th>Product ID</th>
                        <th>Product Name</th>
                        <th>Book UEN</th>
                        <th>Product Genre</th>
                        <th>Arrival Date</th>
                        <th>Price</th>
                        <th>Author</th>
                        <th>Publisher</th>
                        <th>Image</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()) : ?>
                        <tr>
                            <td><?php echo $row["productID"]; ?></td>
                            <td><?php echo $row["productName"]; ?></td>
                            <td><?php echo $row["bookUEN"]; ?></td>
                            <td><?php echo $row["productGenre"]; ?></td>
                            <td><?php echo $row["arrivalDate"]; ?></td>
                            <td><?php echo $row["price"]; ?></td>
                            <td><?php echo $row["bookAuthor"]; ?></td>
                            <td><?php echo $row["bookPublisher"]; ?></td>
                            <td><?php echo $row["productImage"]; ?></td>
                            <td>
                                <form action="/product/product_update.php" method="post">
                                    <input type="hidden" name="productID" value="<?php echo $row["productID"]; ?>">
                                    <input type="submit" value="Edit">
                                </form>
                            </td>
                            <td>
                                <form action="/product/product_delete.php" method="post">
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
</html>
  
