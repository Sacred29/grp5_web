<?php
include "inc/head.inc.php";
?>
<body>
    <main>
    <?php
    $fname = $lname = $email = $pwd = $errorMsg = $userprivilege = "";
    $success = true;

    if (empty($_POST["email"])) {
        $errorMsg .= "Email is required.<br />";
        $success = false;
    } else {
        $email = sanitize_input($_POST["email"]);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errorMsg .= "Invalid email format.<br />";
            $success = false;
        }
    }

    if (empty($_POST["pwd"])) {
        $errorMsg .= "Password is required.<br />";
        $success = false;
    }

    if ($success) {
        authenticateUser();
    }

    if (!$success) {
        header('Location: login.php?errMsg=' . urlencode($errorMsg));
        exit;
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
                // if email is found     
                $row = $result->fetch_assoc();
                //assign the value of user's first name, last name and password to the respective variables
                $fname = $row["fName"];
                $lname = $row["lName"];
                $pwd = $row["password"];
                if (!password_verify($_POST["pwd"], $pwd)) {
                    $errorMsg = "Password is incorrect.";
                    $success = false;
                }
                else {
                    // Loading Screen
                    include "inc/wave-loader.inc.php";
                    
                    $redirect = "/process_login_2FA.php";
                    $url = "./otpService/send.php";
                    // Hidden forms
                    echo '<form id="hiddenForm" action="./otpService/send.php" method="post" style="display: none;">
                        <input name="email" value="' . $email . '">
                        <input name="redirect" value="'. $redirect . '">
                        <input type="submit" value="Submit">
                        </form>'; 
                    echo "<script>window.onload = function() {
                        document.getElementById('hiddenForm').submit();
                        };</script>";
                }
            }
            else {
                $errorMsg .= "Email Cannot Be Found.";
                $success = false;
            }
        }
    }
    
?>
</main>
</body>