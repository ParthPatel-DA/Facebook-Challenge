<?php 

    require_once 'lib/Facebook/autoload.php';

    if (!session_id()) 
    {
        session_start(); 
    }
    
    $FB = new \Facebook\Facebook([
        'app_id' => '534582190322560',
        'app_secret' => 'aabf7ce7f242d17621318df37f45478b',
        'default_graph_version' => 'v2.2'
        ]);

        $helper = $FB->getRedirectLoginHelper();
        $redirectURL   = 'https://localhost/RTCamp/';

?>