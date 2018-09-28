<?php
    session_start();
    // unset value of session and then destroy it
    unset($_SESSION['access_token']);
    if (session_destroy()) {
        header("Location: login.php");
    }
?>