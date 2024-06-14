<?php
include_once(__DIR__ . '/../services/DbConnectionService.php');
include_once(__DIR__ . '/../services/SanitizerService.php');
 
class InventoryDynSqlManager {

	private $conn;
	private $clid;
	private $clName;
	
	public function __construct($id) {
		$connection = new DbConnectionService();
		$this->conn = $connection->getConnection();
		if(is_numeric($id)){
			$this->clid = $id;
		}
	}

	public function __destruct(){
 		if($this->conn) {
			$this->conn->close();
		}
	}
	
	public function getDynamicSql(): string
	{
		$sqlStr = '';
		if($this->clid){
			$sql = 'SELECT c.dynamicsql FROM fmchecklists c WHERE (c.clid = '.$this->clid.')';
			//echo $sql;
			$rs = $this->conn->query($sql);
			while($row = $rs->fetch_object()){
				$sqlStr = $row->dynamicsql;
			}
			$rs->close();
		}
		return $sqlStr;
	}
	
	public function testSql($strFrag): bool
	{
		$sql = 'SELECT * FROM omoccurrences o WHERE '.SanitizerService::cleanInStr($this->conn,$strFrag);
		if($this->conn->query($sql)){
			return true;
		}
		return false;
	}
	
	public function saveSql($sqlFrag): void
	{
		$sql = 'UPDATE fmchecklists c SET c.dynamicsql = "'.SanitizerService::cleanInStr($this->conn,$sqlFrag).'" WHERE (c.clid = '.$this->clid.')';
		$this->conn->query($sql);
	}

	public function getClName(){
		return $this->clName;
	}
}
