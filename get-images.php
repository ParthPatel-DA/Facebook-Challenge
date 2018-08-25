<?php

    require_once('fb-config.php');

    $permissions = ['user_photos'];
    $accessToken =  $_SESSION['access_token'];  
    $logoutURL = $helper->getLogoutUrl($accessToken, $redirectURL.'logout.php');
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
                header('Content-Disposition: attachment; filename=Downloads/'.$album_id.'.zip');
                header('Content-Type: application/zip');
                readfile('Downloads/'.$album_id.'.zip');
                header("location:display-album.php");
                echo "<script>alert('Album successfully downloaded');</script>";
            }catch(Facebook\Exceptions\FacebookSDKException $e){
                echo "SDK Exception: ".$e->getMessage();
            }
        }
        
        if(isset($_REQUEST['downloadall'])){
            // $album_list="";
            for($i=0;$i<count($user['albums']);$i++)
            {
                // $album_list .= (string)$user['albums'][$i]['id'] . "/";
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
                    header('Content-Disposition: attachment; filename=Downloads/'.$album_id.'.zip');
                    header('Content-Type: application/zip');
                    readfile('Downloads/'.$album_id.'.zip');
                    echo $album_id.".php";
                    // echo "<script>alert('Album successfully downloaded');</script>";
                }catch(Facebook\Exceptions\FacebookSDKException $e){
                    echo "SDK Exception: ".$e->getMessage();
                }
                
            }

            // $selected_album_list=explode("/",$album_list);
            // print_r($selected_album_list);
            // for($i=1;$i<=count($selected_album_list);$i++)
            // {
            //     $re = $fb->get('/'.$selected_album_list[$i].'/photos?limit=100',$accessToken);
            //     $graphEdge = $re->getGraphEdge();
            //     $album_id=$_GET['albumid'];
            //     $zip=new ZipArchive();
            //     try{
            //         $zip->open('Downloads/'.$selected_album_list[$i].'.zip', ZipArchive::CREATE);
            //         ini_set('max_execution_time', 300);
            //         for($j=0;$j<count($graphEdge);$j++)
            //         {
            //             $res = $fb->get('/'.$graphEdge[$j]['id'].'?fields=images',$accessToken);
            //             $graphNode = $res->getGraphNode();
            //             $path=$graphNode['images'][0]; 
            //             $image_path=$path['source'];
            //             $zip->addFromString($j.'.jpg', file_get_contents($image_path));
            //         }
            //         $zip->close();
            //         header('Content-Disposition: attachment; filename=Downloads/'.$selected_album_list[$i].'.zip');
            //         header('Content-Type: application/zip');
            //         readfile('Downloads/'.$selected_album_list[$i].'.zip');
            //         header("location:display-album.php");
            //         echo '<script>alert("Album successfully downloaded");</script>';
            //     }catch(Facebook\Exceptions\FacebookSDKException $e){
            //         echo "SDK Exception: ".$e->getMessage();
            //     }
            // }
        }

        if(isset($_REQUEST['downloadselected'])){
            // $album_list=;
            $selected_album_list=explode("/",$_REQUEST['downloadselected']);
            // print_r($selected_album_list);
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
                    header('Content-Disposition: attachment; filename=Downloads/'.$album_id.'.zip');
                    header('Content-Type: application/zip');
                    readfile('Downloads/'.$album_id.'.zip');
                    // echo $album_id.".php";
                    // echo "<script>alert('Album successfully downloaded');</script>";
                }catch(Facebook\Exceptions\FacebookSDKException $e){
                    echo "SDK Exception: ".$e->getMessage();
                }
            }
        }

    } else {
        $loginUrl = $helper->getLoginUrl('https://localhost/RTCamp/index.php', $permissions);
    }
?>