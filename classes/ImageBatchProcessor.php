<?php
if(isset($SERVER_ROOT) && $SERVER_ROOT){
	include_once($SERVER_ROOT.'/classes/ImageLocalProcessor.php');
}
elseif(isset($SERVER_ROOT) && $SERVER_ROOT){
	if(file_exists($SERVER_ROOT.'/config/dbconnection.php')){ 
		include_once($SERVER_ROOT.'/config/dbconnection.php');
	}
	else{
		include_once('ImageBatchConnectionFactory.php');
	}
	if (file_exists($SERVER_ROOT.'/classes/ImageLocalProcessor.php')) { 
		require_once($SERVER_ROOT.'/classes/ImageLocalProcessor.php');
	}
}
else{
	//Files reside in same folder and script is run from within the folder
	if(file_exists('ImageLocalProcessor.php')) { 
		require_once('ImageLocalProcessor.php');
	}
}

class ImageBatchProcessor extends ImageLocalProcessor {

	function __construct(){
		parent::__construct();
	}

	function __destruct(){
		parent::__destruct();
	}
}
?>
