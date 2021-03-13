<?php
include_once(__DIR__ . '/DbConnection.php');

class Manager  {
	protected $conn;
	protected $id;
    protected $errorMessage = '';
    protected $warningArr = array();

	protected $logFH;
	protected $verboseMode = 0;

    public function __construct($id = null){
		$connection = new DbConnection();
    	$this->conn = $connection->getConnection();
 		if($id !== null || is_numeric($id)){
	 		$this->id = $id;
 		}
	}

 	public function __destruct(){
 		if(!($this->conn === null)) {
			$this->conn->close();
		}
		if($this->logFH){
			fclose($this->logFH);
		}
	}

	protected function setLogFH($path): void
	{
		$this->logFH = fopen($path, 'ab');
	}

	protected function logOrEcho($str, $indexLevel=0, $tag = 'li'): void
	{
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

    public function checkFieldExists($table, $field): bool
	{
        $exists = false;
        $sql = 'SHOW COLUMNS FROM '.$table.' WHERE field = "'.$field.'"';
        //echo "<div>SQL: ".$sql."</div>";
        $result = $this->conn->query($sql);
        if($result->num_rows) {
			$exists = true;
		}
        return $exists;
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

	protected function cleanOutStr($str){
		return str_replace(array('"', "'"), array('&quot;', '&apos;'), $str);
	}

	protected function cleanInStr($str){
		$newStr = trim($str);
		if($newStr){
			$newStr = preg_replace('/\s\s+/', ' ',$newStr);
			$newStr = $this->conn->real_escape_string($newStr);
		}
		return $newStr;
	}

	protected function cleanInArray($arr): array
	{
		$newArray = array();
		foreach($arr as $key => $value){
			$newArray[$this->cleanInStr($key)] = $this->cleanInStr($value);
		}
		return $newArray;
	}

}
