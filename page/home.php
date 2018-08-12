<?php

require_once '../vendor/autoload.php'; // change path as needed
session_start();
if(isset($_GET['state'])) {
    
    $_SESSION['FBRLH_state'] = $_GET['state'];
}

require_once '../config/config.php';    

    


$access_token=$_SESSION['fb_access_token'];

if(isset($access_token)) {
    try {
        $response = $fb->get('/me',$access_token);
        
		$fb_user = $response->getGraphUser();
		
		$id=$fb_user['id'];
		$name=$fb_user['name'];
		$str="http://graph.facebook.com/".$id."/picture?type=square";
		

        //  var_dump($fb_user);
    } catch (\Facebook\Exceptions\FacebookResponseException $e) {
        echo  'Graph returned an error: ' . $e->getMessage();
    } catch (\Facebook\Exceptions\FacebookSDKException $e) {
        // When validation fails or other local issues
        echo 'Facebook SDK returned an error: ' . $e->getMessage();
    }
}



$url="https://graph.facebook.com/v3.1/me?fields=albums&access_token=".$access_token;
$result = file_get_contents($url);
$album=json_decode($result);

$url1="https://graph.facebook.com/v3.1/me?fields=albums%7Bpicture%7D&access_token=".$access_token;
$result = file_get_contents($url1);
$pic=json_decode($result);



?>
<html>
    <head>
        
    <title>Facebook Album Downloder</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    </head>
    <body>
    <nav class="navbar navbar-default">
        <div class="container-fluid">
            <div class="navbar-header">
                <a class="navbar-brand" href="#"><b class="text-info">Facebook Album Downloder</b></a>
        </div>
        <ul class="nav navbar-nav navbar-right">
            <li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#">
            <?php echo "<strong class='text-info' style='padding-right:10px;'>".$name."</strong>"."  "."<img src='$str' height='35px' class='img-circle'>";?> <span class="caret"></span></a>
            <ul class="dropdown-menu">
                <li><a href="#">Logout</a></li>
            </ul>
            </li>
        </ul>
            
        </div>  
    </nav>
       <div class="container">
            <div class="row">
                <?php
                
                    foreach($album->albums->data as $mydata)
                    {
                        
                        foreach($pic->albums->data as $mydata1)
                        {
                            if($mydata1->id==$mydata->id)
                            {
                ?>
                                <div class="col-lg-3 col-md-3  col-sm-12 col-xm-12" style="height:400px;padding:2px;">
                                    <div id="box" style="border:1px solid;">
                                        <div id="image" style="height:80%;width:100%;background:url('<?php echo $mydata1->picture->data->url;?>');background-position:center;background-size:cover;    ">
                                        </div>
                                        <h4 style="margin-left:10px;"><?php echo $mydata->name; ?></h4>
                                        <div id="buttongrp">
                                            <center>
                                             <button type="button" class="btn btn-default"><span class="glyphicon glyphicon-eye-open "></span><span class="btntext" style="margin-left:5px;">View</span></button>
                                            <button type="button" class="btn btn-default"><span class="glyphicon glyphicon-save"></span><span class="btntext" style="margin-left:5px;">downloud</span></button>  
                                            <button type="button" class="btn btn-default"><span class="glyphicon glyphicon-share"></span><span class="btntext" style="margin-left:5px;">Share</span></button>

                                        </div>    
                                    </div>
                                </div>
                            
                <?php
                                break;
                            }
                        }
                        
                    }
                ?>
            
           
           <!--https://graph.facebook.com/me/albums?limit=25&before=
-->           
        </div>
        </div>
    </body>
</html>