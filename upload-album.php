<?php
if (!session_id()) 
{
    session_start(); 
}
require_once 'fb-config.php';
require_once 'lib/google-api-php-client/src/Google/Client.php';
require_once 'lib/google-api-php-client/src/Google/Service/Oauth2.php';
require_once 'lib/google-api-php-client/src/Google/Service/Drive.php';
require_once 'googleDrive-config.php';

$fb_json = json_decode(file_get_contents("lib/conf/fb-key.json"), true);
$fb = new Facebook\Facebook([
    'app_id' => $fb_json["app-id"], // Replace {app-id} with your app id
    'app_secret' => $fb_json["app-secret"],
    'default_graph_version' => 'v2.2',
    'default_access_token' => isset($_SESSION['facebook_access_token']) ? $_SESSION['facebook_access_token']  : $fb_json["app-secret"]
    ]);

    $accessToken = $_SESSION['access_token'];

if (isset($_REQUEST['uploadAlbum']) && isset($_REQUEST['albumName'])) {

    $driveInfo = "";
    $folderName = "";
    $folderDesc = "";

    $albumID = $_REQUEST['uploadAlbum'];
    $albumName = $_REQUEST['albumName'];

    // Get the client Google credentials
    $credentials = $_COOKIE["credentials"];
    $json = json_decode(file_get_contents("lib/conf/GoogleClientId.json"), true);
    // Get your app info from JSON downloaded from google dev console
    $CLIENT_ID = $json['web']['client_id'];
    $CLIENT_SECRET = $json['web']['client_secret'];
    $REDIRECT_URI = $json['web']['redirect_uris'][3];

    // Create a new Client
    $client = new Google_Client();
    $client->setClientId($CLIENT_ID);
    $client->setClientSecret($CLIENT_SECRET);
    $client->setRedirectUri($REDIRECT_URI);
    $client->addScope(
        "https://www.googleapis.com/auth/drive",
        "https://www.googleapis.com/auth/drive.appfolder"
    );

    // Refresh the user token and grand the privileges
    $client->setAccessToken($credentials);
    $service = new Google_Service_Drive($client);


    $re = $fb->get('/'.$albumID.'?fields=name,photos.limit(100){images}',$accessToken);
    $graphEdge = $re->getGraphNode();
    $masterFolderName = "Facebook_".$_SESSION['Name']."_Albums";
    $masterFolder = getFolderExistsCreate($service, $masterFolderName, $folderDesc, "NULL");
    $albumFolder = getFolderExistsCreate($service, $albumName, $folderDesc, $masterFolder);
    try{
        for($j=0;$j<count($graphEdge['photos']);$j++)
        {
            $mimeType = 'image/jpeg';
            $title = $j.".jpg";
            $description = "Facebook Album Image";
            $file_tmp_name = $graphEdge['photos'][$j]['images'][0]['source'];
            // Call the insert function with parameters listed below
            $driveInfo = insertFile($service, $title, $description, $mimeType, $file_tmp_name, $albumFolder);
        }
        $a = $re->getDecodedBody();
        $str = $a['photos']['paging']['next'];
        if($str!=""){
            $arr = explode("v3.1",$str);
            $re = $fb->get($arr[1],$accessToken);
            $graphEdge = $re->getGraphEdge();
            $images1 = json_decode($graphEdge, true);
            foreach($images1 as $img){
                $file_tmp_name = $img['images'][0]['source'];
            
                // Set the file metadata for drive
                $mimeType = 'image/jpeg';
                $title = $j.".jpg";
                $description = "Facebook Album Image";
                $j++;
                // Call the insert function with parameters listed below
                $driveInfo = insertFile($service, $title, $description, $mimeType, $file_tmp_name, $albumFolder);
            }
        }
    }catch(Facebook\Exceptions\FacebookSDKException $e){
        echo "SDK Exception: ".$e->getMessage();
    }
    echo "true";
}

if (isset($_REQUEST['uploadAlbums'])) {
    // Init the variables
    $driveInfo = "";
    $folderName = "";
    $folderDesc = "";

    $albums = $_REQUEST['uploadAlbums'];
    // Get the client Google credentials
    $credentials = $_COOKIE["credentials"];
    $json = json_decode(file_get_contents("lib/conf/GoogleClientId.json"), true);
    
    $CLIENT_ID = $json['web']['client_id'];
    $CLIENT_SECRET = $json['web']['client_secret'];
    $REDIRECT_URI = $json['web']['redirect_uris'][3];

    // Create a new Client
    $client = new Google_Client();
    $client->setClientId($CLIENT_ID);
    $client->setClientSecret($CLIENT_SECRET);
    $client->setRedirectUri($REDIRECT_URI);
    $client->addScope(
        "https://www.googleapis.com/auth/drive",
        "https://www.googleapis.com/auth/drive.appfolder"
    );

    // Refresh the user token and grand the privileges
    $client->setAccessToken($credentials);
    $service = new Google_Service_Drive($client);

    $arrAlbums = explode('/', $albums);

    foreach ($arrAlbums as $album) {
        $album_IDs_Names = explode('-', $album);

        $re = $fb->get('/'.$album_IDs_Names[0].'?fields=name,photos.limit(100){images}',$accessToken);
        $graphEdge = $re->getGraphNode();
        $masterFolderName = "Facebook_".$_SESSION['Name']."_Albums";
        $masterFolder = getFolderExistsCreate($service, $masterFolderName, $folderDesc, "NULL");
        $albumFolder = getFolderExistsCreate($service, $album_IDs_Names[1], $folderDesc, $masterFolder);
        try{
            for($j=0;$j<count($graphEdge['photos']);$j++)
            {
                $mimeType = 'image/jpeg';
                $title = $j.".jpg";
                $description = "Facebook Album Image";
                $file_tmp_name = $graphEdge['photos'][$j]['images'][0]['source'];
                // Call the insert function with parameters listed below
                $driveInfo = insertFile($service, $title, $description, $mimeType, $file_tmp_name, $albumFolder);
            }
            $a = $re->getDecodedBody();
            $str = $a['photos']['paging']['next'];
            if($str!=""){
                $arr = explode("v3.1",$str);
                $re = $fb->get($arr[1],$accessToken);
                $graphEdge = $re->getGraphEdge();
                $images1 = json_decode($graphEdge, true);
                foreach($images1 as $img){
                    $file_tmp_name = $img['images'][0]['source'];
                
                    // Set the file metadata for drive
                    $mimeType = 'image/jpeg';
                    $title = $j.".jpg";
                    $description = "Facebook Album Image";
                    $j++;
                    // Call the insert function with parameters listed below
                    $driveInfo = insertFile($service, $title, $description, $mimeType, $file_tmp_name, $albumFolder);
                }
            }
        }catch(Facebook\Exceptions\FacebookSDKException $e){
            echo "SDK Exception: ".$e->getMessage();
        }
    }
    echo "true";
}
?>