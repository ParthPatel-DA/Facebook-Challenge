<?php
        require_once "googleDrive-config.php";

        global $CLIENT_ID, $CLIENT_SECRET, $REDIRECT_URI;
        $client = new Google_Client();
        $client->setClientId($CLIENT_ID);
        $client->setClientSecret($CLIENT_SECRET);
        $client->setRedirectUri($REDIRECT_URI);
        $client->setScopes('email');

        $authUrl = $client->createAuthUrl();

    if (isset($_REQUEST['code'])) {
        getCredentials($_REQUEST['code'], $authUrl);
        header("Location: index.php");
    } else {
        $googleAuthUrl = getAuthorizationUrl("", "");
        echo "<script>window.location = \"".$googleAuthUrl."\"</script>";
    }
?>
