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
		// this class use for create zip
        $zip = new ZipArchive();
        require_once '../vendor/facebook/autoload.php'; 
        
		if(isset($_GET['state'])) {

            $_SESSION['FBRLH_state'] = $_GET['state'];
        }
		// add config file
        require_once '../config/config.php';    
		
		// add session value into the variable
        $access_token=$_SESSION['fb_access_token'];
		
        // create zip file name with rand function
		$zipfilename=rand(1,99999999).rand(1,9999999).rand(1,9999999);
		
		// open zip		
        $zip->open('zip/'.$zipfilename.'.zip', ZipArchive::CREATE); 
         
		 //get the albumids and create one array
		$albumid1=explode(",",$_GET['albumid']);
        
		foreach($albumid1 as $albumid)
        {

				//foldername crate with rand function
				$foldername=rand(1,99999999).rand(1,9999999).rand(1,9999999); 
               
			   // create directory in files folder with foldername
				mkdir('files/'.$foldername,0755);    
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
        echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]"."/FacebookAlbumDownloader/page/zip/".$zipfilename;
}
catch(Exception $e)
{
    echo "false";
}

?>
