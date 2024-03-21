<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$username = $fname = $lname = $email = $pwd = $DOB = $errorMsg = $errorMsg2 = $errorMsg3 = "";
$singleError = true; // Initialize as true
$fields = array(
    "username" => "username",
    "fname" => "First name",
    "lname" => "Last name",
    "email" => "Email",
    "DOB" => "Date of Birth",
    "pwd" => "Password",
    "pwd_confirm" => "Password Confirmation"

);
$errorMsg = "";
$success = true;
$pwd = $_POST['pwd'];
$pwd_confirm = $_POST['pwd_confirm'];

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
        $username = sanitize_input($_POST['username']);

        if ($field === "email" && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errorMsg2 .= "Invalid email format.";
            $success = false;
        }
    }
}

if ($pwd !== $pwd_confirm) {
    $errorMsg .= "Passwords do not match.";
    $success = false;
} else {
    $pwd = password_hash($_POST["pwd"], PASSWORD_DEFAULT);
}

if (!preg_match('/^[a-zA-Z\-\' ]+$/', $lname)) {
    $errorMsg .= "Invalid last name format.";
    $success = false;
}

if ($success) {
    //$hashedPassword = password_hash($pwd, PASSWORD_DEFAULT);
    echo "<h4>Registration successful!</h4>";
    echo "<p>Username: " . $username;
    echo "<p>Email: " . $email;
    echo "<p>First name: " . $fname;
    echo "<p>Last name: " . $lname;
    echo "<p>Hashed password: " . $pwd;
    saveMemberToDB();
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

function saveMemberToDB()
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
            //Prepare statement
            //Bind and execute query statement
            $stmt = $conn->prepare("INSERT INTO userTable (username, fname, lname, email, password, DOB) VALUES (?,?,?,?,?,?)");
            $stmt->bind_param("ssssss", $username,$fname, $lname, $email, $pwd, $DOB);

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
