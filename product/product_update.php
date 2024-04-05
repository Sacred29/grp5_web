<?php
session_start();

include "./../inc/head.inc.php";
include "./../inc/nav.inc.php";

function sanitize_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

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

/// Check if form has been submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    // Initialize variables to avoid undefined index warnings
    $productName = isset($_POST['productName']) ? sanitize_input($_POST['productName']) : '';
    $bookUEN = isset($_POST['bookUEN']) ? sanitize_input($_POST['bookUEN']) : '';
    $productGenre = isset($_POST['productGenre']) ? sanitize_input($_POST['productGenre']) : '';
    $arrivalDate = isset($_POST['arrivalDate']) ? sanitize_input($_POST['arrivalDate']) : '';
    $price = isset($_POST['price']) ? sanitize_input($_POST['price']) : '';
    $bookAuthor = isset($_POST['bookAuthor']) ? sanitize_input($_POST['bookAuthor']) : '';
    $bookPublisher = isset($_POST['bookPublisher']) ? sanitize_input($_POST['bookPublisher']) : '';
    
    // Handling file upload
    $productImage = isset($product['productImage']) ? $product['productImage'] : ''; // Default to existing image if available
    if (isset($_FILES['productImage1']) && $_FILES['productImage1']['error'] == 0) {
        $productImage = "/images/" . basename($_FILES["productImage1"]["name"]);
        $destination_dir = "./../images/" . basename($_FILES["productImage1"]["name"]);
        if (!move_uploaded_file($_FILES["productImage1"]["tmp_name"], $destination_dir)) {
            echo "Error uploading file.";
        }
    }
    

    if ($productID) {
        $updateSql = "UPDATE bookStore.productTable SET productName=?, bookUEN=?, productGenre=?, arrivalDate=?, price=?, bookAuthor=?, bookPublisher=?, productImage=? WHERE productID=?";
        
        // Prepared statement to avoid SQL injection
        if ($stmt = $conn->prepare($updateSql)) {
            $stmt->bind_param("ssssdsssi", $productName, $bookUEN, $productGenre, $arrivalDate, $price, $bookAuthor, $bookPublisher, $productImage, $productID);
            if ($stmt->execute()) {
                $_SESSION['message'] = 'Product updated successfully.';
                header("Location: ./../admin/management.php");
                exit;
            } else {
                $_SESSION['error'] = "Error updating product: " . $conn->error;
            }
            $stmt->close();
        } else {
            $_SESSION['error'] = "Error updating product: " . $conn->error;
        }
    }
} else {
    // Display form
    if (isset($product)) {
?>

        

   
            <main class="container">
            <h1><?php echo isset($product) ? "Edit Product" : "Product Registration"; ?></h1>

            <form action="<?php echo isset($product) ? '' : '/process_productRegister.php'; ?>" method="post" enctype="multipart/form-data">
                <?php if (isset($product)): ?>
                    <input type="hidden" name="productID" value="<?php echo htmlspecialchars($product['productID']); ?>">
                <?php endif; ?>
                
                <div class="mb-3">
                    <label for="productName" class="form-label">Product Name</label>
                    <input maxlength="45" type="text" id="productName" name="productName" class="form-control" value="<?php echo isset($product) ? htmlspecialchars($product['productName']) : ''; ?>" placeholder="Input Product Name">
                </div>
                
                <div class="mb-3">
                    <label for="arrivalDate" class="form-label">Arrival Date</label>
                    <input required type="date" id="arrivalDate" name="arrivalDate" class="form-control" value="<?php echo isset($product) ? htmlspecialchars($product['arrivalDate']) : ''; ?>" placeholder="Select Date">
                </div>
                
                <div class="mb-3">
                    <label for="productGenre" class="form-label">Product Genre</label>
                    <select name="productGenre" id="productGenre" class="form-control">
                        <option value="Fiction" <?php echo isset($product) && $product['productGenre'] == 'Fiction' ? 'selected' : ''; ?>>Fiction</option>
                        <option value="Non-Fiction" <?php echo isset($product) && $product['productGenre'] == 'Non-Fiction' ? 'selected' : ''; ?>>Non-Fiction</option>
                        <option value="Educational" <?php echo isset($product) && $product['productGenre'] == 'Educational' ? 'selected' : ''; ?>>Educational</option>
                        <option value="Self-Help" <?php echo isset($product) && $product['productGenre'] == 'Self-Help' ? 'selected' : ''; ?>>Self-Help</option>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label for="bookUEN" class="form-label">Product UEN:</label>
                    <input required type="text" id="bookUEN" name="bookUEN" class="form-control" value="<?php echo isset($product) ? htmlspecialchars($product['bookUEN']) : ''; ?>" placeholder="Input Book UEN">
                </div>
                
                <div class="mb-3">
                    <label for="price" class="form-label">Price: </label>
                    <input required type="text" id="price" name="price" class="form-control" value="<?php echo isset($product) ? htmlspecialchars($product['price']) : ''; ?>" placeholder="Input Price">
                </div>
                
                <div class="mb-3">
                    <label for="bookAuthor" class="form-label">Product Author:</label>
                    <input required type="text" id="bookAuthor" name="bookAuthor" class="form-control" value="<?php echo isset($product) ? htmlspecialchars($product['bookAuthor']) : ''; ?>" placeholder="Input Product Author">
                </div>
                
                <div class="mb-3">
                    <label for="bookPublisher" class="form-label">Product Publisher:</label>
                    <input required type="text" id="bookPublisher" name="bookPublisher" class="form-control" value="<?php echo isset($product) ? htmlspecialchars($product['bookPublisher']) : ''; ?>" placeholder="Input Product Publisher">
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Select Product Image:</label>
                    <input type="hidden" id="productImage" name="productImage" >
                    <input type="file" id="productImage1" name="productImage1" class="form-control" >
                    <?php if (isset($product) && $product['productImage']): ?>
                        <img src="<?php echo htmlspecialchars($product['productImage']); ?>" alt="Current Image" style="max-width: 200px; max-height: 200px;">
                    <?php endif; ?>
                </div>
                
                <div class="mb-3">
                    <button type="submit" name="update" class="btn btn-primary"><?php echo isset($product) ? "Update Product" : "Submit"; ?></button>
                    <a href="/admin/management.php" class="btn btn-secondary">Back</a>
                </div>
            </form>
    </main>
<?php
    } else {
        echo "No user found with the provided ID.";
    }
}
$conn->close();
?>