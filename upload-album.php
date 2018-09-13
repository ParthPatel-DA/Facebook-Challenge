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
    $REDIRECT_URI = $json['web']['redirect_uris'][2];

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


    // $allImages = getAllAlbumImages($fb, $albumID);
    $response = $fb->get(
        '/'.$albumID.'/photos?limit=100',
        $_SESSION['access_token']
    );
    $allImages = $response->getGraphEdge();

    $masterFolderName = "Facebook_".$_SESSION['Name']."_Albums";
    ini_set('max_execution_time', 0);
    $masterFolder = getFolderExistsCreate($service, $masterFolderName, $folderDesc, "NULL");
    $albumFolder = getFolderExistsCreate($service, $albumName, $folderDesc, $masterFolder);
    for ($i=0; $i < count($allImages); $i++) {
        
        $response = $fb->get(
            '/'.$allImages[$i]['id'].'?fields=name,id,created_time,images',
            $_SESSION['access_token']
        );
        $imageDetails = $response->getGraphNode();
        // $imageDetails = getImageDetails($fb, $allImages[$i]);
        $file_tmp_name = $imageDetails['images'][0]['source'];
        
        // Set the file metadata for drive
        $mimeType = 'image/jpeg';
        $title = $i.".jpg";
        $description = "Facebook Album Image";

        // Call the insert function with parameters listed below
        $driveInfo = insertFile($service, $title, $description, $mimeType, $file_tmp_name, $albumFolder);
        sleep(0.1);
    }
    ini_set('max_execution_time', 0);
    $nextImg = $fb->next($allImages);
    $data_json=json_decode($nextImg, true);
    if($data_json != ""){
        foreach($data_json as $img){
            
            $response = $fb->get(
                '/'.$img['id'].'?fields=name,id,created_time,images',
                $_SESSION['access_token']
            );
            $imageDetails = $response->getGraphNode();
            // $imageDetails = getImageDetails($fb, $allImages[$i]);
            $file_tmp_name = $imageDetails['images'][0]['source'];
            
            // Set the file metadata for drive
            $mimeType = 'image/jpeg';
            $title = $i.".jpg";
            $description = "Facebook Album Image";
            $i++;
            // Call the insert function with parameters listed below
            $driveInfo = insertFile($service, $title, $description, $mimeType, $file_tmp_name, $albumFolder);
            sleep(0.1);
        }
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
    $REDIRECT_URI = $json['web']['redirect_uris'][2];

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

        // $allImages = getAllAlbumImages($fb, $album_IDs_Names[0]);
        $response = $fb->get(
            '/'.$albumID.'/photos?limit=100',
            $_SESSION['access_token']
        );
        $allImages = $response->getGraphEdge();

        $masterFolderName = "Facebook_".$_SESSION['Name']."_Albums";
        ini_set('max_execution_time', 0);
        $masterFolder = getFolderExistsCreate($service, $masterFolderName, $folderDesc, "NULL");
        $albumFolder = getFolderExistsCreate($service, $album_IDs_Names[1], $folderDesc, $masterFolder);
        for ($i=0; $i < count($allImages); $i++) {
            $response = $fb->get(
                '/'.$allImages[$i]['id'].'?fields=name,id,created_time,images',
                $_SESSION['access_token']
            );
            $imageDetails = $response->getGraphNode();
            // $imageDetails = getImageDetails($fb, $allImages[$i]);
            $file_tmp_name = $imageDetails['images'][0]['source'];

            // Set the file metadata for drive
            $mimeType = 'image/jpeg';
            $title = $i.".jpg";
            $description = "Facebook Album Image";

            // Call the insert function with parameters listed below
            $driveInfo = insertFile($service, $title, $description, $mimeType, $file_tmp_name, $albumFolder);
            sleep(0.1);
        }
        ini_set('max_execution_time', 0);
        $nextImg = $fb->next($allImages);
        $data_json=json_decode($nextImg, true);
        if($data_json != ""){
            foreach($data_json as $img){
                $response = $fb->get(
                    '/'.$img['id'].'?fields=name,id,created_time,images',
                    $_SESSION['access_token']
                );
                $imageDetails = $response->getGraphNode();
                // $imageDetails = getImageDetails($fb, $allImages[$i]);
                $file_tmp_name = $imageDetails['images'][0]['source'];
                
                // Set the file metadata for drive
                $mimeType = 'image/jpeg';
                $title = $i.".jpg";
                $description = "Facebook Album Image";
                $i++;
                // Call the insert function with parameters listed below
                $driveInfo = insertFile($service, $title, $description, $mimeType, $file_tmp_name, $albumFolder);
                sleep(0.1);
            }
        }
    }
    echo "true";
}

function getAllAlbumImages($fb, $albumID)
{
    try {
        $response = $fb->get(
            '/'.$albumID.'/photos?limit=100',
            $_SESSION['access_token']
        );
    } catch (Facebook\Exceptions\FacebookResponseException $e) {
        echo 'Graph returned an error: ' . $e->getMessage();
        exit;
    } catch (Facebook\Exceptions\FacebookSDKException $e) {
        echo 'Facebook SDK returned an error: ' . $e->getMessage();
        exit;
    }
    $allImages = $response->getGraphEdge();

    return $allImages;
}
?>