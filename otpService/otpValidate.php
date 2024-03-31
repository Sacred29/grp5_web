
<?php
    include "../inc/head.inc.php";
    echo '<link rel="stylesheet" href="../css/otp.css">';
    echo '<script defer src="../js/otp.js"></script>';    
?>

<body>
    <?php
    include "../inc/nav.inc.php";
    ?>
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
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
        $success = false;
        exit;
    } 
    else {
    
        // Concatenate the inputs together
        $concatenatedInput = $_POST['input1'] . $_POST['input2'] . $_POST['input3'] . $_POST['input4'] . $_POST['input5'] . $_POST['input6'];
    
        // Parse the concatenated input into an integer
        $parsedInteger = (int)$concatenatedInput;

        // You can then submit this integer wherever you need, like saving it to a database or using it in further processing.
        // For demonstration, let's just print it here.
        echo "Parsed Integer: " . $parsedInteger;
        // Email to search for
        // $email = 'bull.daniel.3@gmail.com';

        // Execute the SELECT query
        $sql = "SELECT * FROM otpTable WHERE code = '$parsedInteger' ORDER BY id DESC LIMIT 1";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // Output data of each row
            while($row = $result->fetch_assoc()) {
                $email = $row["email"];
                $expiry = $row["expiry"];
            }
            $now = new DateTime();
            $now = $now->format('Y-m-d H:i:s');
            if ($now < $expiry) {
                // OTP Validated
                echo "YOU HAVE LOGGED IN!!!";
            }
            else {
                echo "OTP has expired.";
            }


        } else {
            echo "No matching entry found for otp";
        }

        $conn->close();

        
        /*
        $stmt = $conn->query("SELECT * FROM otpTable WHERE email = :email ORDER BY id DESC LIMIT 1");
        if ($stmt->num_rows > 0) {
            // Loop through the rows and fetch the data
            while ($row = $stmt->fetch_assoc()) {
                // Access data using column names
                echo "ID: " . $row["productID"] . " - Name: " . $row["productName"] . "<br>";
                $codes = $row["code"];
                $expired = $row["expiry"];
            }
            $now = new DateTime();
            $now = $now->format('Y-m-d H:i:s');
            if ($now < $expired) {
                
            } 

            else {
                echo "OTP Expired"
            }


        } else {
            echo "0 results";
        }
        */
    }
}
?>
<main class="container">
<form class="otpForm" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<pre>
<h1>2FA Validation</h1> 
<h2>Please input your one-time password.</h2>
</pre>
<div id="inputs" class="inputs bottom">
            <input class="input" type="text"
                inputmode="numeric" maxlength="1" id="input1" name="input1"/>
            
                <input class="input" type="text"
                inputmode="numeric" maxlength="1"id="input2" name="input2" />
            <input class="input" type="text"
                inputmode="numeric" maxlength="1" id="input3" name="input3"/>
            <input class="input" type="text"
                inputmode="numeric" maxlength="1" id="input4" name="input4"/>
            <input class="input" type="text"
                inputmode="numeric" maxlength="1" id="input5" name="input5"/>
            <input class="input" type="text"
                inputmode="numeric" maxlength="1" id="input6" name="input6"/>
</div>
        <input type="submit" value="Submit">
        
</form>
</main>
    <?php
    include "../inc/footer.inc.php";
    ?>
</body>