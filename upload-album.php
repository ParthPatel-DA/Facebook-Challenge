<?php

require_once 'fb-config.php';
require_once 'lib/google-api-php-client/src/Google/Client.php';
require_once 'lib/google-api-php-client/src/Google/Service/Oauth2.php';
require_once 'lib/google-api-php-client/src/Google/Service/Drive.php';
require_once 'googleDrive-config.php';

$fb = new Facebook\Facebook([
    'app_id' => '534582190322560', // Replace {app-id} with your app id
    'app_secret' => 'aabf7ce7f242d17621318df37f45478b',
    'default_graph_version' => 'v2.2',
    'default_access_token' => isset($_SESSION['facebook_access_token']) ? $_SESSION['facebook_access_token']  : 'aabf7ce7f242d17621318df37f45478b'
    ]);

if (isset($_REQUEST['uploadAlbum']) && isset($_REQUEST['albumName'])) {

    // Init the variables
    $driveInfo = "";
    $folderName = "";
    $folderDesc = "";

    $albumID = $_REQUEST['uploadAlbum'];
    $albumName = $_REQUEST['albumName'];

    // Get the client Google credentials
    $credentials = $_COOKIE["credentials"];
    $str="{\"web\":{\"client_id\":\"131197583719-det06fk5eu1hc5shbugvcdledlubtgf4.apps.googleusercontent.com\",\"project_id\":\"fleet-petal-214209\",\"auth_uri\":\"https://accounts.google.com/o/oauth2/auth\",\"token_uri\":\"https://www.googleapis.com/oauth2/v3/token\",\"auth_provider_x509_cert_url\":\"https://www.googleapis.com/oauth2/v1/certs\",\"client_secret\":\"hiQKubN1n64mtHmH1MfLRfhJ\",\"redirect_uris\":[\"http://localhost/RTCamp/\"],\"https://localhost/RTCamp/googleDrive-login.php\"]}}";
    // $str = "{\"web\":{\"client_id\":\"59128490941-9pi7oolm20ot5h9m62ngj6g0f3e7j0pb.apps.googleusercontent.com\",\"project_id\":\"twittertemp-1533798939629\",\"auth_uri\":\"https://accounts.google.com/o/oauth2/auth\",\"token_uri\":\"https://www.googleapis.com/oauth2/v3/token\",\"auth_provider_x509_cert_url\":\"https://www.googleapis.com/oauth2/v1/certs\",\"client_secret\":\"OIDqKUtb5GpwMjM12ob6fUIV\",\"redirect_uris\":[\"http://localhost/FacebookTest/\",\"https://localhost/rtCamp_Facebook_Assignment/googleLogin.php\"]}}";
    // Get your app info from JSON downloaded from google dev console
    $json = json_decode($str, true);
    $CLIENT_ID = $json['web']['client_id'];
    $CLIENT_SECRET = $json['web']['client_secret'];
    $REDIRECT_URI = $json['web']['redirect_uris'][0];

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


    $allImages = getAllAlbumImages($fb, $albumID);

    $masterFolderName = "Facebook_".$_SESSION['Name']."_Albums";

    $masterFolder = getFolderExistsCreate($service, $masterFolderName, $folderDesc, "NULL");
    $albumFolder = getFolderExistsCreate($service, $albumName, $folderDesc, $masterFolder);
    for ($i=0; $i < count($allImages); $i++) {
        $imageDetails = getImageDetails($fb, $allImages[$i]);
        $file_tmp_name = $imageDetails['images'][4]['source'];

        // Set the file metadata for drive
        $mimeType = 'image/jpeg';
        $title = $i.".jpg";
        $description = "Facebook Album Image";

        // Call the insert function with parameters listed below
        $driveInfo = insertFile($service, $title, $description, $mimeType, $file_tmp_name, $albumFolder);
    }

    echo "Success";
}

if (isset($_REQUEST['uploadAlbums'])) {
    // Init the variables
    $driveInfo = "";
    $folderName = "";
    $folderDesc = "";

    $albums = $_REQUEST['uploadAlbums'];
    // Get the client Google credentials
    $credentials = $_COOKIE["credentials"];
    $str="{\"web\":{\"client_id\":\"131197583719-det06fk5eu1hc5shbugvcdledlubtgf4.apps.googleusercontent.com\",\"project_id\":\"fleet-petal-214209\",\"auth_uri\":\"https://accounts.google.com/o/oauth2/auth\",\"token_uri\":\"https://www.googleapis.com/oauth2/v3/token\",\"auth_provider_x509_cert_url\":\"https://www.googleapis.com/oauth2/v1/certs\",\"client_secret\":\"hiQKubN1n64mtHmH1MfLRfhJ\",\"redirect_uris\":[\"http://localhost/RTCamp/\"],\"https://localhost/RTCamp/googleDrive-login.php\"]}}";
    // $str = "{\"web\":{\"client_id\":\"59128490941-9pi7oolm20ot5h9m62ngj6g0f3e7j0pb.apps.googleusercontent.com\",\"project_id\":\"twittertemp-1533798939629\",\"auth_uri\":\"https://accounts.google.com/o/oauth2/auth\",\"token_uri\":\"https://www.googleapis.com/oauth2/v3/token\",\"auth_provider_x509_cert_url\":\"https://www.googleapis.com/oauth2/v1/certs\",\"client_secret\":\"OIDqKUtb5GpwMjM12ob6fUIV\",\"redirect_uris\":[\"http://localhost/FacebookTest/\",\"https://localhost/rtCamp_Facebook_Assignment/googleLogin.php\"]}}";
    // Get your app info from JSON downloaded from google dev console
    $json = json_decode($str, true);
    $CLIENT_ID = $json['web']['client_id'];
    $CLIENT_SECRET = $json['web']['client_secret'];
    $REDIRECT_URI = $json['web']['redirect_uris'][0];

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

    $arrAlbums = explode('_', $albums);

    foreach ($arrAlbums as $album) {
        $album_IDs_Names = explode('-', $album);

        $allImages = getAllAlbumImages($fb, $album_IDs_Names[0]);

        $masterFolderName = "Facebook_".$_SESSION['Name']."_Albums";

        $masterFolder = getFolderExistsCreate($service, $masterFolderName, $folderDesc, "NULL");
        $albumFolder = getFolderExistsCreate($service, $album_IDs_Names[1], $folderDesc, $masterFolder);
        for ($i=0; $i < count($allImages); $i++) {
            $imageDetails = getImageDetails($fb, $allImages[$i]);
            $file_tmp_name = $imageDetails['images'][4]['source'];

            // Set the file metadata for drive
            $mimeType = 'image/jpeg';
            $title = $i.".jpg";
            $description = "Facebook Album Image";

            // Call the insert function with parameters listed below
            $driveInfo = insertFile($service, $title, $description, $mimeType, $file_tmp_name, $albumFolder);
        }
    }
    echo "Success";
}

function getAllAlbumImages($fb, $albumID)
{
    try {
        $response = $fb->get(
            '/'.$albumID.'/photos?limit=500',
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