<?php
// Start the session
session_start();

include_once "./../db_config.php";

function sanitize_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

$currentPasswordError = ""; // Initialize variable for current password error
$success = true;

if (isset($_SESSION["userID"])) {
    if (isset($_POST['submitPasswordChange'])) {
        $userID = $_SESSION['userID'];
        $currentPassword = sanitize_input($_POST['currentPassword']);
        $newPassword = sanitize_input($_POST['newPassword']);
        $confirmPassword = sanitize_input($_POST['confirmPassword']);

        // Fetch the current password from the database
        $stmt = $conn->prepare("SELECT password FROM userTable WHERE userID = ?");
        $stmt->bind_param("i", $userID);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $currentPasswordHash = $user['password'];

        // Verify the current password
        if (password_verify($currentPassword, $currentPasswordHash)) {
            // Check if the new password and confirm password match
            if ($newPassword === $confirmPassword) {
                // Update the password in the database
                $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
                $updateStmt = $conn->prepare("UPDATE userTable SET password = ? WHERE userID = ?");
                $updateStmt->bind_param("si", $newPasswordHash, $userID);

                if ($updateStmt->execute()) {
                    $_SESSION['message'] = "Password changed successfully.";
                } else {
                    $_SESSION['message'] = "Error updating password: " . $conn->error;
                }
                $updateStmt->close();
            } else {
                $_SESSION['message'] = "New passwords do not match.";
            }
        } else {
            $_SESSION['message'] = "Current password is incorrect.";
        }

        $stmt->close();
        header("Location: ./../account.php");
        exit();
    }
} else {
    $_SESSION['message'] = "Please log in to change your password!";
    $success = false;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <link href="path_to_bootstrap_css/bootstrap.min.css" rel="stylesheet">
    <title>Change Password</title>
</head>

<body>
    <main class="container">
        <?php if (isset($_SESSION['message'])) {
            echo "<div class='alert alert-info'>" . $_SESSION['message'] . "</div>";
            unset($_SESSION['message']);
        }
        ?>
        <?php if ($success) { ?>
            <form method="post" action="./account/password_change.php">
                <div class="mb-3">
                    <label for="currentPassword" class="form-label">Current Password:</label>
                    <input type="password" class="form-control" id="currentPassword" name="currentPassword" required>
                </div>
                <div class="mb-3">
                    <label for="newPassword" class="form-label">New Password:</label>
                    <input type="password" class="form-control" id="newPassword" name="newPassword" required>
                </div>
                <div class="mb-3">
                    <label for="confirmPassword" class="form-label">Confirm New Password:</label>
                    <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
                </div>
                <button type="submit" class="btn btn-primary" name="submitPasswordChange">Change Password</button>
            </form>
        <?php } ?>
    </main>
    <script src="path_to_bootstrap_js/bootstrap.bundle.min.js"></script>
</body>

</html>