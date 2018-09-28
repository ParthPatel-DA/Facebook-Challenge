<?php
    if (!session_id()) 
    {
        session_start(); 
    }
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Albums - rtCamp Facebook Assignment</title>
    <link rel="shortcut icon" href="https://static.xx.fbcdn.net/rsrc.php/yo/r/iRmz9lCMBD2.ico">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" media="screen" href="main.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" media="screen" href="lib/air-slider.min.css" />
    <script src="lib/air-slider.min.js"></script>
</head>

<body bgcolor="#e9ebee">
    <?php
        // include required files
        require_once('googleDrive-config.php');
        require_once('fb-config.php');
        require_once('upload-album.php');
        $permissions = ['user_photos'];
        // redirect on login page, if session not set with "access_token" key
        if(!isset($_SESSION['access_token'])){
            header("Location: login.php");
        }
        try {
            $accessToken =  $_SESSION['access_token'];
            if (isset($accessToken)) 
            {
                // get user data
                $response = $fb->get('/me?fields=name,id,email,albums', $accessToken);
                $user = $response->getGraphuser();
                // set userName in session
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
                <div id="container">
                    <!-- <div class="slider" id="slideshow">
                        <div class="slide">
                            <img src="" alt="slide1" width="100%" />
                        </div>
                    </div> -->
                </div>
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
            <span id="modelMsg">It may take some time. Don't refresh page. This pop-up automatically close.</span>
            <input id="modelGoogleLogin" type="button" value="Google Login" onclick="document.location.href='googleDrive-login.php'">
        </section></center>
    </section>
    <section id="model-slider">
        <center><section id="model-body">
            <a style="float: right;z-index: 10;margin-top: -30px;font-size: 20px;/* color: #fff; */" onclick="CloseSilder();">Close</a>
            <div class="slider" id="slideshow">
                <div class="slide">
                    <img src="" alt="slide1" width="100%" />
                </div>
            </div>
        </section></center>
    </section>
    <footer>
        &copy; 2018 Parth Patel. ALL RIGHTS RESERVED
    </footer>
    <script src="main.js"></script>
    <script src="album.js"></script>
    <script>
        var slider = new airSlider({
            autoPlay: true,
            width: '100%',
            height: '100%'
        });
    </script>
    <?php
            } else {
                $fb_json = json_decode(file_get_contents("lib/conf/fb-key.json"), true);
                $loginUrl = $helper->getLoginUrl($fb_json["location"].'index.php', $permissions);
                // $loginUrl = $helper->getLoginUrl('https://parthpatel454500.000webhostapp.com/index.php', $permissions);
            }
        } catch(Facebook\Exceptions\FacebookResponseException $e){
            echo "<script>alert('Somthing want wrong! Please try again after sometime.');</script>";
        }
    ?>
</body>
</html>
