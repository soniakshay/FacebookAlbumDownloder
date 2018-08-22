<?php
	//destroy all sesssion
	session_start();
	session_destroy();
	header('Location:../index.php');
?>