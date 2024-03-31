<?php
include "inc/head.inc.php";
?>

<body>
    <?php
    include "inc/nav.inc.php";
    ?>
    <main class="container">
        <h1>Change password</h1>
        <form id="password-form" action="process_password_change.php" method="post">
            <div class="mb-3">
                <label for="pwd" class="form-label">Enter your current password:</label>
                <input required type="password" id="pwd_current" name="pwd_cur" class="form-control" placeholder="Enter current password">
                <!-- <input id="pwd" name="pwd" class="form-control" placeholder="Enter password"> -->
            </div>
            <div class="mb-3">
                <label for="pwd" class="form-label">Enter your new password:</label>
                <input required type="password" id="pwd_new" name="pwd_new" class="form-control" placeholder="Enter new password">
                <!-- <input id="pwd" name="pwd" class="form-control" placeholder="Enter password"> -->
            </div>
            <div class="mb-3">
                <label for="pwd_confirm" class="form-label">Confirm new password:</label>
                <input required type="password" id="pwd_confirm" name="pwd_confirm" class="form-control" placeholder="Confirm new password">
                <!-- <input id="pwd_confirm" name="pwd_confirm"  class="form-control"placeholder="Confirm password"> -->
            </div>
            <div class="mb-3">
                <button type="submit">Submit</button>
            </div>
        </form>
    </main>
    <?php
    include "inc/footer.inc.php";
    ?>
</body>