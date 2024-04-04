<?php
include "inc/head.inc.php";
?>

<body>
    <?php
    include "inc/nav.inc.php";
    ?>
    <main class="container">
        <h1>Product Registration</h1>

        <form action="/process_productRegister.php" method="post">
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