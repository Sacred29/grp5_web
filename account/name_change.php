<?php 
    session_start();
    include_once "./../db_config.php";

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    function sanitize_input($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    $user = ['fName' => '', 'lName' => ''];

    // Fetch user data if member_id is set
    if (isset($_SESSION['userID'])) {
        $userID = $_SESSION['userID'];
        $stmt = $conn->prepare("SELECT fName, lName FROM userTable WHERE userID = ?");
        $stmt->bind_param("i", $userID);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
        }
        $stmt->close();
    }


    if (isset($_POST['submitNameChange'])) {
        // Assume user ID is stored in the session
        $userID = $_SESSION['userID'];
        
        // Sanitize user input
        $firstName = sanitize_input($_POST['firstName']);
        $lastName = sanitize_input($_POST['lastName']);
        
        
        // Check if the names are the same as the current ones
        if ($firstName == $user['fName'] && $lastName == $user['lName']) {
            $_SESSION['message'] = "No changes were made as the names are the same.";
        } else {
            // Proceed with the update
            $stmt = $conn->prepare("UPDATE userTable SET fName = ?, lName = ? WHERE userID = ?");
            $stmt->bind_param("ssi", $firstName, $lastName, $userID);

            if ($stmt->execute()) {
            // Update the session variables
            $_SESSION['fName'] = $firstName;
            $_SESSION['lName'] = $lastName;
            
            $_SESSION['message'] = "Your details have been updated successfully.";
        } else {
            $_SESSION['message'] = "Error updating your details: " . $conn->error;
        }

        $stmt->close();
        }
        header("Location: ./../account.php");
        exit();
    }
?>

<form method="post" action="./account/name_change.php">
    <div class="mb-3">
        <label for="firstName" class="form-label">First Name:</label>
            <input type="text" class="form-control" id="firstName" name="firstName" value="<?php echo htmlspecialchars($user['fName']); ?>" required>
        </div>
    <div class="mb-3">
        <label for="lastName" class="form-label">Last Name:</label>
        <input type="text" class="form-control" id="lastName" name="lastName" value="<?php echo htmlspecialchars($user['lName']); ?>" required>
    </div>
    <button type="submit" class="btn btn-primary" name="submitNameChange">Save Changes</button>
</form>
