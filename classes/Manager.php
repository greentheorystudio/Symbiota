<?php
include_once(__DIR__ . '/../services/DbService.php');

class Manager  {
	protected $conn;
	protected $id;
    protected $errorMessage = '';
    protected $warningArr = array();

	protected $logFH;
	protected $verboseMode = 0;

    public function __construct($id = null){
		$connection = new DbService();
    	$this->conn = $connection->getConnection();
 		if($id !== null || is_numeric($id)){
	 		$this->id = $id;
 		}
	}

 	public function __destruct(){
 		if($this->logFH){
			fclose($this->logFH);
		}
	}

	protected function setLogFH($path): void
	{
		$this->logFH = fopen($path, 'ab');
	}

	protected function logOrEcho($str, $indexLevel = null, $tag = null): void
	{
		if(!$tag){
            $tag = 'li';
        }
	    if($this->verboseMode){
			if($this->verboseMode === 3 || $this->verboseMode === 1){
				if($this->logFH){
					fwrite($this->logFH,$str);
				}
			}
			if($this->verboseMode === 3 || $this->verboseMode === 2){
				echo '<'.$tag.' style="'.($indexLevel?'margin-left:'.($indexLevel*15).'px':'').'">'.$str.'</'.$tag.'>';
				flush();
			}
		}
	}

    public function setVerboseMode($c): void
	{
		$this->verboseMode = $c;
	}

	public function getErrorMessage(): string
	{
		return $this->errorMessage;
	}

    public function getWarningArr(): array
    {
        return $this->warningArr;
    }
}
