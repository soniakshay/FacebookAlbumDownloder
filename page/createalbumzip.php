<?php

$zip = new ZipArchive();
require_once '../vendor/autoload.php'; 
session_start();
if(isset($_GET['state'])) {
    
    $_SESSION['FBRLH_state'] = $_GET['state'];
}
require_once '../config/config.php';    
$access_token=$_SESSION['fb_access_token'];
$albumid=$_GET['albumid'];

$foldername=rand(1,99999999).rand(1,9999999).rand(1,9999999);
mkdir('files/'.$foldername,007);

$path='files/'.$foldername.'/';
$zip->open('zip/'.$foldername.'.zip', ZipArchive::CREATE);

$url1="https://graph.facebook.com/v3.1/".$albumid."/photos?fields=images%2Calbum&access_token=".$access_token;
$result = file_get_contents($url1);
$pic=json_decode($result);
$existphotokey=(array)$pic;
$a=(array)$pic->paging;

    
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
                    $url = $mydata->images[3]->source;
                    $img = $path.rand(1,9999999).rand(1,99999999).'.jpg';
                    file_put_contents($img, file_get_contents($url));
                    $zip->addFile("{$img}");    
                   
                }       
                if($url1!="none")
                {
                    $result = file_get_contents($url1);
                }
                $pic=json_decode($result);
                $existphotokey=(array)$pic;
                $a=(array)$pic->paging;

        
        }while($url1!="none");

$zip->close();

$zipfilename=$foldername.'.zip';
/* header('Content-Type: application/zip');
  header('Content-Disposition: attachment; filename='.$zipfilename);
  header('Content-Length: ' . filesize($zipfilename));
rmdir('files/'.$foldername);
*/
echo 'https://localhost/FacebookAlbumDownloder/page/zip/'.$zipfilename;

?>
