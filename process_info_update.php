<?php
session_start();
include "inc/head.inc.php";
?>

<body>
    <?php
    include "inc/nav.inc.php";
    include "inc/header.inc.php";

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    $fname = $lname = $email = $pwd = $errorMsg = $errorMsg2 = $errorMsg3 = "";
    $is_bot = true;
    $userPrivilege = "user";

    $singleError = true; // Initialize as true
    $fields = array(
        "fname" => "First name",
        "lname" => "Last name",
        "email" => "Email",
    );
    $errorMsg = "";
    $success = true;

    // session userid not set means not logged in
    if (!isset($_SESSION["userID"])) {
        $errorMsg .= "Please log in!";
        $success = false;
    }

    if ($success) {
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
                $email = sanitize_input($_POST["email"]);
                // Additional check to make sure e-mail address is well-formed.
                $lname = sanitize_input($_POST["lname"]);
                $fname = sanitize_input($_POST["fname"]);

                if ($field === "email" && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $errorMsg2 .= "Invalid email format.";
                    $success = false;
                }
            }
        }

        if (!preg_match('/^[a-zA-Z\-\' ]+$/', $lname)) {
            $errorMsg .= "Invalid last name format.";
            $success = false;
        }
    }

    if ($success) {
        //$hashedPassword = password_hash($pwd, PASSWORD_DEFAULT);
        echo "<h4>User details update successful!</h4>";
        echo "<p>Email: " . $email;
        echo "<p>First name: " . $fname;
        echo "<p>Last name: " . $lname;
        updateMemberInDB();
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

    function updateMemberInDB()
    {
        global $fname, $lname, $email, $pwd, $userPrivilege, $errorMsg, $success;
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
                $stmt = $conn->prepare("UPDATE userTable SET fName = ?, lName = ?, email = ? WHERE userID = ?");
                $stmt->bind_param("ssss", $fname, $lname, $email, $_SESSION["userID"]);

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
    ?>
    <?php
    include "inc/footer.inc.php";
    ?>
</body>