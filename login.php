<?php
    if (!session_id()) 
    {
        session_start(); 
    }
    require_once('fb-config.php');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Login - rtCamp Facebook Assignment</title>
    <link rel="shortcut icon" href="https://static.xx.fbcdn.net/rsrc.php/yo/r/iRmz9lCMBD2.ico">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" media="screen" href="main.css" />
    <script src="main.js"></script>
</head>
<body>
<section class="profile-header-box">
        <label>rtCamp Facebook Assignment</label>
    </section>
    <section class="login-body" style="margin-top: 75px; padding: 180px 20px;">
        <center>
            <section style="width: 400px; border: 1px solid #657786; box-shadow: 0 0px 10px 0 rgba(0, 0, 0, 0.25); padding: 0px 0px 80px 0px;">
                <div style="font-size: 30px; background: #4267b2; color: #fff; padding: 10px 20px;"><label>Login</label></div>
                <div style="margin-top: 90px;">
                    <?php
                        $permissions = ['email,user_photos']; // Optional permissions
                        $fb_json = json_decode(file_get_contents("lib/conf/fb-key.json"), true);
                        $loginUrl = $helper->getLoginUrl($fb_json["location"].'fb-callback.php', $permissions);
                        // $loginUrl = $helper->getLoginUrl('https://parthpatel454500.000webhostapp.com/fb-callback.php', $permissions);
                    
                        echo '<a href="' . htmlspecialchars($loginUrl) . '" style="background: #4267b2; color: #fff; text-decoration: none; font-size: 20px; font-weight: bolder; padding: 10px 30px; margin-top: 50px;">Login With Facebook</a>';
                    ?>
                    
                </div>
            </section>
        </center>
    </section>
    <footer>
        &copy; 2018 Parth Patel. ALL RIGHTS RESERVED
    </footer>
</body>
</html>


    
