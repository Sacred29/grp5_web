<?php
$redirect_Success ? $redirect_Success: "index.php";
$redirect_Fail ? $redirect_Fail : "index.php";

echo '<link rel="stylesheet" href="../css/otp.css">';
echo '<script defer src="../js/otp.js"></script>';
?>

<form class="otpForm container" method="post" action="<?php echo realpath('otpValidate.php'); ?>">
    <div id="inputs" class="inputs bottom">
        <input class="input" type="text" inputmode="numeric" maxlength="1" id="input1" name="input1" />

        <input class="input" type="text" inputmode="numeric" maxlength="1" id="input2" name="input2" />
        <input class="input" type="text" inputmode="numeric" maxlength="1 " id="input3" name="input3" />
        <input class="input" type="text" inputmode="numeric" maxlength="1 " id="input4" name="input4" />
        <input class="input" type="text" inputmode="numeric" maxlength="1" id="input5" name="input5" />
        <input class="input" type="text" inputmode="numeric" maxlength="1" id="input6" name="input6" />
    </div>
    <input type="submit" value="Submit" name="specific">

</form>
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['specific'])) {
    
    $config_file = parse_ini_file('/var/www/private/db-config.ini');
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

    if ($conn->connect_error) {
        $errorMsg = "Connection failed: " . $conn->connect_error;
        exit;
    } else {
        $concatenatedInput = $_POST['input1'] . $_POST['input2'] . $_POST['input3'] . $_POST['input4'] . $_POST['input5'] . $_POST['input6'];

        // Parse the concatenated input into an integer
        $parsedInteger = (int)$concatenatedInput;

        // Execute the SELECT query
        $sql = "SELECT * FROM otpTable WHERE code = '$parsedInteger' ORDER BY id DESC LIMIT 1";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // Output data of each row
            while ($row = $result->fetch_assoc()) {
                $email = $row["email"];
                $expiry = $row["expiry"];
            }
            $now = new DateTime();
            $now = $now->format('Y-m-d H:i:s');
            if ($now < $expiry) {
                // OTP Validated
                header("Location: " . $redirect_Success);

            } else {
                echo "<script>console.log('OTP has expired');</script>";
                header("Location: " . $redirect_Fail);
            }
        } else {
            echo "<script>console.log('No matching entry found for otp');</script>";
            header("Location: " . $redirect_Fail);
        }

        $conn->close();
    }
}

?>