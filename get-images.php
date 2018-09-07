<?php

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
            try{
                $images = "true~";
                $albumID = $_REQUEST['slidealbumid'];
                $response = $fb->get('/'.$albumID.'/photos?limit=100', $accessToken);
                $cnt=0;
                $a = $response->getGraphEdge();
                ini_set('max_execution_time', 300);
                for($j=0;$j<count($a);$j++){
                    $response = $fb->get('/'.$a[$j]['id'].'?fields=images', $accessToken);
                    $graphNode = $response->getGraphNode();
                    $path=$graphNode['images'][0]; 
                    $image_path=$path['source'];
                    // $b=$response->getGraphNode()['images'][$j];
                    if($cnt==0){
                        // $images .= "<img src='".$b['source']."' alt style='animation: fadey 8000ms ease 0s 1 normal none running;' width='100%' height='100%'>";
                        $cnt++;
                        $images .="<div class='slide active-slide fadeIn'><img src='".$image_path."' alt='slide".$cnt."' width='100%' /></div>";
                    }
                    else{
                        // $images .= "<img src='".$b['source']."' alt>";
                        $cnt++;
                        $images .="<div class='slide'><img src='".$image_path."' alt='slide".$cnt."' width='100%' /></div>";
                    }              
                }
                $nextImg = $fb->next($a);
                $data_json=json_decode($nextImg, true);
                if($data_json != ""){
                    foreach($data_json as $img){
                        $response = $fb->get('/'.$img['id'].'?fields=images', $accessToken);
                        $graphNode = $response->getGraphNode();
                        $path=$graphNode['images'][0]; 
                        $image_path=$path['source'];
                        // $b=$response->getGraphNode()['images'][$j];
                        $cnt++;
                        $images .="<div class='slide'><img src='".$image_path."' alt='slide".$cnt."' width='100%' /></div>";
                    }
                }
            }
            catch(Exception $e){
                echo $images."<div class='controls'><button id='prev'>&lt;</button><button id='next'>&gt;</button></div>_fail";
            }
            echo $images."<div class='controls'><button id='prev'>&lt;</button><button id='next'>&gt;</button></div>";
        }

        if(isset($_REQUEST['downloadsingle'])){
            $result = "true";
            $re = $fb->get('/'.$_REQUEST['downloadsingle'].'/photos?limit=100',$accessToken);
            $graphEdge = $re->getGraphEdge();
            $album_id=$_REQUEST['downloadsingle'];
            $zip=new ZipArchive();
            try{
                if(file_exists('Downloads/'.$album_id.'.zip')){
                    unlink('Downloads/'.$album_id.'.zip');
                }
                $zip->open('Downloads/'.$album_id.'.zip', ZipArchive::CREATE);
                ini_set('max_execution_time', 300);
                for($j=0;$j<count($graphEdge);$j++)
                {
                    $res = $fb->get('/'.$graphEdge[$j]['id'].'?fields=images',$accessToken);
                    $graphNode = $res->getGraphNode();
                    $path=$graphNode['images'][0]; 
                    $image_path=$path['source'];
                    $zip->addFromString($j.'.jpg', file_get_contents($image_path));
                }
                $nextImg = $fb->next($graphEdge);
                $data_json=json_decode($nextImg, true);
                if($data_json != ""){
                    foreach($data_json as $img){
                        $res = $fb->get('/'.$img['id'].'?fields=images',$accessToken);
                        $graphNode = $res->getGraphNode();
                        $path=$graphNode['images'][0]; 
                        $image_path=$path['source'];
                        $zip->addFromString($j.'.jpg', file_get_contents($image_path));
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
                $re = $fb->get('/'.$user['albums'][$i]['id'].'/photos?limit=100',$accessToken);
                $graphEdge = $re->getGraphEdge();
                $zip=new ZipArchive();
                try{
                    $zip->open('Downloads/'.$user['id']."_".$user['name'].'.zip', ZipArchive::CREATE);
                    ini_set('max_execution_time', 300);
                    $zip->addEmptyDir($user['albums'][$i]['name']);
                    for($j=0;$j<count($graphEdge);$j++)
                    {
                        $res = $fb->get('/'.$graphEdge[$j]['id'].'?fields=images',$accessToken);
                        $graphNode = $res->getGraphNode();
                        $path=$graphNode['images'][0]; 
                        $image_path=$path['source'];
                        $zip->addFromString($user['albums'][$i]['name']."/".$j.'.jpg', file_get_contents($image_path));
                    }
                    $nextImg = $fb->next($graphEdge);
                    $data_json=json_decode($nextImg, true);
                    if($data_json != ""){
                        foreach($data_json as $img){
                            $res = $fb->get('/'.$img['id'].'?fields=images',$accessToken);
                            $graphNode = $res->getGraphNode();
                            $path=$graphNode['images'][0]; 
                            $image_path=$path['source'];
                            $zip->addFromString($user['albums'][$i]['name']."/".$j.'.jpg', file_get_contents($image_path));
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
                $re = $fb->get('/'.$album_IDs_Names[0].'/photos?limit=100',$accessToken);
                $graphEdge = $re->getGraphEdge();
                $album_id=$album_IDs_Names[1];
                $zip=new ZipArchive();
                try{
                    $zip->open('Downloads/'.$user['id']."_".$user['name'].'.zip', ZipArchive::CREATE);
                    ini_set('max_execution_time', 300);
                    $zip->addEmptyDir($album_id);
                    for($j=0;$j<count($graphEdge);$j++)
                    {
                        $res = $fb->get('/'.$graphEdge[$j]['id'].'?fields=images',$accessToken);
                        $graphNode = $res->getGraphNode();
                        $path=$graphNode['images'][0]; 
                        $image_path=$path['source'];
                        $zip->addFromString($album_id."/".$j.'.jpg', file_get_contents($image_path));
                    }
                    $nextImg = $fb->next($graphEdge);
                    $data_json=json_decode($nextImg, true);
                    if($data_json != ""){
                        foreach($data_json as $img){
                            $res = $fb->get('/'.$img['id'].'?fields=images',$accessToken);
                            $graphNode = $res->getGraphNode();
                            $path=$graphNode['images'][0]; 
                            $image_path=$path['source'];
                            $zip->addFromString($user['albums'][$i]['name']."/".$j.'.jpg', file_get_contents($image_path));
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
        $loginUrl = $helper->getLoginUrl('https://localhost/Facebook-Challenge/index.php', $permissions);
    }
?>
