<?php
if(isset($_GET['albumid']))
{
        $zip = new ZipArchive();
        require_once '../vendor/autoload.php'; 
        session_start();
        if(isset($_GET['state'])) {

            $_SESSION['FBRLH_state'] = $_GET['state'];
        }
        require_once '../config/config.php';    
        $access_token=$_SESSION['fb_access_token'];
        $zipfilename=rand(1,99999999).rand(1,9999999).rand(1,9999999);

        $zip->open('zip/'.$zipfilename.'.zip', ZipArchive::CREATE);
                
        $albumid1=explode(",",$_GET['albumid']);
        
        foreach($albumid1 as $albumid)
        {

                $foldername=rand(1,99999999).rand(1,9999999).rand(1,9999999);

                mkdir('files/'.$foldername,007);

                $path='files/'.$foldername.'/';
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
                }
        $zip->close();

        $zipfilename=$zipfilename.'.zip';
		$_SESSION['zipfilename']=$zipfilename;
}
$url_array = explode('?', 'http://'.$_SERVER ['HTTP_HOST'].$_SERVER['REQUEST_URI']);
$url = $url_array[0];

require_once '../google-api-php-client/src/Google_Client.php';
require_once '../google-api-php-client/src/contrib/Google_DriveService.php';
$client = new Google_Client();
$client->setClientId('');
$client->setClientSecret('');
$client->setRedirectUri($url);
$client->setScopes(array('https://www.googleapis.com/auth/drive'));
if (isset($_GET['code'])) {
    $_SESSION['accessToken'] = $client->authenticate($_GET['code']);
    header('location:'.$url);exit;
} elseif (!isset($_SESSION['accessToken'])) {
    $client->authenticate();
}
/*$files= array();
$dir = dir('files');
while ($file = $dir->read()) {
    if ($file != '.' && $file != '..') {
        $files[] = $file;
    }
}
*/
$files=array();
$files[0]=$_SESSION['zipfilename'];
//$dir->close();

    $client->setAccessToken($_SESSION['accessToken']);
    $service = new Google_DriveService($client);
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $file = new Google_DriveFile();
    foreach ($files as $file_name) {
        $file_path = 'zip/'.$file_name;
        $mime_type = finfo_file($finfo, $file_path);
        $file->setTitle($file_name);
        $file->setDescription('This is a '.$mime_type.' document');
        $file->setMimeType($mime_type);
        $service->files->insert(
            $file,
            array(
                'data' => file_get_contents($file_path),
                'mimeType' => $mime_type
            )
        );
    }
    finfo_close($finfo);
    header('Location:home.php');
	exit;

?>
