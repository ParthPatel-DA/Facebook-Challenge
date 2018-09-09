<?php
        require_once "googleDrive-config.php";
        session_start();
        global $CLIENT_ID, $CLIENT_SECRET, $REDIRECT_URI;
        $client = new Google_Client();
        $client->setClientId($CLIENT_ID);
        $client->setClientSecret($CLIENT_SECRET);
        $client->setRedirectUri($REDIRECT_URI);
        $client->setScopes('email');
        $client->setAccessType('offline');
        $client->setApprovalPrompt('force');
        $authUrl = $client->createAuthUrl();

    if (isset($_GET['code'])) {
        getCredentials($_GET['code'], $authUrl);
        echo "<script>window.location = 'index.php'</script>";
    } else {
        $googleAuthUrl = getAuthorizationUrl("", "");
        echo "<script>window.location = \"".$googleAuthUrl."\"</script>";
    }
?>
