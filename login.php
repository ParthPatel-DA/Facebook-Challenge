<?php
    require_once('fb-config.php');
    $permissions = ['email,user_photos']; // Optional permissions
    $loginUrl = $helper->getLoginUrl('https://localhost/RTCamp/fb-callback.php', $permissions);

    echo '<a href="' . htmlspecialchars($loginUrl) . '">Log in with Facebook!</a>';
?>