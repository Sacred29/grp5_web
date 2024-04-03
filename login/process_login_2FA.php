<!DOCTYPE html>
<html lang="en">
<?php
?>

<?php
    include "../inc/head.inc.php";
    echo '<script src="https://kit.fontawesome.com/777528c189.js" crossorigin="anonymous"></script>';
?>

<body>
    <?php
        include "./../inc/nav.inc.php";
        include "./../inc/header.inc.php"
    ?>
    <main>
        <?php
        $redirect_Success="process_login_privilege.php";
        $redirect_Fail="process_login_2FA.php";
        include "./../otpService/otpValidate.php";
        ?>
    </main>
    <?php
        include "./../inc/footer.inc.php";
    ?>
</body>
</html>