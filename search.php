<!DOCTYPE html>
<html lang="en">
<style>
    .dropdown-toggle,
    .dropdown-menu {
        width: 100%;
    }

    .btn-showAdvanced {
        width: auto !important;
        margin: 0 auto;
    }

    #advanced-options-collapsible {
        margin-top: 2em;
    }
</style>
<?php
include "inc/head.inc.php";
session_start();

$bookTitle = $_GET["book-title"] ?? '';
$bookGenre = $_GET["book-genre"] ?? '';
$bookAuthor = $_GET["book-author"] ?? '';
$bookPublisher = $_GET["book-publisher"] ?? '';
$ratingMinimum = intval($_GET["rating-minimum"] ?? 0);
$sortBy = $_GET["orderby"] ??  '';
$sortOrder = $_GET["sort-order"] ?? '';
?>

<body>
    <!-- Collapsible Top Navbar -->
    <?php
    include "inc/nav.inc.php";
    ?>
    <main class="container">
        <section class="featured-places">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="section-heading">
                            <h1>Search</h1>
                        </div>
                    </div>
                </div>
                <form action="/search.php" method="get">
                    <div class="row">
                        <div class="mb-3">
                            <label for="book-title" class="form-label">Book Title:</label>
                            <input maxlength="255" value="<?php echo $bookTitle ?: '' ?>" type="text" id="book-title" name="book-title" class="form-control" placeholder="Enter book title">
                        </div>
                    </div>
                    <div class="row">
                        <button class="btn btn-primary btn-showAdvanced" type="button" data-toggle="collapse" data-target="#advanced-options-collapsible" aria-expanded="false" aria-controls="advanced-options-collapsible">
                            Advanced search options
                        </button>
                    </div>
                    <div class="collapse" id="advanced-options-collapsible">
                        <div class="row">
                            <div class="col">
                                <label for="book-author" class="form-label">Author:</label>
                                <input maxlength="45" value="<?php echo $bookAuthor ?: '' ?>" type="text" id="book-author" name="book-author" class="form-control" placeholder="Enter book author">
                            </div>
                            <div class="col">
                                <label for="book-publisher" class="form-label">Publisher:</label>
                                <input maxlength="45" value="<?php echo $bookPublisher ?: '' ?>" type="text" id="book-publisher" name="book-publisher" class="form-control" placeholder="Enter book publisher">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="dropdown">
                                    <label class="form-label">Genre</label>
                                    <br>
                                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButtonGenre" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <?php echo $bookGenre ?: 'Select...' ?>
                                    </button>
                                    <div class="dropdown-menu" id="genre-dropdown" aria-labelledby="dropdownMenuButtonGenre">
                                        <a class="dropdown-item genre-item" data-value="Fiction" href="#">Fiction</a>
                                        <a class="dropdown-item genre-item" data-value="Non-Fiction" href="#">Non-Fiction</a>
                                        <a class="dropdown-item genre-item" data-value="Educational" href="#">Educational</a>
                                        <a class="dropdown-item genre-item" data-value="Self-Help" href="#">Self-Help</a>
                                    </div>
                                </div>
                                <input type="text" value="<?php echo $bookGenre ?: '' ?>" name="book-genre" id="book-genre" hidden>
                            </div>
                            <div class="col">
                                <label for="rating-minimum" class="form-label">Minimum average rating</label>
                                <input type="range" value="<?php echo $ratingMinimum ?: 0 ?>" class="form-control-range" min="0" max="5" name="rating-minimum" id="rating-minimum">
                                <span id="rangeval"><?php echo $ratingMinimum ?: "0" ?></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="dropdown">
                                    <label class="form-label">Order books by</label>
                                    <br>
                                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButtonSortBy" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <?php echo $sortBy ?: 'Select...' ?>
                                    </button>
                                    <div class="dropdown-menu" id="orderby-dropdown" aria-labelledby="dropdownMenuButtonSortBy">
                                        <a class="dropdown-item orderby-item" data-value="Price" href="#">Price</a>
                                        <a class="dropdown-item orderby-item" data-value="Title" href="#">Title</a>
                                        <a class="dropdown-item orderby-item" data-value="Arrival date" href="#">Arrival date</a>
                                        <a class="dropdown-item orderby-item" data-value="Rating" href="#">Rating</a>
                                    </div>
                                </div>
                                <input type="text" value="<?php echo $sortBy ?: '' ?>" name="orderby" id="orderby" hidden>
                            </div>
                            <div class="col">
                                <div class="dropdown">
                                    <label class="form-label">Sort order</label>
                                    <br>
                                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButtonSortOrder" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <?php echo $sortOrder ?: 'Select...' ?>
                                    </button>
                                    <div class="dropdown-menu" id="sort-order-dropdown" aria-labelledby="dropdownMenuButtonSortOrder">
                                        <a class="dropdown-item sort-order-item" data-value="Ascending" href="#">Ascending</a>
                                        <a class="dropdown-item sort-order-item" data-value="Descending" href="#">Descending</a>
                                    </div>
                                </div>
                                <input type="text" <?php echo $sortOrder ?: '' ?> name="sort-order" id="sort-order" hidden>
                            </div>
                        </div>
                    </div>

                    <br>
                    <div class="row">
                        <div class="mb-3">
                            <button class="btn btn-primary" type="submit">Search</button>
                        </div>
                    </div>
            </div>
            </form>

            <div class="row">
                <?php
                $books = [];
                //create db connection
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

                //check connection
                if ($conn->connect_error) {
                    $errorMsg = "Connection failed: " . $conn->connect_error;
                    $success = false;
                } else {
                    $bookTitle = $_GET["book-title"] ?? '';
                    $bookGenre = $_GET["book-genre"] ?? '';
                    $bookAuthor = $_GET["book-author"] ?? '';
                    $bookPublisher = $_GET["book-publisher"] ?? '';
                    $ratingMinimum = intval($_GET["rating-minimum"] ?? 0);
                    $sortBy = $_GET["sortBy"] ??  '';
                    $sortOrder = $_GET["sort-order"] ?? '';

                    //Prepare statement
                    //Bind and execute query statement
                    $stmt = $conn->prepare("CALL search_books(?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("ssssiss", $bookTitle, $bookGenre, $bookAuthor, $bookPublisher, $ratingMinimum, $sortBy, $sortOrder);


                    //$stmt = $conn->prepare("INSERT INTO world_of_pets_members (fname, lname, email, password) VALUES ('jane','doe','jane@abc.com','123')");


                    if (!$stmt->execute()) {
                        echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
                    } else {
                        $result = $stmt->get_result();
                        // Check if there are rows returned
                        if ($result->num_rows > 0) {
                            $result->fetch_all(MYSQLI_ASSOC);
                            // Loop through the rows and fetch the data
                            foreach ($result as $row) {
                                // Access data using column names
                                $books[] = $row;
                                // Adjust column names as per your table structure
                            }
                        } else {
                            echo "<h4>No results found for the given search criteria.<h4>";
                        }
                    }
                }
                $conn->close();

                ?>

                <section>
                    <?php foreach ($books as $book) : ?>

                        <div class='col-md-4'>
                            <div class='featured-item'>
                                <div class='item-wrapper'>
                                    <div class='thumb'>
                                        <img src='<?= htmlspecialchars($book['productImage']) ?>' alt='Product Image' style='min-height: 400px; max-height: 400px'>
                                    </div>
                                    <div class='down-content'>
                                        <h4 style='min-height: 50px;'>Product Name: <?= htmlspecialchars($book['productName']) ?></h4>
                                        <span style='min-height: 20px;'><sup>Price: <?= htmlspecialchars(number_format((float)$book['price'], 2)) ?></sup></span>
                                        <p style='min-height: 20px;'>Product Author: <?= htmlspecialchars($book['bookAuthor']) ?></p>
                                        <p style='min-height: 20px;'>Product Publisher: <?= htmlspecialchars($book['bookPublisher']) ?></p>
                                        <div class='text-button'>
                                            <a href='/productDetails.php?id=<?= $book['productID'] ?>'>View More</a>
                                        </div>
                                    </div>

                                </div>

                            </div>
                        </div>

                    <?php endforeach; ?>
                </section>

    </main>
    <?php
    include "inc/footer.inc.php";
    ?>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script>
        window.jQuery || document.write('<script src="js/vendor/jquery-1.11.2.min.js"><\/script>');
    </script>
    <script src="js/vendor/bootstrap.min.js"></script>
    <script src="js/datepicker.js"></script>
    <script src="js/plugins.js"></script>
    <script>
        // 
        $(".dropdown-item").on("click", function(e) {
            e.preventDefault(); // don't scroll to top when clicking dropdown elements
            $(this).parent().prev().text($(this).text()); // Change the button text.
            $(this).parent().parent().next().val($(this).data("value")); // set the hidden input value
        });

        // display the slider value
        $("#rating-minimum").on("input", function(e) {
            document.getElementById('rangeval').innerText = $("#rating-minimum").val();
        });
    </script>
</body>

</html>