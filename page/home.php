<?php
    session_start();//session start
    
	//if check login or not if not login then redirect login page
	if(!isset($_SESSION['fb_access_token'] ))
    {
        header('Location:../index.php');
    }
    
    require_once '../vendor/facebook/autoload.php'; 
	
	if(isset($_GET['state'])) {

        $_SESSION['FBRLH_state'] = $_GET['state'];
    }

    require_once '../config/config.php';    
    
	//access token fetch in session variable 
    $access_token=$_SESSION['fb_access_token'];
    
	//get user id,name,profile picture
    if(isset($access_token)) {
        try 
        {
            $response = $fb->get('/me',$access_token);

            $fb_user = $response->getGraphUser();

            $id=$fb_user['id'];
            $name=$fb_user['name'];
            $str="http://graph.facebook.com/".$id."/picture?type=square";
        } catch (\Facebook\Exceptions\FacebookResponseException $e) {
            echo  'Graph returned an error: ' . $e->getMessage();
        } catch (\Facebook\Exceptions\FacebookSDKException $e) {
            // When validation fails or other local issues
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
        }
    }
    
	//get json data of all album name and id
    $url="https://graph.facebook.com/v3.1/me?fields=albums&access_token=".$access_token;
    $result = file_get_contents($url);
    $album=json_decode($result);
    
	//store all album id  in variable for downloude all album
    $allalbumid=null;
    foreach($album->albums->data as $mydata)
    {
        $allalbumid=$allalbumid.$mydata->id.",";
                        
    }
    $allalbumid=rtrim($allalbumid,",");

    //fetch all album detail id,picture
    $url1="https://graph.facebook.com/v3.1/me?fields=albums%7Bpicture%7D&access_token=".$access_token;
    $result = file_get_contents($url1);
    $pic=json_decode($result);

?>

<html>
    <head>
		<title>Facebook Album Downloader</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
		<link rel="stylesheet" href="../assets/css/style.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
		<script src="../assets/js/homepage.js"></script>
		<script src="../assets/js/slideshow.js"></script>
	</head>
    <body>
		<!--Image popup div-->
		<div id="imagepopup">
			<div id="closebtndiv">
				<button id="closebtn" onclick="closepopup()">X</button>
			</div>
			<div id="lodingicon">
			</div>
			<div id="slideshowimage">
			<center>
			<div class="btn-group" style="border:1px solid;">
					<button type="button" class="navbtn btn" onclick="previous()">Previous</button>
					<button type="button" class="navbtn btn" onclick="autoslideshow()">Auto</button>
					<button type="button" class="navbtn btn" onclick="next()">Next</button>	
			</div>
				<center><img src="" class="img" height="auto" width="350px"  id="imageslideshow"  class="img-responsive">
				
			</div>
		</div>
		<!--navigation-->
		<nav class="navbar navbar-default" >
			<div class="container-fluid">
				<div class="navbar-header">
					<a class="navbar-brand" href="#"><b class="text-info">Facebook Album Downloader</b></a>
				</div>
				<ul class="nav navbar-nav navbar-right">
					<li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#">
						<?php echo "<strong class='text-info' style='padding-right:10px;'>".$name."</strong>"."  "."<img src='$str' height='35px' class='img-circle'>";?> <span class="caret"></span></a>
						<ul class="dropdown-menu">
							<li><a href="logout.php">Logout</a></li>
						</ul>
					</li>
				</ul>
			</div>  
		</nav>
		
        <!--alert box for uplode status success or not-->
        <?php
        if(isset($_GET['uplodestatus'])=="success")
        {
        ?>
            <div class="container" id="uplodestatus">
                <div class="alert alert-success" role="alert">
                   Upload Album   <strong>SuccessFully!</strong>
                </div>

            </div>
        <?php
        }
        ?>
       
	   
	   <!--loading div and downlode link button-->
		<div class="container" id="lodingdiv">
			<div class="alert alert-dismissible alert-light">
              <button type="button" class="close" data-dismiss="alert">&times;</button>
               <center> <strong style="font-size:30px;color:#31708f;" id="sts">Loading..</strong>
                   <a href="#" id="demo"><button type="button" class="btn btn-primary">Downloud</button></a></center>
    
			</div>
        </div>
         
		 <!--button for selected album and all album-->
        <div class="container">
            <center>
				<div class="btn-group" role="group" aria-label="Basic example">
					<button type="button" class="btn btn-secondary" onclick="createzipwithseletedalbum()"><span class="glyphicon glyphicon-save"></span><span> Download Selected Album</span></button>
					<button type="button" class="btn btn-secondary"  onclick="createzipwithallalbum()"><span class="glyphicon glyphicon-save"></span><span> Download All Album</span></button>
					<button type="button" class="btn btn-secondary"  onclick="sharewithselectedalbum()"><span class="glyphicon glyphicon-share"></span><span> Move Selected Album</span></button>
				</div>
			</center>
        </div>
        
		
         <!--view album with picture and name-->
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
								<div class="col-lg-3 col-md-3  col-sm-12 col-xm-12" style="height:410px;padding:2px;margin-top:20px;">
									<div id="box" style="border:1px solid gray;">
										<div id="image" style="height:80%;width:100%;background:url('<?php echo $mydata1->picture->data->url;?>');background-position:center;background-size:cover;    ">
										 </div>
										 <h4 style="margin-left:10px;"><input type="checkbox"  class="messageCheckbox"  value="<?php echo $mydata->id; ?>" onchange="checkuncheck(this)"><span style="padding-left:10px;"><strong><?php echo $mydata->name; ?></strong></span></h4>
										  <div id="buttongrp" style="padding-bottom:15px;">
											   <center>
												<button type="button" class="btn btn-default" onclick="loadslideshow(<?php echo $mydata->id; ?>)"><span class="glyphicon glyphicon-eye-open "></span><span class="btntext" style="margin-left:5px;">View</span></button>
												<button type="button" class="btn btn-default" onclick="createzip(<?php echo $mydata->id; ?>)"><span class="glyphicon glyphicon-save"></span><span class="btntext" style="margin-left:5px;">Download</span></button>  
												 <a href="uploadgoogledrive.php?albumid=<?php echo $mydata->id;?>" ><button type="button" class="btn btn-default"><span class="glyphicon glyphicon-share"></span><span class="btntext" style="margin-left:5px;">Share</span></button></a>
											</div>    
									  </div>
								  </div>
										
                <?php	
								break;
                            }
                        }
                    }
                ?>
           </div>
		</div>
    </body>
</html>