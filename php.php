<?php
    include "inc/head.inc.php";
?>
<body>
<?php
    authenticateUser();

    function authenticateUser(){
        global $fname, $lname, $email, $pwd, $errorMsg, $success;

        //create db connection
        $config = parse_ini_file('C:/var/www/private/db-config.ini');
       
        // $conn = new mysqli(
        //     $config['servername'],
        //     $config['username'],
        //     $config['password'],
        //     $config['dbname']
        // );
        $conn = new mysqli(
            "35.212.243.22",
            "inf1005-sqldev",
            "p1_5",
            "bookStore"
        );
          

            //check connection
            if ($conn->connect_error){
                $errorMsg = "Connection failed: " .$conn->connect_error;
                $success = false;
            }
            else {
                //prepare statement
                

                //bind and execute query statement
                
               
                $result =  $conn->query("SELECT * FROM userTable");
                if($result->num_rows>0){
                    //email field is unique --> only one row in result set
                    while ($row = $result->fetch_assoc()) {
                        echo "id: " . $row["userID"]. " - Name: " . $row["email"]. "<br>";
                    }
                   
                }
                else {
                    echo "0 results";
                }
                $result->close();
            }
            $conn->close();
        }
    
?>
    <?php
    include "inc/footer.inc.php";
    ?>
</body>