<?php
session_start();
include "inc/head.inc.php";
?>

<body>

    <?php
    include "inc/nav.inc.php";

    //query database for given user
    //validate user input - check if user input or if password doesn't match
    //notify the user if the above happens

    //if invalid input --> display error
    //if successful --> display user info
    $fname = $lname = $email = $pwd = $errorMsg = "";
    $success = true;

    if (empty($_POST["email"])) {
        $errorMsg .= "Email is required.<br>";
        $success = false;
    } else {
        $email = sanitize_input($_POST["email"]);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errorMsg .= "Invalid email format.";
            $success = false;
        }
    }

    if (empty($_POST["pwd"])) {
        $errorMsg .= "Password is required.<br>";
        $success = false;
    } else {
        if ($success) {
            authenticateUser();
            echo "<h4>Login successful!</h4>";
            echo "<p>Welcome back, " . $fname . $lname;
            echo "<br><button class=\"btn btn-lg btn-primary\" onclick=\"location.href='user_details.php'\">Edit user detail</button>";
            echo "<button class=\"btn btn-lg btn-primary\" onclick=\"location.href='index.php'\">Back to Home</button>";
        }
    }


    //function AU
    function sanitize_input($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    function authenticateUser()
    {
        global $fname, $lname, $email, $pwd, $errorMsg, $success;

        //create db connection
        $config = parse_ini_file('/var/www/private/db-config.ini');
        if (!$config) {
            $errorMsg =  "Failed to read database config file.";
            $success = false;
        } else {
            $conn = new mysqli(
                $config['servername'],
                $config['username'],
                $config['password'],
                $config['dbname']
            );

            //check connection
            if ($conn->connect_error) {
                $errorMsg = "Connection failed: " . $conn->connect_error;
                $success = false;
            } else {
                //prepare statement
                $stmt = $conn->prepare("SELECT * FROM userTable WHERE email=?");

                //bind and execute query statement
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    //email field is unique --> only one row in result set
                    $row = $result->fetch_assoc();
                    //assign the value of user's first name, last name and password to the respective variables
                    $fname = $row["fName"];
                    $lname = $row["lName"];
                    $pwd = $row["password"];

                    //check if password matches
                    if (!password_verify($_POST["pwd"], $pwd)) {
                        $errorMsg = "Email not found or password does not match...";
                        $success = false;
                    } else {
                        $_SESSION["userID"] = $row["userID"];
                    }
                } else {
                    $errorMsg = "Email not found or password does not match...";
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