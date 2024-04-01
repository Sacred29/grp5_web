<?php
$success = true;
$fname = $lname = $email = $pwd = $errorMsg = "";
$fields = array(
    "fname" => "First name",
    "lname" => "Last name",
    "email" => "Email",
    "pwd" => "Password",
    "pwd_confirm" => "Password Confirmation"
);
$success = true;
$is_bot = true; 
$pwd= $_POST['pwd'];
$pwd_confirm = $_POST['pwd_confirm'];

// I don't think the catcha works.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['recaptcha_response'])) {
    
    // Build POST request:
    $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
    $recaptcha_secret = '6Le0e7MZAAAAAI-pCmb3uScqvJUf5y6RN6bTqra4';
    $recaptcha_response = $_POST['recaptcha_response']; // replace with getenv

    // Make and decode POST request:
    $recaptcha = file_get_contents($recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
    $recaptcha = json_decode($recaptcha);

    // check if recaptcha server-side validation is successful
    if ($recaptcha->success) {
        // Take action based on the score returned:
        if ($recaptcha->score >= 0.5) {
            $is_bot = false;
        } else {
            $errorMsg = "reCAPTCHA thinks you are a bot. Try again in a few minutes. <br />";
        }
    } else {
        $errorMsg .= "Oops! Something went wrong with reCAPTCHA verification. <br />";
        $success = false;
    }
}
if (!$is_bot) {

} 

foreach ($fields as $field => $fieldname) {
    if (empty($_POST[$field])) {
        $errorMsg = $fieldname . "is required <br />";
        $success = false;
    }
    else
    {
        $email = sanitize_input($_POST["email"]);
        // Additional check to make sure e-mail address is well-formed.
        $lname = sanitize_input($_POST["lname"]);
        $fname = sanitize_input($_POST["fname"]);

        if ($field === "email" && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errorMsg2 .= "Invalid email format.";
            $success = false;
        }

        if (isset($_POST["user_privilege"])) {
            $userPrivilege = sanitize_input($_POST["user_privilege"]);
        }
        else {
            $userPrivilege = 'user';
        }
   }
}

if ($pwd !== $pwd_confirm) {
    $errorMsg .= "Passwords do not match.";
    $success = false;
}
else {
    $pwd = password_hash($_POST["pwd"], PASSWORD_DEFAULT);
}
if (!preg_match('/^[a-zA-Z\-\' ]+$/', $lname)) {
    $errorMsg .= "Invalid last name format.";
    $success = false;
}
if ($success) {
    saveMemberToDB();
    // registration successful.
}

if(!$success) {
    header('Location: register.php?errMsg=' . urlencode($errorMsg));
    exit;
}

function sanitize_input($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
function saveMemberToDB() {
    global $fname, $lname, $email, $pwd, $errorMsg, $success, $userPrivilege;
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
        $config['servername'] ,
        $config['username'] , 
        $config['password'] , 
        $config['dbname'] 
    ); 

    if ($conn->connect_error) {
        $errorMsg = "Connection failed: " .$conn->connect_error;
        $success = false;
    }
    else {
        // Check if the email already exists
        $sql = "SELECT COUNT(*) AS count FROM userTable WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();
        if ($count > 0) {
            $success = false;
            $errorMsg .= "Email already exists. <br />";
        } else {
            // If the count is 0, the email does not exist. so can add.
            $stmt = $conn->prepare("INSERT INTO userTable (fName, lName, email, password, userPrivilege) VALUES (?,?,?,?,?)");
            $stmt->bind_param("sssss    ", $fname, $lname, $email, $pwd, $userPrivilege);
            if (!$stmt->execute()){
                $errorMsg = "Execute failed: (" .$stmt->errno .") " . $stmt->error;
                $success = false;
            }
            $stmt->close();
            $conn->close();
            
            // redirect after inserting
            header("Location: index.php");
            exit;
            
        }
        $conn->close();
    }
}