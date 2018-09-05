<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Page Title</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" media="screen" href="main.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    
</head>

<body bgcolor="#e9ebee">
    <?php
        require_once('fb-config.php');
        require_once('upload-album.php');
        $permissions = ['user_photos'];
        if(!isset($_SESSION['access_token'])){
            header("Location: login.php");
        }
        $accessToken =  $_SESSION['access_token'];  
        $logoutURL = $helper->getLogoutUrl($accessToken, $redirectURL.'logout.php');
        if (isset($accessToken)) 
        {
            $fb_json = json_decode(file_get_contents("lib/conf/fb-key.json"), true);
            $fb = new Facebook\Facebook([
            'app_id' => $fb_json["app-id"], 
            'app_secret' => $fb_json["app-secret"],
            'default_graph_version' => 'v2.2',
            'default_access_token' => isset($_SESSION['facebook_access_token']) ? $_SESSION['facebook_access_token']  : $fb_json["app-secret"]
            ]);
        
            $response = $fb->get('/me?fields=name,id,email,albums', $accessToken);
            $user = $response->getGraphuser();
            $_SESSION['Name'] = $user['name'];
    ?>
    <section class="profile-header-box">
        <label>rtCamp Facebook Assignment</label>
        <input type="submit" value="Logout" onclick="document.location.href='logout.php'">
    </section>
    <section class="body">
        <section class="left-side-bar">
            <section>
                <img class="profile-pic" src="http://graph.facebook.com/<?php echo $user['id']; ?>/picture?type=large" alt="Pic" width="200px" height="200px">
            </section>
            <section>
                <label><?php echo $user['name']; ?></label>
                <br>

            </section>
            <section></section>
            <section id="ShareBox">
                <input type="submit" value="Download Selected Album" name="btnClone" id="btnClone" onclick="DownloadSelected()"><br>
                <input type="submit" value="Move To Google Drive" name="btnDrive" id="btnDrive" onclick="UploadSelected()">
            </section>
        </section>
        <section class="main">
            <section id="TimeLine">
                <figure id="container">
                    <figure id="slideshow">
                        <img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/4273/dada-voltaire-tinkerbot.jpg" alt>
                        <img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/4273/happy-bot-tinkerbot.jpg" alt>
                        <img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/4273/sgt-swift-tinkerbot.jpg" alt>
                        <img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/4273/xf-3-fandicaff-tinkerbot.jpg" alt>
                    </figure>
                </figure>
            </section>
            <section id="Follower">
                <section>
                    <div>
                        <h1>Album List</h1>
                    </div>
                    <div>
                        <input type="button" value="Download" onclick="DownloadAll()">
                        <input type="button" value="Upload on Google Drive" onclick="UploadAll()">
                    </div>
                </section>
                <section class="row" id="row">
                        <?php
                            for($i=0;$i<count($user['albums']);$i++)
                            {
                              $response = $fb->get(
                                '/'.$user['albums'][$i]['id'].'/photos?limit=200',
                                $accessToken
                              );
                              $a = $response->getGraphEdge();
                              
                                for($j=0;$j<count($a);$j++){
                                    if($j==0){
                                        $response = $fb->get(
                                            '/'.$a[0]['id'].'?fields=link,name,id,created_time,images,picture',
                                            $accessToken
                                        );
                                        $b=$response->getGraphNode()['images'][$j];
                        ?>
                            <div>
                                <img src="<?php echo $b['source']; ?> " alt='' id='imgAlbum' onclick="fullScreen(this)">
                                <input type='hidden' class="albumID" id='albumID' value="<?php echo $user['albums'][$i]['id']; ?>">
                                <div id='divAlbum'>
                                    <div id="AlbumName"><?php echo $user['albums'][$i]['name']; ?></div>
                                    <span class='checkbox'>*<input type='checkbox' name='' id='' onchange='checkAlbum(this);' value="<?php echo $user['albums'][$i]['id']."-".$user['albums'][$i]['name']; ?>"></span>
                                    <a class='imgCount' onclick="DownloadSingle(<?php echo $user['albums'][$i]['id']; ?>)"><i class="fa fa-save"></i></a>
                                    <a class="imgGD" onclick="UploadToDriveSingle(<?php echo $user['albums'][$i]['id']; ?>,'<?php echo $user['albums'][$i]['name']; ?>')"><i class="fa fa-cloud-upload"></i></a>
                                </div>
                                
                            </div>
                        <?php
                                    }
                                }       
                            }  
                        ?>
                </section>
            </section>
        </section>
        
    </section>
    <section id="model-background">
    </section>
    <section id="model">
        <center><section id="model-body">
            <i id="modelspinner" class="fa fa-spinner fa-pulse"></i><br><br>
            <label id="modelDes">Please wait while downloading Albums</label><br><br>
            <span id="modelMsg">It may take same time. Don't refresh page. This pop-up automatically close.</span>
            <input id="modelGoogleLogin" type="button" value="Google Login" onclick="document.location.href='googleDrive-login.php'">
        </section></center>
    </section>
    <footer>
        &copy; 2018 rtCamp ALL RIGHTS RESERVED
    </footer>
    <script src="main.js"></script>
    <script src="album.js"></script>
</body>
<?php
    } else {
        $loginUrl = $helper->getLoginUrl('https://localhost/Facebook-Challenge/index.php', $permissions);
    }
?>
</html>
