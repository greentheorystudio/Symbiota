<?php
include_once(__DIR__ . '/DbConnection.php');
include_once(__DIR__ . '/Sanitizer.php');
 
class InventoryDynSqlManager {

	private $conn;
	private $clid;
	private $clName;
	
	public function __construct($id) {
		$connection = new DbConnection();
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
		$sql = 'SELECT * FROM omoccurrences o WHERE '.Sanitizer::cleanInStr($strFrag);
		if($this->conn->query($sql)){
			return true;
		}
		return false;
	}
	
	public function saveSql($sqlFrag): void
	{
		$sql = 'UPDATE fmchecklists c SET c.dynamicsql = "'.Sanitizer::cleanInStr($sqlFrag).'" WHERE (c.clid = '.$this->clid.')';
		$this->conn->query($sql);
	}

	public function getClName(){
		return $this->clName;
	}
}
