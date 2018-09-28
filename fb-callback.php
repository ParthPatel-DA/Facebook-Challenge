<?php
    // start session, if not started
    if (!session_id()) 
    {
        session_start(); 
    }
    require_once 'fb-config.php';
    try {
        // get accessToken of facebook for accessing the data
        $accessToken = $helper->getAccessToken();
    } catch (Facebook\Exceptions\FacebookResponseException $e) {
        echo 'Graph returned an error: ' . $e->getMessage();
        exit;
    } catch (Facebook\Exceptions\FacebookSDKException $e) {
        echo 'Facebook SDK returned an error: ' . $e->getMessage();
        exit;
    }

    if(!$accessToken){
        header('Location:login.php');
        exit();
    }
    try{
        $OAuth2Client = $fb->getOAuth2Client();
        if(!$accessToken->isLongLived())
        {
            $accessToken =$OAuth2Client->getLongLivedAceessToken($accessToken);
        }
        // set accessToken in session for further use
        $_SESSION['access_token']=(string)$accessToken;
        // redirect on index(album) page
        header('location:index.php');
    } catch(Facebook\Exceptions\FacebookResponseException $e){
        // if get error than redirect on error page
        header('location:error.html');
    }

?>