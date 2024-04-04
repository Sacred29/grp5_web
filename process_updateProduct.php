<?php
include "inc/head.inc.php";
session_start();
?>






<body>
    <?php
    include "inc/nav.inc.php";
    ?>
    <main>
        <?php
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        $productName = $arrivalDate = $genre = $bookUEN = $price = $bookAuthor = $bookPublisher = $productImage = $errorMsg = $errorMsg2 = $errorMsg3 = "";
        $singleError = true; // Initialize as true
        $fields = array(
            "productName" => "Product Name",
            "arrivalDate" => "Arrival Date",
            "genre" => "Genre",
            "bookUEN" => "Book UEN",
            "price" => "Price",
            "bookAuthor" => "Book Author",
            "bookPublisher" => "Book Publisher",
            // "productImage" => "Product Image",
        );
        $errorMsg = "";
        $success = true;

        foreach ($fields as $field => $fieldname) {
            if (empty($_POST[$field])) {
                if ($singleError) {
                    $errorMsg = $fieldname . " is required.";
                    $singleError = false; // Set to false if multiple fields are missing
                } else {
                    $errorMsg .= "<br>" . $fieldname . " is required.<br>";
                }
                $success = false;
            } else {
                $productName = sanitize_input($_POST["productName"]);
                // Additional check to make sure e-mail address is well-formed.
                $arrivalDate = sanitize_input($_POST["arrivalDate"]);
                $genre = sanitize_input($_POST["genre"]);
                $bookUEN = sanitize_input($_POST["bookUEN"]);
                $price = sanitize_input($_POST["price"]);
                $bookAuthor = sanitize_input($_POST["bookAuthor"]);
                $bookPublisher = sanitize_input($_POST["bookPublisher"]);
                // $productImage = sanitize_input($_POST["productImage"]);
                
            }
        }

        if (isset($_FILES)) {
            $productImage = $_FILES["productImage"]["name"];
            $destination_dir = "./images/";
            $file_name = $_FILES["productImage"]["name"];
            $file_tmp = $_FILES["productImage"]["tmp_name"];
            if(move_uploaded_file($file_tmp, $destination_dir . $file_name)) {
                echo "File uploaded successfully!";
            } else {
                echo "Error uploading file.";
            }
        }


        if ($success) {
            //$hashedPassword = password_hash($pwd, PASSWORD_DEFAULT);
            echo "<h4>Product Update Successful!</h4>";
            echo "<p>Product Name: " . $productName;
            echo "<p>Arrival Date: " . $arrivalDate;
            echo "<p>Genre: " . $genre;
            echo "<p>Price: " . $price;
            echo "<p>Book Author: " . $bookAuthor;
            echo "<p>Book Publisher: " . $bookPublisher;
            echo "<p>Product Image: " . $productImage;
            echo '</br><a href="/products.php" class="btn btn-success">Return to Products Page</a><br><br>';
            saveProductToDB();
        } else {
            echo "<h4>The following errors were detected:</h4>";
            echo "<p>" . $errorMsg2 . "</p>";
            echo "<p>" . $errorMsg . "</p>";
        }


        /*
* Helper function that checks input for malicious or unwanted content.
*/
        function sanitize_input($data)
        {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
            return $data;
        }

        /*
    Helper function to write member data to database
    Function --> retrieve database login from config
    --> how to write to dabase using PHP oo MySSQLi
*/

        function saveProductToDB()
        {
            global $productName, $arrivalDate, $genre, $bookUEN, $price, $bookAuthor, $bookPublisher, $productImage, $errorMsg, $success;

            $productImage = "/images/" . $productImage;

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


            //check connection
            if ($conn->connect_error) {
            } else {
                if ($_SERVER['REQUEST_METHOD']  == 'POST') {
                    if (isset($_GET['id'])) {
                        $id = $_GET['id'];
                        //Prepare statement
                        //Bind and execute query statement
                        echo "<script>console.log($id)</script>";
                        $stmt = $conn->prepare("UPDATE productTable SET productName = ?, arrivalDate = ?, productGenre = ?, bookUEN = ?, price = ?, bookAuthor = ?, bookPublisher = ?,  productImage = ? WHERE productID = $id");
                        $stmt->bind_param("ssssdsss", $productName, $arrivalDate, $genre, $bookUEN, $price, $bookAuthor, $bookPublisher, $productImage);

                        //$stmt = $conn->prepare("INSERT INTO world_of_pets_members (fname, lname, email, password) VALUES ('jane','doe','jane@abc.com','123')");

                        if (!$stmt->execute()) {
                            $errorMsg = "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
                            $success = false;
                        }
                        $stmt->close();
                    }
                    $conn->close();
                }
            }
        }
        ?>
    </main>
    <?php
    include "inc/footer.inc.php";
    ?>
</body>

</html>