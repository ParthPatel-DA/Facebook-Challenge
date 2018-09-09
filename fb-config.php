<?php
    require_once 'lib/Facebook/autoload.php';

    $fb_json = json_decode(file_get_contents("lib/conf/fb-key.json"), true);
    $FB = new \Facebook\Facebook([
        'app_id' => $fb_json["app-id"],
        'app_secret' => $fb_json["app-secret"],
        'default_graph_version' => 'v2.2'
        ]);

        $helper = $FB->getRedirectLoginHelper();
        if(isset($_GET['state'])){
            $helper->getPersistentDataHandler()->set('state',$_GET['state']);
        }
        // $redirectURL   = 'https://localhost/RTCamp/';

?>