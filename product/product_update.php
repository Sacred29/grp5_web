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

$productID = isset($_POST['productID']) ? $conn->real_escape_string($_POST['productID']) : null;

// Fetch user data if member_id is set
if ($productID) {
    $sql = "SELECT * FROM bookStore.productTable WHERE productID = '$productID'";
    $result = $conn->query($sql);
    $product = $result->fetch_assoc();
}

// Check if form has been submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    // Get form data
    $productName = $conn->real_escape_string($_POST['productName']);
    $bookUEN = $conn->real_escape_string($_POST['bookUEN']);
    $productGenre = $conn->real_escape_string($_POST['productGenre']);
    $arrivalDate = $conn->real_escape_string($_POST['arrivalDate']);
    $price = $conn->real_escape_string($_POST['price']);
    $bookAuthor = $conn->real_escape_string($_POST['bookAuthor']);
    $bookPublisher = $conn->real_escape_string($_POST['bookPublisher']);
    $productImage = $conn->real_escape_string($_POST['productImage']);
    //$productImage1 = $conn->real_escape_string($_POST['productImage1']);
    
    if (isset($_POST['productImage1'])) {
        $productImage = "/images/" . $_FILES["productImage1"]["name"];

        $destination_dir = "/images/";
        $file_name = $_FILES["productImage1"]["name"];
        $file_tmp = $_FILES["productImage1"]["tmp_name"];
        if(move_uploaded_file($file_tmp, $destination_dir . $file_name)) {
            echo "File uploaded successfully!";
        } else {
            echo "Error uploading file.";
        }
    }
    

    // Update user data
    $updateSql = "UPDATE bookStore.productTable SET productName='$productName', bookUEN='$bookUEN', productGenre='$productGenre', arrivalDate='$arrivalDate', price='$price', bookAuthor='$bookAuthor',bookPublisher='$bookPublisher', productImage='$productImage' WHERE productID='$productID'";

    if ($conn->query($updateSql) === TRUE) {
        header("Location: /admin/management.php");
        exit;
    } else {
        echo "Error updating product: " . $conn->error;
        header("Location: /admin/management.php");
        exit;
    }
} else {
    // Display form
    if (isset($product)) {
?>

        <!DOCTYPE html>
        <html lang="en">

        <head>
            <title>Edit User</title>
        </head>

        <body>
            <h1>Edit User</h1>
            <form action="" method="post" enctype="multipart/form-data" >
                <input type="hidden" name="productID" value="<?php echo $product['productID']; ?>">
                <div>
                    <label>Product Name</label>
                    <input type="text" name="productName" value="<?php echo $product['productName']; ?>">
                </div>
                <div>
                    <label>UEN</label>
                    <input type="number" name="bookUEN" value="<?php echo $product['bookUEN']; ?>">
                </div>
                <div>
                    <label>Genre</label>
                    <input type="text" name="productGenre" value="<?php echo $product['productGenre']; ?>">
                </div>
                <div>
                    <label>Arrival Date</label>
                    <input type="date" name="arrivalDate" value="<?php echo $product['arrivalDate']; ?>">
                </div>
                <div>
                    <label>Price</label>
                    <input type="number" step="any" name="price" value="<?php echo $product['price']; ?>">
                </div>
                <div>
                    <label>Author</label>
                    <input type="text" name="bookAuthor" value="<?php echo $product['bookAuthor']; ?>">
                </div>
                <div>
                    <label>Publisher</label>
                    <input type="text" name="bookPublisher" value="<?php echo $product['bookPublisher']; ?>">
                </div>
                <div>
                    <label>Image</label>
                    <input type="hidden" name="productImage" value="<?php echo $product['productImage']; ?>">
                    <input type="file" name="productImage1"  id="productImage1" >
                </div>
                
                <button type="submit" name="update">Update Product</button>
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