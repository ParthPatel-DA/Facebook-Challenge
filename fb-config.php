<?php 

    require_once 'lib/Facebook/autoload.php';

    if (!session_id()) 
    {
        session_start(); 
    }
    
    $FB = new \Facebook\Facebook([
        'app_id' => '269606253764691',
        'app_secret' => 'd16a59604495daf88b6e96d112b51415',
        'default_graph_version' => 'v2.2'
        ]);

        $helper = $FB->getRedirectLoginHelper();
        $redirectURL   = 'https://localhost/RTCamp/';

?>