<?php
    if (!session_id()) 
    {
        session_start(); 
    }

    require_once('fb-config.php');
    require_once('getallimages.php');

    $permissions = ['user_photos'];
    $accessToken =  $_SESSION['access_token'];  
    if (isset($accessToken)) 
    {
        $response = $fb->get('/me?fields=name,id,email,albums', $accessToken);
        $user = $response->getGraphuser();
        
        if(isset($_REQUEST['slidealbumid'])){
            $images = "true~";
            $albumID = $_REQUEST['slidealbumid'];
            $AllAlbums = getAllImages($albumID, $fb);
            $arr = explode("~",$AllAlbums);
            for($j=0;$j<count($arr)-1;$j++)
            {
                $images .="<div class='slide'><img src='".$arr[$j]."' alt='slide".($j+1)."' width='100%' /></div>";
            }
            echo $images;
        }

        if(isset($_REQUEST['downloadsingle'])){
            $result = "true";
            $album_id=$_REQUEST['downloadsingle'];
            $AllAlbums = getAllImages($album_id, $fb);
            $arr = explode("~",$AllAlbums);
            $zip=new ZipArchive();
            try{
                if(file_exists('Downloads/'.$album_id.'.zip')){
                    unlink('Downloads/'.$album_id.'.zip');
                }
                $zip->open('Downloads/'.$album_id.'.zip', ZipArchive::CREATE);
                for($j=0;$j<count($arr)-1;$j++)
                {
                    $zip->addFromString($j.'.jpg', file_get_contents($arr[$j]));
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
                $AllAlbums = getAllImages($user['albums'][$i]['id'], $fb);
                $arr = explode("~",$AllAlbums);
                $zip=new ZipArchive();
                try{
                    $zip->open('Downloads/'.$user['id']."_".$user['name'].'.zip', ZipArchive::CREATE);
                    $zip->addEmptyDir($user['albums'][$i]['name']);
                    for($j=0;$j<count($arr)-1;$j++)
                    {
                        $zip->addFromString($user['albums'][$i]['name']."/".$j.'.jpg', file_get_contents($arr[$j]));
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
                $AllAlbums = getAllImages($album_IDs_Names[0], $fb);
                $arr = explode("~",$AllAlbums);
                $zip=new ZipArchive();
                try{
                    $zip->open('Downloads/'.$user['id']."_".$user['name'].'.zip', ZipArchive::CREATE);
                    $zip->addEmptyDir($album_IDs_Names[1]);
                    for($j=0;$j<count($arr)-1;$j++)
                    {
                        $zip->addFromString($album_IDs_Names[1]."/".$j.'.jpg', file_get_contents($arr[$j]));
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
