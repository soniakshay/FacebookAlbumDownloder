<?php
	session_start();///session start
	//if check login or not if not login then redirect login page
	if(!isset($_SESSION['fb_access_token'] ))
	{
		header('Location:../index.php');
	}
	if(isset($_GET['albumid']))
	{
		$_SESSION['albumid']=$_GET['albumid'];
	}


	$url_array = explode('?', 'http://'.$_SERVER ['HTTP_HOST'].$_SERVER['REQUEST_URI']);
	require_once '../vendor/googlelib/vendor/autoload.php';
	$client = new Google_Client(); //create class for google clent
	$client->setClientId('');//client id
	$client->setClientSecret('');//clent secret
	$client->setRedirectUri($url_array[0]);
	$client->addScope(Google_Service_Drive::DRIVE);
	$client->setScopes(array('https://www.googleapis.com/auth/drive'));
	if(isset($_GET['code']))
	{
		$client->authenticate($_GET['code']);
		$access_token = $client->getAccessToken();
		$client->setAccessToken($access_token);

	}
	else
	{
		$auth_url = $client->createAuthUrl();
		header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
	}
		
		$service = new Google_Service_Drive($client);
		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		
		
		// get all folder name and id of google drive
		$foldername=array();
		$folderid=array();
		$pageToken = null;
		do {
				$response = $service->files->listFiles(array(
				'q' => "mimeType='application/vnd.google-apps.folder' and 'root' in parents and trashed=false",
				'spaces' => 'drive',
				'pageToken' => $pageToken,
				'fields' => 'nextPageToken, files(id, name)',
			));
			foreach ($response->files as $file) {
				array_push($foldername,$file->name);
				array_push($folderid,$file->id);
				}
			
		
		} while ($pageToken != null);
		
		
		$foldername_and_id=array_combine($foldername,$folderid); // this array combine all folder name and id of google drive
		
		
		//if check directory name exist or not on google drive if not exist the create directory
		$file = new Google_Service_Drive_DriveFile();
		if(in_array("FaceBook Album Downloder",$foldername))
		{
			$folderId =$foldername_and_id['FaceBook Album Downloder'] ;
		
		}
		else
		{

			$fileMetadata = new Google_Service_Drive_DriveFile(array(
			'name' => 'FaceBook Album Downloder',
			'mimeType' => 'application/vnd.google-apps.folder'));
			
			$file = $service->files->create($fileMetadata, array(
			'fields' => 'id'));

			$folderId = $file->id;
		
		}


	
	//move album to google drive  
	
	if(isset($_SESSION['albumid']))
	{
        require_once '../vendor/facebook/autoload.php'; 
        
        require_once '../config/config.php';    
        $access_token=$_SESSION['fb_access_token'];// add session value into the variable
		$albumid1=explode(",",$_SESSION['albumid']); // get albumids and create  one array
	    
		
		foreach($albumid1 as $albumid)
        {

                $foldername=rand(1,99999999).rand(1,9999999).rand(1,9999999);
				
				
				//create directory on google drive in FaceBook Album Downloder folder
				$fileMetadata = new Google_Service_Drive_DriveFile(array(
				'name' => $foldername,
				'parents' => array($folderId),
				'mimeType' => 'application/vnd.google-apps.folder'));
				
				$file = $service->files->create($fileMetadata, array(
				'fields' => 'id'));
				$folderid = $file->id;

				
				
				
				//fetch images with album wise
                $url1="https://graph.facebook.com/v3.1/".$albumid."/photos?fields=images%2Calbum&access_token=".$access_token;
                $result = file_get_contents($url1);
                $pic=json_decode($result);
                $existphotokey=(array)$pic;
                $a=(array)$pic->paging;
                if(array_key_exists("next",$a))
                {
                        $url1=$a["next"];
                }
                        //fetch all images with pagging
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
                                    $url = $mydata->images[0]->source;
                                    $img = rand(1,9999999).rand(1,99999999).'.jpg';
                                    
									//move images on Album directory
									$fileMetadata = new Google_Service_Drive_DriveFile(array(
									'name' => $img,
									'parents' => array($folderid)
									));
									$content = file_get_contents($url);
									$file = $service->files->create($fileMetadata, array(
									'data' => $content,
									'mimeType' => 'image/jpeg',
									'uploadType' => 'multipart',
									'fields' => 'id'));	

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
				
		}	
		finfo_close($finfo);
		header('Location:home.php?uplodestatus=success');
		exit;
?>
