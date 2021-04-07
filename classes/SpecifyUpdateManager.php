<?php
include_once(__DIR__ . '/DbConnection.php');

class SpecifyUpdateManager{

	private $conn;

    public function __construct() {
        $connection = new DbConnection();
        $this->conn = $connection->getConnection();
    }
 	
 	public function __destruct(){
		if($this->conn) $this->conn->close();
	}
	
	public function getSpecifyCollectionList(){
		$retArr = array();
		$sql = 'SELECT c.CollID, c.CollectionName '.
			'FROM omcollections AS c LEFT JOIN uploadspecparameters AS up ON c.CollID = up.CollID '.
			'WHERE up.title = "Specify Updater" '.
			'ORDER BY c.CollectionName ';
		//echo $sql;
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
				$retArr[$r->CollID] = $r->CollectionName;
			}
			$rs->close();
		}
		return $retArr;
	}
	
	public function getCollectionList(){
		$retArr = array();
		$sql = 'SELECT c.CollID, c.CollectionName '.
			'FROM omcollections AS c LEFT JOIN uploadspecparameters AS up ON c.CollID = up.CollID '.
			'WHERE c.CollID NOT IN(SELECT CollID FROM uploadspecparameters WHERE title = "Specify Updater") '.
			'ORDER BY c.CollectionName ';
		//echo $sql;
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
				$retArr[$r->CollID] = $r->CollectionName;
			}
			$rs->close();
		}
		return $retArr;
	}
	
	public function getUspid($collId){
		$uspid = '';
		$sql = 'SELECT uspid '.
			'FROM uploadspecparameters '.
			'WHERE CollID = '.$collId.' AND title = "Specify Updater" ';
		//echo $sql;
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
				$uspid = $r->uspid;
			}
			$rs->close();
		}
		return $uspid;
	}
	
	public function addUploadProfile($collId){
		global $clientRoot;
		$urlPath = '';
		$urlPath = "http://";
		if(!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) $urlPath = "https://";
		$urlPath .= $_SERVER["SERVER_NAME"];
		if($_SERVER["SERVER_PORT"] && $_SERVER["SERVER_PORT"] != 80) $urlPath .= ':'.$_SERVER["SERVER_PORT"];
		$urlPath .= $clientRoot.(substr($clientRoot,-1)=='/'?'':'/').'collections/datasets/dwc/'.$collId.'_DwC-A_temp.zip';
		$sql = 'INSERT INTO uploadspecparameters(collid, uploadtype, title, path) VALUES ('.
			$collId.',6,"Specify Updater","'.$urlPath.'")';
		//echo $sql;
		if(!$this->conn->query($sql)){
			return '<div>Error Adding Upload Parameters: '.$this->conn->error.'</div><div style="margin-left:10px;">SQL: '.$sql.'</div>';
		}
		return 'SUCCESS: New upload profile added';
	}
	
	private function cleanInStr($str){
		$newStr = trim($str);
		$newStr = preg_replace('/\s\s+/', ' ',$newStr);
		$newStr = $this->conn->real_escape_string($newStr);
		return $newStr;
	}
}
?>
