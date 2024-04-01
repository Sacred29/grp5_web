<?php
    session_start();
?>

<?php
    include "inc/head.inc.php";
?>
<body>
    <?php
    include "inc/nav.inc.php";
    ?>
    <main class="container">
        <h1>Member Registration</h1>
        <form action="process_register.php" method="post">
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
                <input required type="password" id="pwd_confirm" name="pwd_confirm"  class="form-control"placeholder="Confirm password">
                <!-- <input id="pwd_confirm" name="pwd_confirm"  class="form-control"placeholder="Confirm password"> -->
            </div>
            <?php
            if ($_SESSION['user_privilege'] !== 'staff') {
            ?>
            <div>
                <label>User Privilege:</label>
                    <select name="userPrivilege">
                        <option value="user" <?php echo (isset($user['userPrivilege']) && $user['userPrivilege'] == 'user') ? 'selected' : ''; ?>>User</option>
                        <option value="staff" <?php echo (isset($user['userPrivilege']) && $user['userPrivilege'] == 'staff') ? 'selected' : ''; ?>>Staff</option>
                    </select>
            </div>
            <?php
                }
            ?>
            <div class="mb-3">
                <button type="submit">Submit</button>
            </div>
        </form>
    </main>
    <?php
    include "inc/footer.inc.php";
    ?>
</body>
