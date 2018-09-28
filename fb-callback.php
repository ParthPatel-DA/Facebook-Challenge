<?php
    if (!session_id()) 
    {
        session_start(); 
    }
    require_once 'fb-config.php';
    try {
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
        $response = $fb->get("/me?fields=id,first_name,last_name,email",$accessToken);
        $userdata =$response->getGraphNode()->asArray();
        $_SESSION['userData']=$userdata;
        $_SESSION['access_token']=(string)$accessToken;
        header('location:index.php');
    } catch(Facebook\Exceptions\FacebookResponseException $e){
        header('location:error.html');
    }

    // $_SESSION['access_token'] = (string)$accessToken;
    // require_once 'login.php';

?>