        <?php
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

        if ($conn->connect_error) {
            $errorMsg = "Connection failed: " . $conn->connect_error;
            $success = false;
            exit;
        } else {
            // DB logic
            if (!$_SESSION["email"]) {
                trigger_error("User Email is missing", E_USER_ERROR);
                exit;
            }
            $email = $_SESSION["email"];
            $expiry = new DateTime();
            $expiry = ($expiry->modify(' +5 minutes'))->format('Y-m-d H:i:s');
            $timezone = date_default_timezone_get();

            // Random 6 digit number
            $code = rand(100000, 999999);
            
            // Store in otpTable
            $stmt = $conn->prepare("INSERT INTO otpTable (email, expiry, code) VALUES (?,?,?)");

            $stmt->bind_param("ssi", $email, $expiry, $code);

            if (!$stmt->execute()) {
                $errorMsg = "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
                $success = false;
                exit;
            }


            $stmt->close();
            $conn->close();

            // Send Email
            $senderName  = "Bookstore Botique";
            $senderEmail= "BookstoreBotique@thedaniel.life";
            $customerName  = $_SESSION["fName"] . " " . $_SESSION["lName"];
            $customerEmail  = $email;
            $subject = "OTP Verification";
            $body = "Your OTP Passcode is <b>" . $code . "</b>. It will Expire within 5 minutes, at " . $timezone . " " . $expiry;

            include __DIR__ . '/../mailer/sendMail.php';
        }

        ?>
