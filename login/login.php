<?php
    session_start();
?>

<?php
    include "../inc/head.inc.php";
?>

<body>
    <?php
        include "../inc/nav.inc.php";
        include "../inc/header.inc.php"
    ?>
    <main class="container">
        <h1> Member Login </h1>
        <p>
            Existing members log in here. For new members, please go to the <a href='/register/register.php'>Member Registration page</a>.
        </p>
        <form action=<?php echo "/login/process_login.php";?> method="post">
        <div class="mb-3">
            <label for="email" class="form-label">Email:</label>
            <input required maxlength="45" type="email" id="email" name="email" class="form-control" placeholder="Enter email">
        </div>
        <div class="mb-3">
            <label for="pwd" class="form-label">Password:</label>
            <input required type="password" id="pwd" name="pwd" class="form-control" placeholder="Enter password">
        </div>
        <div class="mb-3">
            <button type="submit">Submit</button>
        </div>
        <div id="errorMsg" class="mb-3"></div>
        </form>
    </main>
    <?php
    include "../inc/footer.inc.php";
    ?>
</body>
<?php
if(isset($_GET['errMsg'])) {
    $errMsg = urldecode($_GET['errMsg']);
    echo "<script>document.getElementById('errorMsg').innerHTML = '<p>" . $errMsg . "</p>';</script>";
}
?>
