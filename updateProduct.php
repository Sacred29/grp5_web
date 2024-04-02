<?php
var_dump($_SESSION);
include "inc/head.inc.php";
?>

<body>
    <?php
    include "inc/nav.inc.php";
    ?>
    <main class="container">
        <?php
        $books = [];
        //create db connection
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
        } else {



            //check connection
            if ($conn->connect_error) {
                $errorMsg = "Connection failed: " . $conn->connect_error;
                $success = false;
            } else {
                //Prepare statement
                //Bind and execute query statement
                if (isset($_GET['id'])) {
                    $id = $_GET['id'];
                    echo "<script>console.log('id :" . $_GET['id'] . "')</script>";
                    echo "<script>console.log('" . $_GET[''] . "')</script>";
                    $stmt = $conn->query("SELECT * FROM productTable WHERE productID = $id");

                    //$stmt = $conn->prepare("INSERT INTO world_of_pets_members (fname, lname, email, password) VALUES ('jane','doe','jane@abc.com','123')");



                    // Check if there are rows returned
                    if ($stmt->num_rows > 0) {
                        // Loop through the rows and fetch the data
                        while ($row = $stmt->fetch_assoc()) {
                            // Access data using column names
                            $books[] = $row;
                            // Adjust column names as per your table structure
                        }
                    } else {
                        echo "0 results";
                    }

                    $conn->close();
                }
            }
        }

        ?>
        <h1>Product Update</h1>

        <form action="process_updateProduct.php?id=<?php echo $_GET['id']?>" method="POST">
            <input type="hidden" name="id" id = "id" value=<?php echo $_GET['id']?>>
            <div class="mb-3">
                <label for="productName" class="form-label">Product Name</label>
                <input maxlength="45" type="text" id="productName" name="productName" class="form-control" placeholder="Input Product Name">
            </div>
            <div class="mb-3">
                <label for="arrivalDate" class="form-label">Arrival Date</label>
                <input required type="date" id="arrivalDate" name="arrivalDate" class="form-control" placeholder="Select Date">
                <!-- <input id="lname" name="lname" class="form-control" placeholder="Enter last name"> -->
            </div>
            <div class="mb-3">
                <label for="genre" class="form-label">Product Genre</label>
                <select name="genre" id="genre">
                    <option value="Fiction">Fiction</option>
                    <option value="Non-Fiction">Non-Fiction</option>
                    <option value="Educational">Educational</option>
                    <option value="Self-Help">Self-Help</option>
                </select>
                <!-- <input id="email" name="email" class="form-control" placeholder="Enter email"> -->
            </div>
            <div class="mb-3">
                <label for="bookUEN" class="form-label">Product UEN:</label>
                <input required type="text" id="bookUEN" name="bookUEN" class="form-control" placeholder="Input Book UEN">
                <!-- <input id="pwd" name="pwd" class="form-control" placeholder="Enter password"> -->
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Price: </label>
                <input required type="text" id="price" name="price" class="form-control" placeholder="Input Price">
                <!-- <input id="pwd_confirm" name="pwd_confirm"  class="form-control"placeholder="Confirm password"> -->
            </div>
            <div class="mb-3">
                <label for="bookAuthor" class="form-label">Product Author:</label>
                <input required type="text" id="bookAuthor" name="bookAuthor" class="form-control" placeholder="Input Product Author">
                <!-- <input id="pwd" name="pwd" class="form-control" placeholder="Enter password"> -->
            </div>
            <div class="mb-3">
                <label for="bookPublisher" class="form-label">Product Publisher:</label>
                <input required type="text" id="bookPublisher" name="bookPublisher" class="form-control" placeholder="Input Product Publisher">
                <!-- <input id="pwd" name="pwd" class="form-control" placeholder="Enter password"> -->
            </div>
            <label for="productImage">Select Product Image:</label>
            <input type="file" id="productImage" name="productImage">
            </br>
            <div class="mb-3">
                <button type="submit">Submit</button>
            </div>
        </form>
    </main>
    <?php
    include "inc/footer.inc.php";
    ?>
</body>