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

                    $str=$mydata->images[0]->source;
                    
                    $imagelinksarray[]=$str;
                        
                }
                if($url1!="none")
                {
                    $result = file_get_contents($url1);
                }
                $pic=json_decode($result);

                $a=(array)$pic->paging;

        
        }while($url1!="none");
    
  echo json_encode($imagelinksarray);      
?>