<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Page Title</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" media="screen" href="main.css" />
    <script src="main.js"></script>
    <!-- <link rel="stylesheet" href="air-slider.min.css"> -->
    <!-- <script src="air-slider.min.js"></script> -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        
</head>

<body bgcolor="#e9ebee">
    <?php
        // require_once('fb-callback2.php');
        require_once('fb-config.php');
        require_once('upload-album.php');
        // session_start();
        // require_once('googleDrive-login.php');
        $permissions = ['user_photos'];
        $accessToken =  $_SESSION['access_token'];  
        $logoutURL = $helper->getLogoutUrl($accessToken, $redirectURL.'logout.php');
        if (isset($accessToken)) 
        {
            $fb = new Facebook\Facebook([
            'app_id' => '534582190322560', // Replace {app-id} with your app id
            'app_secret' => 'aabf7ce7f242d17621318df37f45478b',
            'default_graph_version' => 'v2.2',
            'default_access_token' => isset($_SESSION['facebook_access_token']) ? $_SESSION['facebook_access_token']  : 'aabf7ce7f242d17621318df37f45478b'
            ]);
        
            $response = $fb->get('/me?fields=name,id,email,albums', $accessToken);
            $user = $response->getGraphuser();
            $_SESSION['Name'] = $user['name'];
            

        // $accessToken = $_SESSION['access_token'];
        // if(!isset($_SESSION['access_token'])){
        //     header("Location: login.php");
        // }
        // $response = $fb->get('/me?fields=name,id,email,albums', $accessToken);
        // $user = $response->getGraphuser();
    ?>
    <section class="profile-header-box">
        <label>rtCamp Facebook Assignment</label>
        <input type="submit" value="Logout">
    </section>
    <section class="body">
        <section class="left-side-bar">
            <section>
                <?php 
                    // $re = $fb->get($user['id'].'/picture',$accessToken);
                    // $pic = $re->getGraphuser();
                    // echo $pic['url'];
                ?>
                <img class="profile-pic" src="http://graph.facebook.com/<?php echo $user['id']; ?>/picture?type=large" alt="Pic" width="200px" height="200px">
            </section>
            <section>
                <label><?php echo $user['name']; ?></label>
                <br>
                <span><?php echo "#".$user['id']; ?></span>
                <br>

            </section>
            <section>
                <!-- <p>Tweets
                    <br>
                    <span>1</span>
                </p>
                <p>Following
                    <br>
                    <span>10</span>
                </p>
                <p>Followers
                    <br>
                    <span>100</span>
                </p> -->
                
            </section>
            
            <section id="ShareBox">
                <input type="submit" value="Download Selected Album" name="btnClone" id="btnClone" onclick="DownloadSelected()"><br>
                <input type="submit" value="Move To Google Drive" name="btnDrive" id="btnDrive" onclick="fun()">
            </section>
        </section>
        <section class="main">
            <section id="TimeLine">
                <!-- <div class="slider">
                    <div class="slide">
                        <img src="img/image1.jpg" alt="slide1" width="100%" />
                    </div>
                    <div class="slide">
                        <img src="img/image2.jpeg" alt="slide2" />
                    </div>
                    <div class="slide">
                        <img src="img/image3.jpeg" alt="slide3" />
                    </div>
                </div> -->
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
                        <!-- <input type="text" name="txtSearch" placeholder="Search"> -->
                        <input type="text" id="data">
                        <input type="button" value="Download" onclick="DownloadAll()">
                        <div style="100%"><a href="googleDrive-login.php" style="text-decoration: none; font-weight: bolder; text-content: center;">Google Login</a></div>
                    </div>
                </section>
                <section class="row" id="row">
                        <?php
                            for($i=0;$i<count($user['albums']);$i++)
                            {
                            //   echo "". $user['albums'][$i]['name']."<br><br>";
                                
                              // for($j=0;$j<)
                              $response = $fb->get(
                                '/'.$user['albums'][$i]['id'].'/photos?limit=100',
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
                                <div id='divAlbum'><span class='checkbox'>*<input type='checkbox' name='' id='' onchange='checkAlbum(this);' value="<?php echo $user['albums'][$i]['id']; ?>"></span><a class='imgCount' onclick="DownloadSingle(<?php echo $user['albums'][$i]['id']; ?>)"><i class="fa fa-file-zip-o"></i></a></div>
                                <a onclick="UploadToDriveSingle(<?php echo $user['albums'][$i]['id']; ?>,'<?php echo $user['albums'][$i]['name']; ?>')">Upload</a>
                            </div>
                        <?php
                                    }
                                }

                                    
                            }
                            
                            
                            
                        ?>
                        <!-- <div>
                            <img src="img/image1.jpg" alt="" id="imgAlbum">
                            <div id="divAlbum"><span class="checkbox"><input type="checkbox" name="" id="" style="" onchange="checkAlbum(this);"></span><span class="imgCount">5</span></div> 
                        </div>
                        <div>
                            <img src="img/photo1.png" alt="" id="imgAlbum">
                            <div id="divAlbum"><span class="checkbox"><input type="checkbox" name="" id="" style="" onchange="checkAlbum(this);"></span><span class="imgCount">5</span></div> 
                        </div>
                        <div>
                            <img src="img/image3.jpeg" alt="" id="imgAlbum">
                            <div id="divAlbum"><span class="checkbox"><input type="checkbox" name="" id="" style="" onchange="checkAlbum(this);"></span><span class="imgCount">5</span></div> 
                        </div>
                        <div>
                            <img src="img/image2.jpeg" alt="" id="imgAlbum">
                            <div id="divAlbum"><span class="checkbox"><input type="checkbox" name="" id="" style="" onchange="checkAlbum(this);"></span><span class="imgCount">5</span></div> 
                        </div>
                        <div>
                            <img src="img/photo2.png" alt="" id="imgAlbum">
                            <div id="divAlbum"><span class="checkbox"><input type="checkbox" name="" id="" style="" onchange="checkAlbum(this);"></span><span class="imgCount">5</span></div> 
                        </div>
                        <div>
                            <img src="img/image2.jpeg" alt="" id="imgAlbum">
                            <div id="divAlbum"><span class="checkbox"><input type="checkbox" name="" id="" style="" onchange="checkAlbum(this);"></span><span class="imgCount">5</span></div> 
                        </div>
                        <div>
                            <img src="img/photo4.jpg" alt="" id="imgAlbum">
                            <div id="divAlbum"><span class="checkbox"><input type="checkbox" name="" id="" style="" onchange="checkAlbum(this);"></span><span class="imgCount">5</span></div> 
                        </div>
                        <div>
                            <img src="img/pic3.jpg" alt="" id="imgAlbum">
                            <div id="divAlbum"><span class="checkbox"><input type="checkbox" name="" id="" style="" onchange="checkAlbum(this);"></span><span class="imgCount">5</span></div> 
                        </div>
                        <div>
                            <img src="img/image2.jpeg" alt="" id="imgAlbum">
                            <div id="divAlbum"><span class="checkbox"><input type="checkbox" name="" id="" style="" onchange="checkAlbum(this);"></span><span class="imgCount">5</span></div> 
                        </div>
                        <div>
                            <img src="img/photo3.jpg" alt="" id="imgAlbum">
                            <div id="divAlbum"><span class="checkbox"><input type="checkbox" name="" id="" style="" onchange="checkAlbum(this);"></span><span class="imgCount">5</span></div> 
                        </div> -->
                </section>
            </section>
        </section>
        
    </section>
    <footer>
        © 2011 rtCamp ALL RIGHTS RESERVED
    </footer>
    
</body>
<script type="text/javascript">
    var varTimeLine = document.getElementById("TimeLine");
    var varFollower = document.getElementById("Follower");
    // var TimeLineLink = document.getElementById("TimeLineLink");
    // var FollowerLink = document.getElementById("FollowerLink");
    var cnt=0;
    var divShareBox = document.getElementById("ShareBox");

    varFollower.style.display = "block";
    // FollowerLink.style.borderBottomWidth = "3px";
    // btnClone.style.visibility = "hidden";
    // btnDrive.style.visibility = "hidden";
    
    // function fun(element){
    //     // alert("okay");
    //     var x = element;
    //     x.style.display = "none";
    //     // x.
    // }

    function checkAlbum(element) {
        // alert(element.style);
        // element.closest(".checkbox").style.backgroundColor = "#008cff";
        
        var a = element;
        if(a.checked) {
            var x = element.closest(".checkbox").closest("#divAlbum");
            x.style.background = "#4267b2";
            
            for (var i = 0; i < x.childNodes.length; i++) {
                if (x.childNodes[i].className == "imgCount") {
                    
                    notes = x.childNodes[i];
                    notes.style.backgroundColor = "#fff";
                    notes.style.color = "#4267b2";
                    break;
                }        
            }
            element.closest(".checkbox").style.backgroundColor = "#fff";
            element.closest(".checkbox").style.color = "#4267b2";
            cnt+=1;
        }
        else {
            var x = element.closest(".checkbox").closest("#divAlbum");
            x.style.background = "#fff"
            for (var i = 0; i < x.childNodes.length; i++) {
                if (x.childNodes[i].className == "imgCount") {
                    
                    notes = x.childNodes[i];
                    notes.style.backgroundColor = "#657786";
                    notes.style.color = "#fff";
                    break;
                }        
            }
            element.closest(".checkbox").style.backgroundColor = "#657786";
            element.closest(".checkbox").style.color = "#657786";
            cnt-=1;
        }

        if(cnt > 0){
            divShareBox.style.display = "block";
        }
        else {
            divShareBox.style.display = "none";
        }
    }
</script>

<script>
    function cancelFullScreen() {
        if (document.cancelFullScreen) {
            document.cancelFullScreen();
        } else if (document.mozCancelFullScreen) {
            document.mozCancelFullScreen();
        } else if (document.webkitCancelFullScreen) {
            document.webkitCancelFullScreen();
        } else if (document.msCancelFullScreen) {
            document.msCancelFullScreen();
        }
        // link = document.getElementById("imgAlbum");
        // link.removeAttribute("onclick");
        // link.setAttribute("onclick", "fullScreen(this)");
        var varTimeLine = document.getElementById("TimeLine");
        varTimeLine.style.display = "none";
    }
    
    

    function fullScreen(element) {
        // var element = element;
        // alert("okay");
        // var x = element.closest('#albumID').value;
        // alert(element.src);
        var x = element.parentElement;
        var node = "";
        for (var i = 0; i < x.childNodes.length; i++) {
            if (x.childNodes[i].className == "albumID") {
                node = x.childNodes[i];
                // alert(node.value);
            }
        }

        var elementcontainer = document.getElementById("container");
        if (elementcontainer.requestFullScreen) {
            elementcontainer.requestFullScreen();
        } else if (elementcontainer.webkitRequestFullScreen) {
            elementcontainer.webkitRequestFullScreen();
        } else if (elementcontainer.mozRequestFullScreen) {
            elementcontainer.mozRequestFullScreen();
        }
    
        elementcontainer.setAttribute("onclick", "cancelFullScreen()");
        var varTimeLine = document.getElementById("TimeLine");
        var y = document.getElementById("slideshow");
        y.innerHTML = "";
        // var txt = document.getElementById("data");
        // txt.value = node.value.trim();
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                var txt = document.getElementById("data");
                y.innerHTML = this.responseText;
                // txt.value = this.responseText;
            }
        }
        xmlhttp.open("GET", "get-images.php?slidealbumid="+node.value.trim(), true);
        xmlhttp.send();
        // $.post('get-images.php'{albumid:node},function(data){
        //     var y = document.getElementById("slideshow");
        //     y.innerHTML = data;
        // });
        // // $.post
        varTimeLine.style.display = "block";
        // alert(x.value);
        //alert(x);
    }

    function DownloadSingle(element){
        // var x = element.parentElement;
        // var node = element;
        // for (var i = 0; i < x.childNodes.length; i++) {
        //     if (x.childNodes[i].className == "albumID") {
        //         node = x.childNodes[i];
        //         // alert(node.value);
        //     }
        // }
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                var txt = document.getElementById("data");
                txt.value = this.responseText;
            }
        }
        xmlhttp.open("GET", "get-images.php?downloadsingle="+element, true);
        xmlhttp.send();
    }

    function DownloadAll(){
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                // var txt = document.getElementById("data");
                // txt.value = this.responseText;
                // window.location.".".this.responseText;
            }
        }
        xmlhttp.open("GET", "get-images.php?downloadall=all", true);
        xmlhttp.send();
    }

    function DownloadSelected(){
        var albumID="";
        // var x = document.getElementById('row');
        var check = document.getElementById('row').querySelectorAll('input[type=checkbox]:checked');
        for(var i = 0; i < check.length; i++){
            albumID+=check[i].value+"/";
        }
        // alert(albumID);
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                // var txt = document.getElementById("data");
                // txt.value = this.responseText;
                // window.location.".".this.responseText;
            }
        }
        xmlhttp.open("GET", "get-images.php?downloadselected="+albumID, true);
        xmlhttp.send();
    }
    
    function UploadToDriveSingle(albumid, name) {
        // alert("okay");
        if(getCookie('credentials') == "") {
            // $('#googleLoginModal').modal('toggle');
        } else {
            // $('#loaderModal').modal('toggle');
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    // var txt = document.getElementById("data");
                    // txt.value = this.responseText;
                    // window.location.".".this.responseText;
                }
                else{
                    alert("There was a problem while using XMLHTTP:\n" + xmlhttp.statusText);
                }
            }   
            xmlhttp.open("GET", "upload-album.php?uploadAlbum=" + albumid + "&albumName=" + name, true);
            xmlhttp.send(null);
        }
    }


    function getCookie(cname) {
            var name = cname + "=";
            var decodedCookie = decodeURIComponent(document.cookie);
            var ca = decodedCookie.split(';');
            for(var i = 0; i <ca.length; i++) {
                var c = ca[i];
                while (c.charAt(0) == ' ') {
                    c = c.substring(1);
                }
                if (c.indexOf(name) == 0) {
                    return c.substring(name.length, c.length);
                }
            }
            return "";
        }

    window.onload = function() {
      imgs = document.getElementById('slideshow').children;
      interval = 8000;
      currentPic = 0;
      imgs[currentPic].style.webkitAnimation = 'fadey '+interval+'ms';
      imgs[currentPic].style.animation = 'fadey '+interval+'ms';
      var infiniteLoop = setInterval(function(){
        imgs[currentPic].removeAttribute('style');
        if ( currentPic == imgs.length - 1) { currentPic = 0; } else { currentPic++; }
        imgs[currentPic].style.webkitAnimation = 'fadey '+interval+'ms';
        imgs[currentPic].style.animation = 'fadey '+interval+'ms';
      }, interval);
    }
    
</script>

<!-- <script>
    var slider = new airSlider({
        autoPlay: true,
        width: '100%',
        height: '600px'
    });
</script> -->
<?php
    } else {
        $loginUrl = $helper->getLoginUrl('https://localhost/RTCamp/index.php', $permissions);
    }
?>
</html>