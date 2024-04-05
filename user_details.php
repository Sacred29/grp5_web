<!DOCTYPE html>
<html lang="en">
<?php
session_start();
include "inc/head.inc.php";
?>

<body>
    <?php
    include "inc/nav.inc.php";
    include "inc/header.inc.php";

    if (isset($_SESSION["userID"])) {
        getUserInfoFromDB();
    } else {
        echo "Please log in to edit your user details!";
    }
    global $fname, $lname, $email, $errorMsg, $success;

    //$fname = $lname = $email = $errorMsg = $success = "";
    function getUserInfoFromDB()
    {
        global $fname, $lname, $email, $errorMsg, $success;
        //create db connection
        $config_file = '/var/www/private/db-config.ini';
        $item = 0;

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
            getenv('SERVERNAME'),
            getenv('DB_USERNAME'),
            getenv('DB_PASSWORD'),
            getenv('DBNAME')
        );

        if ($conn->connect_error) {
            $errorMsg = "Connection failed: " . $conn->connect_error;
            $success = false;
        } else {
            //Prepare statement
            //Bind and execute query statement
            $stmt = $conn->prepare("SELECT email, fName, lName FROM userTable WHERE userID = ?");
            $stmt->bind_param("s", $_SESSION["userID"]);

            if (!$stmt->execute()) {
                $errorMsg = "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
                $success = false;
            } else {
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    //email field is unique --> only one row in result set
                    $row = $result->fetch_assoc();
                    //assign the value of user's first name, last name and password to the respective variables
                    $email = $row["email"];
                    $fname = $row["fName"];
                    $lname = $row["lName"];
                }
            }
            $stmt->close();
        }
        $conn->close();
    }


    ?>
    <main class="container">
        <h1>Update account information</h1>
        <form id="account-info-form" action="process_info_update.php" method="post">
            <div class="mb-3">
                <label for="fname" class="form-label">First Name:</label>
                <input maxlength="45" type="text" value="<?php echo ($fname) ?>" id="fname" name="fname" class="form-control" placeholder="Enter first name">
            </div>
            <div class="mb-3">
                <label for="lname" class="form-label">Last Name:</label>
                <input required maxlength="45" type="text" value="<?php echo ($lname) ?>" id="lname" name="lname" class="form-control" placeholder="Enter last name">
                <!-- <input id="lname" name="lname" class="form-control" placeholder="Enter last name"> -->
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email:</label>
                <input required maxlength="45" type="email" value="<?php echo ($email) ?>" id="email" name="email" class="form-control" placeholder="Enter email">
                <!-- <input id="email" name="email" class="form-control" placeholder="Enter email"> -->
            </div>
            <div class="mb-3">
                <button type="submit">Submit</button>
            </div>
        </form>
    </main>
    <?php
    include "inc/footer.inc.php";
    ?>
</body>

</html>