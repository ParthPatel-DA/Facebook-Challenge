<?php
    if (!session_id()) 
    {
        session_start(); 
    }

    require_once('fb-config.php');

    $permissions = ['user_photos'];
    $accessToken =  $_SESSION['access_token'];  
    if (isset($accessToken)) 
    {
        $fb_json = json_decode(file_get_contents("lib/conf/fb-key.json"), true);
        $fb = new Facebook\Facebook([
        'app_id' => $fb_json["app-id"], // Replace {app-id} with your app id
        'app_secret' => $fb_json["app-secret"],
        'default_graph_version' => 'v2.2',
        'default_access_token' => isset($_SESSION['facebook_access_token']) ? $_SESSION['facebook_access_token']  : $fb_json["app-secret"]
        ]);
    
    

        $response = $fb->get('/me?fields=name,id,email,albums', $accessToken);
        $user = $response->getGraphuser();
        
        if(isset($_REQUEST['slidealbumid'])){
            $images = "true~";
            $albumID = $_REQUEST['slidealbumid'];
            $re = $fb->get('/'.$albumID.'?fields=name,photos.limit(100){images}',$accessToken);
            $graphEdge = $re->getGraphNode();
            $cnt=0;
            try{
                for($j=0;$j<count($graphEdge['photos']);$j++)
                {
                    $cnt++;
                    $images .="<div class='slide'><img src='".$graphEdge['photos'][$j]['images'][0]['source']."' alt='slide".$cnt."' width='100%' /></div>";
                }
                $a = $re->getDecodedBody();
                $str = $a['photos']['paging']['next'];
                if($str!=""){
                    $arr = explode("v3.1",$str);
                    $re = $fb->get($arr[1],$accessToken);
                    $graphEdge = $re->getGraphEdge();
                    $images1 = json_decode($graphEdge, true);
                    foreach($images1 as $img){
                        $cnt++;
                        $images .="<div class='slide'><img src='".$img['images'][0]['source']."' alt='slide".$cnt."' width='100%' /></div>";
                    }
                }
            }catch(Facebook\Exceptions\FacebookSDKException $e){
                echo "SDK Exception: ".$e->getMessage();
            }
            echo $images;
        }

        if(isset($_REQUEST['downloadsingle'])){
            $result = "true";
            $re = $fb->get('/'.$_REQUEST['downloadsingle'].'?fields=name,photos.limit(100){images}',$accessToken);
            $graphEdge = $re->getGraphNode();
            $album_id=$_REQUEST['downloadsingle'];
            $zip=new ZipArchive();
            try{
                if(file_exists('Downloads/'.$album_id.'.zip')){
                    unlink('Downloads/'.$album_id.'.zip');
                }
                $zip->open('Downloads/'.$album_id.'.zip', ZipArchive::CREATE);
                for($j=0;$j<count($graphEdge['photos']);$j++)
                {
                    $zip->addFromString($j.'.jpg', file_get_contents($graphEdge['photos'][$j]['images'][0]['source']));
                }
                $a = $re->getDecodedBody();
                $str = $a['photos']['paging']['next'];
                if($str!=""){
                    $arr = explode("v3.1",$str);
                    $re = $fb->get($arr[1],$accessToken);
                    $graphEdge = $re->getGraphEdge();
                    $images1 = json_decode($graphEdge, true);
                    foreach($images1 as $img){
                        $zip->addFromString($j.'.jpg', file_get_contents($img['images'][0]['source']));
                        $j++;
                    }
                }
                $zip->close();
                $result .= "~Downloads/".$album_id.".zip";
                echo $result;
            }catch(Facebook\Exceptions\FacebookSDKException $e){
                echo "SDK Exception: ".$e->getMessage();
            }
        }
        
        if(isset($_REQUEST['downloadall'])){
            $result = "true";
            if(file_exists('Downloads/'.$user['id']."_".$user['name'].'.zip')){
                unlink('Downloads/'.$user['id']."_".$user['name'].'.zip');
            }
            for($i=0;$i<count($user['albums']);$i++)
            {
                $re = $fb->get('/'.$user['albums'][$i]['id'].'?fields=name,photos.limit(100){images}',$accessToken);
                $graphEdge = $re->getGraphNode();
                $zip=new ZipArchive();
                try{
                    if(file_exists('Downloads/'.$user['id']."_".$user['name'].'.zip')){
                        unlink('Downloads/'.$user['id']."_".$user['name'].'.zip');
                    }
                    $zip->open('Downloads/'.$user['id']."_".$user['name'].'.zip', ZipArchive::CREATE);
                    for($j=0;$j<count($graphEdge['photos']);$j++)
                    {
                        $zip->addFromString($user['albums'][$i]['name']."/".$j.'.jpg', file_get_contents($graphEdge['photos'][$j]['images'][0]['source']));
                    }
                    $a = $re->getDecodedBody();
                    $str = $a['photos']['paging']['next'];
                    if($str!=""){
                        $arr = explode("v3.1",$str);
                        $re = $fb->get($arr[1],$accessToken);
                        $graphEdge = $re->getGraphEdge();
                        $images1 = json_decode($graphEdge, true);
                        foreach($images1 as $img){
                            $zip->addFromString($user['albums'][$i]['name']."/".$j.'.jpg', file_get_contents($img['images'][0]['source']));
                            $j++;
                        }
                    }
                    $zip->close();
                }catch(Facebook\Exceptions\FacebookSDKException $e){
                    echo "SDK Exception: ".$e->getMessage();
                }                
            }
            echo $result."~Downloads/".$user['id']."_".$user['name'].".zip";
        }

        if(isset($_REQUEST['downloadselected'])){
            $result = "true";
            if(file_exists('Downloads/'.$user['id']."_".$user['name'].'.zip')){
                unlink('Downloads/'.$user['id']."_".$user['name'].'.zip');
            }
            $selected_album_list=explode("/",$_REQUEST['downloadselected']);
            for($i = 0; $i < count($selected_album_list)-1; $i++){
                $album_IDs_Names = explode('-', $selected_album_list[$i]);
                
                $re = $fb->get('/'.$album_IDs_Names[0].'?fields=name,photos.limit(100){images}',$accessToken);
                $graphEdge = $re->getGraphNode();
                $zip=new ZipArchive();
                try{
                    if(file_exists('Downloads/'.$user['id']."_".$user['name'].'.zip')){
                        unlink('Downloads/'.$user['id']."_".$user['name'].'.zip');
                    }
                    $zip->open('Downloads/'.$user['id']."_".$user['name'].'.zip', ZipArchive::CREATE);
                    for($j=0;$j<count($graphEdge['photos']);$j++)
                    {
                        $zip->addFromString($user['albums'][$i]['name']."/".$j.'.jpg', file_get_contents($graphEdge['photos'][$j]['images'][0]['source']));
                    }
                    $a = $re->getDecodedBody();
                    $str = $a['photos']['paging']['next'];
                    if($str!=""){
                        $arr = explode("v3.1",$str);
                        $re = $fb->get($arr[1],$accessToken);
                        $graphEdge = $re->getGraphEdge();
                        $images1 = json_decode($graphEdge, true);
                        foreach($images1 as $img){
                            $zip->addFromString($user['albums'][$i]['name']."/".$j.'.jpg', file_get_contents($img['images'][0]['source']));
                            $j++;
                        }
                    }
                    $zip->close();
                }catch(Facebook\Exceptions\FacebookSDKException $e){
                    echo "SDK Exception: ".$e->getMessage();
                }
            }
            echo $result."~Downloads/".$user['id']."_".$user['name'].".zip";
        }

    } else {
        $fb_json = json_decode(file_get_contents("lib/conf/fb-key.json"), true);
        $loginUrl = $helper->getLoginUrl($fb_json["location"].'index.php', $permissions);
    }
?>
