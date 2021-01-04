<?php
include_once(__DIR__ . '/Manager.php');

class WebServiceBase extends Manager{

	public function __construct($id) {
		parent::__construct($id);
		$this->setLogFH('../content/logs/occurrenceWriter_'.date('Ymd').'.log');
	}

	public function validateSecurityKey($k): bool
	{
		global $SECURITY_KEY;
	    if(isset($SECURITY_KEY)){
			if($k === $SECURITY_KEY){
				return true;
			}

			$this->errorMessage = 'Security Key authentication failed';
			return false;
		}

		return true;
	}
}
