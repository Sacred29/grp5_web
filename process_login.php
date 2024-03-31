<?php
session_start();
?>



    <?php
   
   
    $fname = $lname = $email = $pwd = $errorMsg = $userprivilege = "";
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
            if($success) {
                include "./otpService/send.php";
                header("Location: ./process_login_2FA.php");
            }
            // echo "<br><button onclick=\"location.href='index.php'\">Back to Home</button>";
            // echo "<br><button class=\"btn btn-lg btn-primary\" onclick=\"location.href='user_details.php'\">Edit user detail</button>";
            // echo "<button class=\"btn btn-lg btn-primary\" onclick=\"location.href='index.php'\">Back to Home</button>";

            
            
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
                    $_SESSION["email"] = $row ["email"];
                    $_SESSION["fName"] = $row["fName"];
                    $_SESSION["lName"] = $row["lName"];
                    
                    
                 
                }
            } else {
                $errorMsg = "Email not found or password does not match...";
                $success = false;
            }
            $conn->close();
        }
    }
    
?>
