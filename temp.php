<?php
    $lastname = $errorMsg = "";
    $email = $errorMsg2 = "";
    $password = $errorMsg3 = "";
    $singleError = true; // Initialize as true
    $fields = array(
        "lname" => "Last name",
        "email" => "Email",
        "pwd" => "Password",
        "pwd_confirm" => "Password Confirmation"
    );
    $errorMsg = "";
    $success = true;
    $passwordValue = $_POST['pwd'];
    $passwordConfirmValue = $_POST['pwd_confirm'];

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
        else
        {
            $email = sanitize_input($_POST["email"]);
            // Additional check to make sure e-mail address is well-formed.
            $lastname = sanitize_input($_POST["lname"]);

            if ($field === "email" && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errorMsg2 .= "Invalid email format.";
                $success = false;
            }

            elseif ($field === "pwd") {
                $password = $passwordValue; // Assign the raw password value
            }

       }
    }

    if ($passwordValue !== $passwordConfirmValue) {
        $errorMsg .= "Passwords do not match.";
        $success = false;
    }

    if (!preg_match('/^[a-zA-Z\-\' ]+$/', $lastname)) {
        $errorMsg .= "Invalid last name format.";
        $success = false;
    }

    if ($success)
        {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            echo "<h4>Registration successful!</h4>";
            echo "<p>Email: " . $email;
            echo "<p>Last name: " . $lastname;
            echo "<p>Hashed password: " .$hashedPassword;
            saveMemberToDB();

        }
    else
        {
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

    function saveMemberToDB() {
        global $fname, $lname, $email, $pwd, $errorMsg, $success;

        //create db connection
        $config = parse_ini_file('/var/www/private/db-config.ini');
        if (!$config) {
            $errorMsg =  "Failed to read database config file.";
            $success = false;
        }
        else {
            $conn = new mysqli(
                $config['servername'],
                $config['username'],
                $config['password'],
                $config['dbname']
            );

            //check connection
            if ($conn->connect_error){
                $errorMsg = "Connection failed: " .$conn->connect_error;
                $success = false;
            }
            else {
                //Prepare statement
                //$stmt = $conn->prepare("INSERT INTO world_of_pets_members (fname, lname, email, password) VALUES (?,?,?,?)");
                //Bind and execute query statement
                //$stmt->bind_param("ssss",$fname,$lname,$email,$pwd_hashed);
                $stmt = $conn->prepare("INSERT INTO world_of_pets_members (fname, lname, email, password) VALUES (?,?,?,?)");
                $stmt->bind_param("ssss", $fname, $lname, $email, $passwordValue);
                // $fname = "Ray";
                // $lname = "Ban";
                // $email = "Rb@ra.com";
                // $pwd = "abc";
                
                
                //$stmt = $conn->prepare("INSERT INTO world_of_pets_members (fname, lname, email, password) VALUES ('jane','doe','jane@abc.com','123')");

                if (!$stmt->execute()){
                    $errorMsg = "Execute failed: (" .$stmt->errno .") " . $stmt->error;
                    $success = false;
                }
                $stmt->close();
            }
            $conn->close();
        }
     }
?>

