<?php
session_start();//session start
//if check login or not if not login then redirect login page
if(!isset($_SESSION['fb_access_token'] ))
{
    header('Location:../index.php');
}
error_reporting(0);
try
{       
        $zip = new ZipArchive();// this class use for create zip
        require_once '../vendor/autoload.php'; 
        if(isset($_GET['state'])) {

            $_SESSION['FBRLH_state'] = $_GET['state'];
        }
        require_once '../config/config.php';    // add config file
        $access_token=$_SESSION['fb_access_token']; // add session value into the variable
        $zipfilename=rand(1,99999999).rand(1,9999999).rand(1,9999999); // create zip file name with rand function
        $zip->open('zip/'.$zipfilename.'.zip', ZipArchive::CREATE); // open zip
        $albumid1=explode(",",$_GET['albumid']); //get the albumids and create one array
        foreach($albumid1 as $albumid)
        {

                $foldername=rand(1,99999999).rand(1,9999999).rand(1,9999999); //foldername crate with rand function
                mkdir('files/'.$foldername,007);    // create directory in files folder with foldername
                $path='files/'.$foldername.'/';
                //image  fetch with albumwise
                $url1="https://graph.facebook.com/v3.1/".$albumid."/photos?fields=images%2Calbum&access_token=".$access_token;
                $result = file_get_contents($url1);
                $pic=json_decode($result);
                $existphotokey=(array)$pic;
                $a=(array)$pic->paging;
                if(array_key_exists("next",$a))
                {
                        $url1=$a["next"];
                }
                //fetch image with paging
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
                }
        $zip->close();
        $zipfilename=$zipfilename.'.zip';
        //ziplink        
        echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]"."/FacebookAlbumDownloder/page/zip/".$zipfilename;
}
catch(Exception $e)
{
    echo "false";
}

?>
