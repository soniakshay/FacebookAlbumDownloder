<?php
require_once 'vendor/autoload.php';

session_start();
if(isset($_SESSION['fb_access_token'] ))
{
    header('Location:page/home.php');
}
if(isset($_GET['state'])) {
    $_SESSION['FBRLH_state'] = $_GET['state'];
}

require_once 'config/config.php';

$helper = $fb->getRedirectLoginHelper();
//$permissions = ['user_photos']; 
$loginUrl = $helper->getLoginUrl('https://localhost/FacebookAlbumDownloder/page/fb-callback.php');




?>
<html>
    <head>
    <title>Facebook Album Downloder</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">	<!-- boostrap css-->
        <link rel="stylesheet" href="css/style.css">	<!-- mycss -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script> <!--Boostrap js-->
    </head>
    <body>
		<div id="container">
			<!-- Header-->
			<div id="header">
                        <div class="container">
                            <center><h1 style="color:white;"><strong>Facebook Album Downloder</strong></h1></center>
            
                            <div id="loginbox" class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div id="buttondiv">
                                <?php
                                if(empty($access_token)) {
                                            echo '<a href="' . htmlspecialchars($loginUrl) . '"><button class="btn btn-primary"">Login with Facebook</button> </a>';
                                        }
                                    ?>
                                </div>
                                
                            </div>
                    </div>
			</div>
        </div>
        
    </body>
</html>



























