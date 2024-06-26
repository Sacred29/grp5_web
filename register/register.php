<!DOCTYPE html>
<html lang="en">
<?php
session_start();
?>

<?php
include "./../inc/head.inc.php";
?>
<script src="https://www.google.com/recaptcha/api.js?render=6Le0e7MZAAAAAJDAnFTrhlM8DJ1u-Fvi3N702bD7"></script>
<script>
    grecaptcha.ready(() => {
        document.getElementById('register-form').addEventListener("submit", function(event) {
            event.preventDefault();
            grecaptcha.execute('6Le0e7MZAAAAAJDAnFTrhlM8DJ1u-Fvi3N702bD7', {
                action: 'signup'
            }).then(token => {
                document.querySelector('#recaptchaResponse').value = token;
                document.getElementById('register-form').submit();
            });
        }, false);
    });
</script>

<body>
    <?php
    include "./../inc/nav.inc.php";
    ?>
    <main class="container">
        <h1>Member Registration</h1>
        <p>
            For existing members, please go to the
            <a href="/login/login.php">Sign In page</a>.
        </p>
        <form id="register-form" action="process_register.php" method="post">
            <div class="mb-3">
                <label for="fname" class="form-label">First Name:</label>
                <input maxlength="45" type="text" id="fname" name="fname" class="form-control" placeholder="Enter first name">
            </div>
            <div class="mb-3">
                <label for="lname" class="form-label">Last Name:</label>
                <input required maxlength="45" type="text" id="lname" name="lname" class="form-control" placeholder="Enter last name">
                <!-- <input id="lname" name="lname" class="form-control" placeholder="Enter last name"> -->
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email:</label>
                <input required maxlength="45" type="email" id="email" name="email" class="form-control" placeholder="Enter email">
                <!-- <input id="email" name="email" class="form-control" placeholder="Enter email"> -->
            </div>
            <div class="mb-3">
                <label for="pwd" class="form-label">Password:</label>
                <input required type="password" id="pwd" name="pwd" class="form-control" placeholder="Enter password">
                <!-- <input id="pwd" name="pwd" class="form-control" placeholder="Enter password"> -->
            </div>
            <div class="mb-3">
                <label for="pwd_confirm" class="form-label">Confirm Password:</label>
                <input required type="password" id="pwd_confirm" name="pwd_confirm" class="form-control" placeholder="Confirm password">
                <!-- <input id="pwd_confirm" name="pwd_confirm"  class="form-control"placeholder="Confirm password"> -->
            </div>
            <?php if (!isset($_SESSION["user_privilege"]) || $_SESSION["user_privilege"] == "user") : ?>
                <div class="mb-3 form-check">
                    <input required type="checkbox" name="agree" class="form-check-input" id="agree">
                    <label class="form-check-label" for="agree">Agree to terms and conditions.</label>
                </div>
            <?php endif; ?>
            <div type="hidden" class="mb-3 form-check">
                <input type="hidden" name="prevpage" id="prevpage" value="register.php" />
            </div>
            <?php
            if (isset($_SESSION["user_privilege"]) && $_SESSION['user_privilege'] == "admin") {
                echo '<div class="mb-3">
                <label for="user_privilege">Select the user type:</label>
                <select id="user_privilege" name="user_privilege">
                    <option value="user">User</option>
                    <option value="staff">Staff</option>
                    <option value="admin">Admin</option>
                </select></div>';
            }
            ?>
            <div class="mb-3">
                <input type="hidden" name="recaptcha_response" id="recaptchaResponse">
                <button type="submit">Submit</button>
            </div>
            <div id="errorMsg" class="mb-3"></div>
        </form>
    </main>
    <?php
    include "./../inc/footer.inc.php";
    ?>
</body>
<?php
if (isset($_GET['errMsg'])) {
    $errMsg = urldecode($_GET['errMsg']);
    echo "<script>document.getElementById('errorMsg').innerHTML = '<p>" . $errMsg . "</p>';</script>";
}
?>

</html>