<?php
include_once(__DIR__ . '/../services/DbService.php');
include_once(__DIR__ . '/../services/UuidService.php');

class GUIDManager {

	private $silent = 0;
	private $conn;
	private $destructConn = true;

	public function __construct($con = null){
		if($con){
			$this->conn = $con;
			$this->destructConn = false;
		}
		else{
            $connection = new DbService();
		    $this->conn = $connection->getConnection();
		}
	}

	public function __destruct(){
		if($this->destructConn && $this->conn){
			$this->conn->close();
			$this->conn = null;
		}
	}

    public function setSilent($c): void
	{
		$this->silent = $c;
	}

	private function echoStr($str): void
	{
		if(!$this->silent){
			echo '<li>'.$str.'</li>';
			flush();
		}
	}
}
