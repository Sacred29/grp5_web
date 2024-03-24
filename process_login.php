<?php
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    session_start(); // Ensure this is at the very top

    include "inc/head.inc.php";
    //query database for given user
    //validate user input - check if user input or if password doesn't match
    //notify the user if the above happens

    //if invalid input --> display error
    //if successful --> display user info
    $fname = $lname = $email = $pwd = $errorMsg = $userprivilege = "";
    $success = true;

    if(empty($_POST["email"])){
        $errorMsg .= "Email is required.<br>";
        $success = false;
    }
    else {
        $email = sanitize_input($_POST["email"]);

        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errorMsg .= "Invalid email format.";
            $success = false;
        }
    }

    if(empty($_POST["pwd"])){
        $errorMsg .= "Password is required.<br>";
        $success = false;
    }
    else{
        $pwd = $_POST["pwd"];
    }

    if ($success) {
        $config = true; //= parse_ini_file('/var/www/private/db-config.ini');
        if (!$config) {
            $errorMsg = "Failed to read database config file.";
            $success = false;
        } else {
            $conn = new mysqli('35.212.243.22', 'inf1005-sqldev', 'p1_5', 'bookStore');
            if ($conn->connect_error) {
                $errorMsg = "Connection failed: " . $conn->connect_error;
                $success = false;
            } else {
                $stmt = $conn->prepare("SELECT userID, email, password, fName, lName, userPrivilege FROM bookStore.userTable WHERE email = ?");
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    if (password_verify($pwd, $row['password'])) {
                        $_SESSION['user_email'] = $email;
                        $_SESSION['user_fname'] = $row['fName'];
                        $_SESSION['user_lname'] = $row['lName'];
                        $_SESSION['user_id'] = $row['userID'];
                        $_SESSION['user_privilege'] = $row['userPrivilege'];
                        $success = true;
                    } else {
                        $errorMsg .= "Invalid password.<br>";
                        $success = false;
                    }
                } else {
                    // No user found with the email address
                    $errorMsg .= "No user found with the specified email address.<br>";
                    $success = false;
                }
                $conn->close();
            }
        }
    }

    if ($success) {
        echo "<h4>Login successful!</h4>";
        echo "<p>Welcome back, " . htmlspecialchars($fname) . " " . htmlspecialchars($lname) . "</p>";
        echo "<br><button onclick=\"location.href='index.php'\">Back to Home</button>";
    } else {
        echo "<h4>Login failed</h4>";
        echo "<p>" . $errorMsg . "</p>";
        echo "<br><button onclick=\"location.href='login.php'\">Try Again</button>";
    }


    //function AU
    function sanitize_input($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }


    include "inc/footer.inc.php";
?>
