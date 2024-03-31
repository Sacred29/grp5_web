<?php
session_start();
include "inc/head.inc.php";
?>

<body>

    <?php
    include "inc/nav.inc.php";

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    $pwd = $pwd_cur = $pwd_new = $pwd_confirm = "";

    $singleError = true; // Initialize as true
    $fields = array(
        "pwd_cur" => "Current password",
        "pwd_new" => "New password",
        "pwd_confirm" => "Password confirmation",
    );
    $errorMsg = "";
    $success = true;

    $pwd_cur = $_POST['pwd_cur'];
    $pwd_new = $_POST['pwd_new'];
    $pwd_confirm = $_POST['pwd_confirm'];


    // session userid not set means not logged in
    if (!isset($_SESSION["userID"])) {
        $errorMsg .= "<br>Please log in to change password!<br>";

        $success = false;
    } else {
        foreach ($fields as $field => $fieldname) {
            if (empty($_POST[$field])) {
                if ($singleError) {
                    $errorMsg = $fieldname . " is required.";
                    $singleError = false; // Set to false if multiple fields are missing
                } else {
                    $errorMsg .= "<br>" . $fieldname . " is required.<br>";
                }
                $success = false;
            }
        }
        if ($pwd_new !== $pwd_confirm) {
            $errorMsg .= "Passwords do not match.";
            $success = false;
        } else {
            $pwd = password_hash($_POST["pwd_new"], PASSWORD_DEFAULT);
        }
    }

    if ($success) {
        //$hashedPassword = password_hash($pwd, PASSWORD_DEFAULT);
        savePasswordChangeToDB();
        if ($success) {
            echo "<h4>Password changed successfully!</h4>";
        } else {
            echo "<h4>The following errors were detected:</h4>";
            echo "<p>" . $errorMsg2 . "</p>";
            echo "<p>" . $errorMsg . "</p>";
        }
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

    function savePasswordChangeToDB()
    {
        global $pwd, $errorMsg, $success;
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
            $config['servername'],
            $config['username'],
            $config['password'],
            $config['dbname']
        );

        if ($conn->connect_error) {
            $errorMsg = "Connection failed: " . $conn->connect_error;
            $success = false;
        } else {
            // verify current password
            $stmt = $conn->prepare("SELECT * FROM userTable WHERE userID = ?");
            $stmt->bind_param("s", $_SESSION["userID"]);

            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                //email field is unique --> only one row in result set
                $row = $result->fetch_assoc();
                //assign the value of user's first name, last name and password to the respective variables
                $pwd_current = $row["password"];

                //check if password matches
                if (!password_verify($_POST["pwd_cur"], $pwd_current)) {
                    $errorMsg = "Current password does not match...";
                    $success = false;
                } else {
                    //Prepare statement
                    //Bind and execute query statement
                    $stmt = $conn->prepare("UPDATE userTable SET password = ? WHERE userID = ?");
                    $stmt->bind_param("ss", $pwd,  $_SESSION["userID"]);

                    if (!$stmt->execute()) {
                        $errorMsg = "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
                        $success = false;
                    }
                }
            } else {
                $errorMsg = "Email not found or password does not match...";
                $success = false;
            }



            $stmt->close();

            $conn->close();
        }
    }
    ?>

    <?php
    include "inc/footer.inc.php";
    ?>
</body>