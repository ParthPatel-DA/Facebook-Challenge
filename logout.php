<?php
    session_start();
    unset($_SESSION['access_token']);
    if (session_destroy()) {
        header("Location: login.php");
    }
?>