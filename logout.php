<?php
    session_start();
    // Destroy all session data.
    session_destroy();
    // Redirect to the homepage after logging out.
    header("Location: index.php");
    exit();
?>