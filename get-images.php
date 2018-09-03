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
            $images = "";
            $albumID = $_REQUEST['slidealbumid'];
            $response = $fb->get('/'.$albumID.'/photos?limit=100', $accessToken);
            $cnt=0;
            $a = $response->getGraphEdge();
            for($j=0;$j<count($a);$j++){
                $response = $fb->get('/'.$a[$j]['id'].'?fields=link,name,id,created_time,images,picture', $accessToken);
                $b=$response->getGraphNode()['images'][$j];
                if($cnt==0){
                    $images .= "<img src='".$b['source']."' alt style='animation: fadey 8000ms ease 0s 1 normal none running;' width='100%' height='100%'>";
                    $cnt++;
                }
                else{
                    $images .= "<img src='".$b['source']."' alt>";
                }              
            }
            echo $images;
        }

        if(isset($_REQUEST['downloadsingle'])){
            $result = "true";
            $re = $fb->get('/'.$_REQUEST['downloadsingle'].'/photos?limit=100',$accessToken);
            $graphEdge = $re->getGraphEdge();
            $album_id=$_REQUEST['downloadsingle'];
            $zip=new ZipArchive();
            try{
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
                $zip->close();
                $result .= "_Downloads/".$album_id.".zip";
                echo $result;
            }catch(Facebook\Exceptions\FacebookSDKException $e){
                echo "SDK Exception: ".$e->getMessage();
            }
        }
        
        if(isset($_REQUEST['downloadall'])){
            $result = "true";
            for($i=0;$i<count($user['albums']);$i++)
            {
                $re = $fb->get('/'.$user['albums'][$i]['id'].'/photos?limit=100',$accessToken);
                $graphEdge = $re->getGraphEdge();
                $album_id=$user['albums'][$i]['id'];
                $zip=new ZipArchive();
                try{
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
                    $zip->close();
                    $result .= "_Downloads/".$album_id.".zip";
                }catch(Facebook\Exceptions\FacebookSDKException $e){
                    echo "SDK Exception: ".$e->getMessage();
                }
                
            }
            echo $result;
        }

        if(isset($_REQUEST['downloadselected'])){
            $result = "true";
            $selected_album_list=explode("/",$_REQUEST['downloadselected']);
            for($i = 0; $i < count($selected_album_list)-1; $i++){
                $album_IDs_Names = explode('-', $selected_album_list[$i]);
                $re = $fb->get('/'.$album_IDs_Names[0].'/photos?limit=100',$accessToken);
                $graphEdge = $re->getGraphEdge();
                $album_id=$album_IDs_Names[1];
                $zip=new ZipArchive();
                try{
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
                    $zip->close();
                    $result .= "_Downloads/".$album_id.".zip";
                }catch(Facebook\Exceptions\FacebookSDKException $e){
                    echo "SDK Exception: ".$e->getMessage();
                }
            }
            echo $result;
        }

    } else {
        $loginUrl = $helper->getLoginUrl('https://localhost/Facebook-Challenge/index.php', $permissions);
    }
?>
