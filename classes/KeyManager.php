<?php
include_once(__DIR__ . '/DbConnection.php');

class KeyManager{

	protected $conn;
	protected $language = 'English';

	public function __construct(){
		$connection = new DbConnection();
		$this->conn = $connection->getConnection();
	}

	public function __destruct(){
		if($this->conn) {
			$this->conn->close();
		}
	}

	protected function deleteDescr($tidStr, $charStr = null, $csStr = null): void
	{
		if($tidStr){
			$sqlWhere = '(TID In ('.$tidStr.')) ';
			if($charStr) {
                $sqlWhere .= 'AND (CID IN (' . $charStr . '))';
            }
			if($csStr) {
                $sqlWhere .= 'AND (cs IN (' . $csStr . '))';
            }
			
			$sql = 'DELETE FROM kmdescr WHERE '.$sqlWhere;
			//echo "<div>".$sql."</div>";
			$this->conn->query($sql);
		}
	}

	protected function insertDescr($tid, $cid, $cs): void
	{
		if(is_numeric($tid) && is_numeric($cid) && $cs){
			$sql = 'INSERT INTO kmdescr (TID, CID, CS, Source) VALUES ('.$tid.', '.$cid.", '".$cs."', '".$GLOBALS['USERNAME']."')";
			$this->conn->query($sql);
		}
	}

	protected function deleteInheritance($tidStr,$cidStr): void
	{
		if($tidStr){
			$childrenStr = trim(implode(',',$this->getChildrenArr($tidStr)).','.$tidStr,' ,');
			$sql = 'DELETE FROM kmdescr '.
				'WHERE (TID IN('.$childrenStr.')) '.
				'AND (CID IN('.$cidStr.")) AND (Inherited Is Not Null AND Inherited <> '')";
			//echo $sql;
			$this->conn->query($sql);
		}
	}

	protected function resetInheritance($tidStr, $cidStr): void
	{
		$cnt = 0;
		$childrenStr = trim(implode(',',$this->getChildrenArr($tidStr)).','.$tidStr,' ,'); 
		do{
			$sql = 'INSERT IGNORE INTO kmdescr( TID, CID, CS, Modifier, X, TXT, Seq, Notes, Inherited ) '.
				'SELECT DISTINCT t2.TID, d1.CID, d1.CS, d1.Modifier, d1.X, d1.TXT, '.
				'd1.Seq, d1.Notes, IFNULL(d1.Inherited,t1.SciName) AS parent '.
				'FROM taxa AS t1 INNER JOIN kmdescr d1 ON t1.TID = d1.TID '.
				'INNER JOIN taxstatus ts1 ON d1.TID = ts1.tid '.
				'INNER JOIN taxstatus ts2 ON ts1.tidaccepted = ts2.ParentTID '.
				'INNER JOIN taxa t2 ON ts2.tid = t2.tid '.
				'LEFT JOIN kmdescr d2 ON (d1.CID = d2.CID) AND (t2.TID = d2.TID) '.
				'WHERE (ts2.tid = ts2.tidaccepted) '.
				'AND (d1.cid IN('.$cidStr.')) AND (t2.tid IN('.$childrenStr.')) AND (d2.CID Is Null) AND (t2.RankId <= 220)';
			//echo $sql.'<br/><br/>';
			if(!$this->conn->query($sql)){
				echo 'ERROR setting inheritance.';
			}
			$cnt++;
		}
		while($this->conn->affected_rows && $cnt < 10);
	}

	protected function getChildrenArr($tid): array
	{
		$retArr = array();
		if($tid){
			$targetStr = $tid;
			do{
				if(isset($targetList)) {
					unset($targetList);
				}
				$targetList = array();
				$sql = 'SELECT t.tid '.
					'FROM taxa t INNER JOIN taxstatus ts ON t.tid = ts.tid '.
					'WHERE (ts.ParentTID In ('.$targetStr.')) AND (ts.tid = ts.tidaccepted)';
				$rs = $this->conn->query($sql);
				while($row = $rs->fetch_object()){
					$targetList[] = $row->tid;
					$retArr[] = $row->tid;
			    }
			    $rs->free();
				if($targetList){
					$targetStr = implode(',', $targetList);
				}
			}
			while($targetList);
		}
		return $retArr;
	}
	
	protected function getParentArr($tid): array
	{
 		$retArr = array();
 		if($tid){
			$targetTid = $tid;
			while($targetTid){
				$sql = 'SELECT parenttid FROM taxstatus '.
					'WHERE (tid = '.$targetTid.')';
				//echo $sql;
				$rs = $this->conn->query($sql);
			    if($row = $rs->fetch_object()){
			    	if(!$row->parenttid || $targetTid === $row->parenttid) {
						break;
					}
					$targetTid = $row->parenttid;
					if($targetTid) {
						$retArr[] = $targetTid;
					}
			    }
                $rs->free();
			}
		}
		return $retArr;
	}

	public function setLanguage($lang): void
	{
		$lang = strtolower($lang);
		if(strlen($lang) === 2){
			if($lang === 'en') {
				$lang = 'english';
			}
			if($lang === 'es') {
				$lang = 'spanish';
			}
			if($lang === 'fr') {
				$lang = 'french';
			}
		}
		$this->language = $lang;
	}
}
