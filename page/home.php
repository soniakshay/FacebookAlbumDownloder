<?php
    session_start();//session start
    //if check login or not if not login then redirect login page
    if(!isset($_SESSION['fb_access_token'] ))
    {
        header('Location:../index.php');
    }
    
    require_once '../vendor/autoload.php'; // change path as needed
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
		<title>Facebook Album Downloder</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
		<link rel="stylesheet" href="../css/style.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
		<script>
            //open new window for slideshow image
            function openNewWindow(accesstoken,id)
			{
				str="imageslideshow.php?id="+id;
				window.open(str, "_blank","height=340px width=600px");

			}
            //create zip for one album
			function createzip(id) {
                document.getElementById("lodingdiv").style.display="block";
                document.getElementById("sts").style.display="block";
                document.getElementById("demo").style.display="none";
                    
                var xhttp = new XMLHttpRequest();
				xhttp.onreadystatechange = function() {
				if (this.readyState == 4 && this.status == 200) {
                    if(this.responseText!="false")
                    {
                        document.getElementById("sts").style.display="none";
                        document.getElementById("demo").style.display="block";
                        document.getElementById("demo").href=this.responseText; 
                    }
                    else
                    {
                            document.getElementById("sts").innerHTML="Something Error";
                    
                    }
                }
			  };
				xhttp.open("GET", "createalbumzip.php?albumid=" + id, true);
				xhttp.send();
			}
            
            //checkitem contain collect all chcked album albumid
            var checkitem = new Array();
            function checkuncheck(a) 
            {

                if(a.checked==false)
                {
                        const index = checkitem.indexOf(a.value);
                        checkitem.splice(index, 1);
	           }
                else
                {	
		
                        checkitem.push(a.value);
	           }

            }
            //this function create for selected album
            function createzipwithseletedalbum()
            {
                document.getElementById("lodingdiv").style.display="block";
                document.getElementById("sts").style.display="block";
                document.getElementById("demo").style.display="none";
                    
                var xhttp = new XMLHttpRequest();
				xhttp.onreadystatechange = function() {
				if (this.readyState == 4 && this.status == 200) {
                        if(this.responseText!="false")
                        {
                            document.getElementById("sts").style.display="none";
                            document.getElementById("demo").style.display="block";
                            document.getElementById("demo").href=this.responseText; 
                        }
                        else
                        {
                                document.getElementById("sts").innerHTML="Something Error";

                        }
                    }
                };
				xhttp.open("GET", "createalbumzip.php?albumid=" + checkitem.toString(), true);
				xhttp.send();
                    
            }
            // this function create for downloud all album 
            function createzipwithallalbum()
            {
                allalbumid= '<?php echo $allalbumid ;?>';
                
                document.getElementById("lodingdiv").style.display="block";
                document.getElementById("sts").style.display="block";
                document.getElementById("demo").style.display="none";
                    
                var xhttp = new XMLHttpRequest();
				xhttp.onreadystatechange = function() {
				if (this.readyState == 4 && this.status == 200) {
                        if(this.responseText!="false")
                        {
                            document.getElementById("sts").style.display="none";
                            document.getElementById("demo").style.display="block";
                            document.getElementById("demo").href=this.responseText; 
                        }
                        else
                        {
                                document.getElementById("sts").innerHTML="Something Error";

                        }
                    }
                };
				xhttp.open("GET", "createalbumzip.php?albumid=" + allalbumid, true);
				xhttp.send();
                  
            }
            //drive uplode alertbox hide 
            function alertboxoff()	
            {	
               document.getElementById("uplodestatus").style.display="none";

            }
          setInterval(alertboxoff,5000);

            
		</script>
	</head>
    <body>
        <!--navigation-->
		<nav class="navbar navbar-default" >
			<div class="container-fluid">
				<div class="navbar-header">
					<a class="navbar-brand" href="#"><b class="text-info">Facebook Album Downloder</b></a>
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
                   Uplode Album   <strong>SuccessFully!</strong>
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
                <button type="button" class="btn btn-secondary" onclick="createzipwithseletedalbum()"><span class="glyphicon glyphicon-save"></span><span> Downloud Selected Album</span></button>
                <button type="button" class="btn btn-secondary"  onclick="createzipwithallalbum()"><span class="glyphicon glyphicon-save"></span><span> Downloud All Album</span></button>
                
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
                                            <?php echo "<a href= '#' onClick= openNewWindow('$access_token','$mydata->id');>" ?><button type="button" class="btn btn-default"><span class="glyphicon glyphicon-eye-open "></span><span class="btntext" style="margin-left:5px;">View</span></button></a>
                                            <button type="button" class="btn btn-default" onclick="createzip(<?php echo $mydata->id; ?>)"><span class="glyphicon glyphicon-save"></span><span class="btntext" style="margin-left:5px;">downloud</span></button>  
                                             <a href="uplodegoogledrive.php?albumid=<?php echo $mydata->id;?>" ><button type="button" class="btn btn-default"><span class="glyphicon glyphicon-share"></span><span class="btntext" style="margin-left:5px;">Share</span></button></a>
                                            
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