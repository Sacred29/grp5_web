<!DOCTYPE html>
<html lang="en">
<?php
    include "/inc/head.inc.php";
?>
<body>
        <!-- Collapsible Top Navbar -->
        <?php
            include "/inc/nav.inc.php";
        ?>
        <?php
            include "/inc/header.inc.php";
        ?>
    <main class="container">
        <?php
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
            // DB logic

            // $email = isset($email) ? $email : trigger_error("User Email is missing", E_USER_ERROR);
            $email = "bull.daniel.3@gmail.com";
            $expiry = new DateTime();
            // echo $expiry->format('Y-m-d H:i:s');
            $expiry = ($expiry->modify(' +5 minutes'))->format('Y-m-d H:i:s');
            // echo $expiry->format('Y-m-d H:i:s');
            $timezone = date_default_timezone_get();
            // echo "<br>" . $timezone;
            
            // Random 6 digit number
            $code = rand(100000, 999999);
            // echo "<br>" . "Random 6-digit number: " . $code;

            $stmt = $conn->prepare("INSERT INTO otpTable (email, expiry, code) VALUES (?,?,?)");
            
            $stmt->bind_param("ssi", $email, $expiry, $code);
            
            if (!$stmt->execute()){
                $errorMsg = "Execute failed: (" .$stmt->errno .") " . $stmt->error;
                $success = false;
            }
            

            $stmt->close();
            $conn->close();

            // Send Email
            $senderName = "Admin"; 
            $senderEmail = "Admin@thedaniel.life";
            $customerName = "Customer";
            $customerEmail = $email;
            $subject = "OTP Verification";
            $body = "Your OTP Passcode is <b>" . $code . "</b>. It will Expire within 5 minutes, at " . $timezone . " " . $expiry;

            include "/mailer/sendMail.php";
        }
        
        ?>
    </main>
        <?php
            include "/inc/footer.inc.php";
        ?>
   
        
        <!--Modal-->
        <div id="imgModal" class="imgModal">
            <span class="close">&times;</span>
            <img class="modal-content" id="img01">
        </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js" type="text/javascript"></script>
    <script>window.jQuery || document.write('<script src="/js/vendor/jquery-1.11.2.min.js"><\/script>')</script>x
    <script src="/js/vendor/bootstrap.min.js"></script>
    <script src="/js/datepicker.js"></script>
    <script src="/js/plugins.js"></script>
</body>
</html>