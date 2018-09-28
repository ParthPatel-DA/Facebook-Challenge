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
require_once 'getallimages.php';

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


    // get all images source in string format separated by "~"
    $AllAlbums = getAllImages($albumID, $fb);
    $arr = explode("~",$AllAlbums);
    $masterFolderName = "Facebook_".$_SESSION['Name']."_Albums";
    $masterFolder = getFolderExistsCreate($service, $masterFolderName, $folderDesc, "NULL");
    $albumFolder = getFolderExistsCreate($service, $albumName, $folderDesc, $masterFolder);
    try{
        for($j=0;$j<count($arr)-1;$j++)
        {
            $mimeType = 'image/jpeg';
            $title = $j.".jpg";
            $description = "Facebook Album Image";
            $file_tmp_name = $arr[$j];
            // Call the insert function with parameters listed below
            $driveInfo = insertFile($service, $title, $description, $mimeType, $file_tmp_name, $albumFolder);
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
        // get all images source in string format separated by "~"
        $AllAlbums = getAllImages($album_IDs_Names[0], $fb);
        $arr = explode("~",$AllAlbums);
        $masterFolderName = "Facebook_".$_SESSION['Name']."_Albums";
        $masterFolder = getFolderExistsCreate($service, $masterFolderName, $folderDesc, "NULL");
        $albumFolder = getFolderExistsCreate($service, $album_IDs_Names[1], $folderDesc, $masterFolder);
        try{
            for($j=0;$j<count($arr)-1;$j++)
            {
                $mimeType = 'image/jpeg';
                $title = $j.".jpg";
                $description = "Facebook Album Image";
                $file_tmp_name = $arr[$j];
                // Call the insert function with parameters listed below
                $driveInfo = insertFile($service, $title, $description, $mimeType, $file_tmp_name, $albumFolder);
            }
        }catch(Facebook\Exceptions\FacebookSDKException $e){
            echo "SDK Exception: ".$e->getMessage();
        }
    }
    echo "true";
}
?>