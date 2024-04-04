<?php
include "inc/head.inc.php";
session_start();


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
    header("Location: account.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include "inc/nav.inc.php"; ?>
    <link href="path_to_bootstrap_css/bootstrap.min.css" rel="stylesheet">
    <title>Account Settings</title>
</head>
<body>
<main class="container">
    <h2>Account Settings</h2>
    <?php if (isset($_SESSION['message'])) {
        echo "<script>alert('{$_SESSION['message']}');</script>";
        unset($_SESSION['message']); // Clear the message after displaying it
    }
    ?>
    <!-- Nav tabs -->
    <ul class="nav nav-tabs" id="accountTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="personal-tab" data-bs-toggle="tab" href="#personal" role="tab" aria-controls="personal" aria-selected="true">Personal Details</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="password-tab" data-bs-toggle="tab" href="#password" role="tab" aria-controls="password" aria-selected="false">Change Password</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="orders-tab" data-bs-toggle="tab" href="#orders" role="tab" aria-controls="orders" aria-selected="false">Order Details</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="reviews-tab" data-bs-toggle="tab" href="#reviews" role="tab" aria-controls="reviews" aria-selected="false">Your Reviews</a>
        </li>
    </ul>

    <!-- Tab panes -->
    <div class="tab-content" id="accountTabsContent">
        <div class="tab-pane fade show active" id="personal" role="tabpanel" aria-labelledby="personal-tab" style="display: block !important; visibility: visible !important;">
            <form method="post" action="account.php">
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
        </div>

    <div class="tab-pane fade" id="password" role="tabpanel" aria-labelledby="password-tab">
        <!-- Change password form here -->
    </div>
    <div class="tab-pane fade" id="orders" role="tabpanel" aria-labelledby="orders-tab">
        <!-- Order details content here -->
    </div>
    <div class="tab-pane fade" id="reviews" role="tabpanel" aria-labelledby="reviews-tab">
        <!-- Reviews content here -->
    </div>
</div>
</main>

<!-- Include Bootstrap Bundle with Popper -->
<script src="path_to_bootstrap_js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', (event) => {
    let personalTabContent = document.getElementById('personal');
    if (personalTabContent) {
        personalTabContent.style.display = 'block';
        personalTabContent.style.visibility = 'visible';
        personalTabContent.style.opacity = '1';
    }
});
</script>
</body>
</html>

