<?php
    require_once 'lib/Facebook/autoload.php';

    if (!session_id()) 
    {
        session_start(); 
    }
    $fb_json = json_decode(file_get_contents("lib/conf/fb-key.json"), true);
    $FB = new \Facebook\Facebook([
        'app_id' => $fb_json["app-id"],
        'app_secret' => $fb_json["app-secret"],
        'default_graph_version' => 'v2.2'
        ]);

        $helper = $FB->getRedirectLoginHelper();
        $redirectURL   = 'https://localhost/RTCamp/';

?>