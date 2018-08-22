<?php

    require_once 'fb-config.php';
    // require_once 'index.php';
    // $fb = new Facebook\Facebook([
    //     'app_id' => '534582190322560', // Replace {app-id} with your app id
    //     'app_secret' => 'aabf7ce7f242d17621318df37f45478b',
    //     'default_graph_version' => 'v2.2',
    //     'default_access_token' => isset($_SESSION['facebook_access_token']) ? $_SESSION['facebook_access_token'] : 'aabf7ce7f242d17621318df37f45478b'
    //     ]);

    // $helper = $fb->getRedirectLoginHelper();
    // if(isset($_GET['state'])){
    // $helper->getPersistentDataHandler()->set('state',$_GET['state']);
    // }

    // session_start();

    try {
        $accessToken = $helper->getAccessToken();
    } catch (Facebook\Exceptions\FacebookResponseException $e) {
        // When Graph returns an error
        echo 'Graph returned an error: ' . $e->getMessage();
        exit;
    } catch (Facebook\Exceptions\FacebookSDKException $e) {
        // When validation fails or other local issues
        echo 'Facebook SDK returned an error: ' . $e->getMessage();
        exit;
    }

    if(!$accessToken){
        header('Location:login.php');
        exit();
    }

    $OAuth2Client = $FB->getOAuth2Client();
    if(!$accessToken->isLongLived())
    {
        $accessToken =$OAuth2Client->getLongLivedAceessToken($accessToken);
    }
    $response = $FB->get("/me?fields=id,first_name,last_name,email",$accessToken);
    $userdata =$response->getGraphNode()->asArray();
    $_SESSION['userData']=$userdata;
    $_SESSION['access_token']=(string)$accessToken;
    header('location:index.php');

    $_SESSION['access_token'] = (string)$accessToken;
    require_once 'login.php';

?>