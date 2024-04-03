<!DOCTYPE html>
<html lang="en">

<head>
    <title>Create Review</title>
    <?php
    include "inc/head.inc.php";
    session_start();
    ?>
</head>


<body>
    <?php
    include "inc/nav.inc.php";
    ?>
    <main>
        <?php
        $books = [];
        $productID = $_GET("id");
        $userID = $_SESSION('userID');
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        $productID = $userID = $userReview = $userRating = $errorMsg = $errorMsg2 = $errorMsg3 = "";
        $singleError = true; // Initialize as true
        $fields = array(
            "productID" => "Product ID",
            "userID" => "User ID",
            "userReview" => "User Review",
            "userRating" => "User Rating"

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

                $productID = sanitize_input($_POST[$productID]);
                $userID = sanitize_input($_POST[$userID]);
                $userReview = sanitize_input($_POST['userReview']);
                $userRating = sanitize_input($_POST['userRating']);
                // Additional check to make sure e-mail address is well-formed.

            }
        }


        if ($success) {
            //$hashedPassword = password_hash($pwd, PASSWORD_DEFAULT);
            echo "<h4>Product Registration Successful!</h4>";
            echo "<p>Product ID: " . $productID;
            echo "<p>User ID: " . $userID;
            echo "<p>User Review: " . $userReview;
            echo "<p>User Rating: " . $userRating;
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
            global $productID, $userID, $userReview, $userRating, $errorMsg, $success;


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
                    //Prepare statement
                    //Bind and execute query statement
                    $stmt = $conn->prepare("INSERT INTO reviewTable (productID, userID, userReview, userRating) VALUES (?,?,?,?)");
                    $stmt->bind_param("ddsd", $productID, $userID, $userReview, $userReview);

                    //$stmt = $conn->prepare("INSERT INTO world_of_pets_members (fname, lname, email, password) VALUES ('jane','doe','jane@abc.com','123')");

                    if (!$stmt->execute()) {
                        $errorMsg = "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
                        $success = false;
                    }
                    $stmt->close();
                }
                $conn->close();
                header('Location: productDetails.php?id=' . $productID);
            }
        }
        ?>
    </main>
    <?php
    include "inc/footer.inc.php";
    ?>
</body>

</html>