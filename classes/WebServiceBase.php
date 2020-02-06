<?php
require_once($SERVER_ROOT.'/classes/Manager.php');

class WebServiceBase extends Manager{

	function __construct($id,$conType) {
		parent::__construct($id,$conType);
		$this->setLogFH('../content/logs/occurrenceWriter_'.date('Ymd').'.log');
	}

	function __destruct(){
		parent::__destruct();
	}

	public function validateSecurityKey($k){
		global $SECURITY_KEY;
	    if(isset($SECURITY_KEY)){
			if($k == $SECURITY_KEY){
				return true;
			}
			else{
				$this->errorMessage = 'Security Key authentication failed';
				return false;
			}
		}
		else{
			return true;
		}
		return true;
	}
}
?>
