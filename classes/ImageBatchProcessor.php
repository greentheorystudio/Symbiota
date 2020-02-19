<?php
if(isset($SERVER_ROOT) && $SERVER_ROOT){
	include_once($SERVER_ROOT.'/classes/ImageLocalProcessor.php');
}
elseif(isset($SERVER_ROOT) && $SERVER_ROOT){
	if(file_exists($SERVER_ROOT.'/classes/DbConnection.php')){
		include_once($SERVER_ROOT.'/classes/DbConnection.php');
	}
	else{
		include_once('ImageBatchConnectionFactory.php');
	}
	if (file_exists($SERVER_ROOT.'/classes/ImageLocalProcessor.php')) { 
		require_once($SERVER_ROOT.'/classes/ImageLocalProcessor.php');
	}
}
else if(file_exists('ImageLocalProcessor.php')) {
	require_once('ImageLocalProcessor.php');
}

class ImageBatchProcessor extends ImageLocalProcessor {}
