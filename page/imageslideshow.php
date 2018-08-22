<?php
require_once '../vendor/facebook/autoload.php'; // change path as needed
session_start();
error_reporting(0);
if(isset($_GET['state'])) {
    
    $_SESSION['FBRLH_state'] = $_GET['state'];
}

require_once '../config/config.php'; 
$access_token=$_SESSION['fb_access_token'];
$albumid=$_GET["id"];

// this url get the json data of images with album wise 
$url1="https://graph.facebook.com/v3.1/".$albumid."/photos?fields=images%2Calbum&access_token=".$access_token;
$result = file_get_contents($url1);
$pic=json_decode($result);

//paging 
$a=(array)$pic->paging;
$imagelinksarray=array();  
if(array_key_exists("next",$a))
{
        $url1=$a["next"];
}

        do
        {            
                if(array_key_exists("next",$a))
                {
                    $url1=$a["next"];
                }
                else
                {
                    $url1="none";
                }
                foreach($pic->data as $mydata)
                {

                    $str=$mydata->images[1]->source;
                    
                    $imagelinksarray[]=$str;
                        
                }
                if($url1!="none")
                {
                    $result = file_get_contents($url1);
                }
                $pic=json_decode($result);

                $a=(array)$pic->paging;

        
        }while($url1!="none");
    
        
?>
<!DOCTYPE html>
<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Facebook Album Downloader</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
		<link rel="stylesheet" href="../assets/css/style.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
		<script>
			//all imageUrl store php Array to javascript Array
			
			var link = new Array();
			<?php 
				foreach($imagelinksarray as $val)
				{ 
			?>
					link.push('<?php echo $val; ?>');
			<?php 
				} 
			?>
		</script>
		<script src="../assets/js/slideshow.js"></script>
	</head>
    <body onload="onloadimage()">
		<!--navigation button-->
		<button onclick="previous()" class="slidenavbtn"><</button>
		<button onclick="next()" class="slidenavbtn" style="left:90%;">></button>
		
		<!-- image url holder-->
		<div class="container">
			<center><img src="" id="img"  style="height:340px;" class="img-responsive"  altr="Failed Image Loading.."/></center>
		</div>
		
	</body>
</html>
