<?php
include_once(__DIR__ . '/../services/DbConnectionService.php');
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
            $connection = new DbConnectionService();
		    $this->conn = $connection->getConnection();
		}
	}

	public function __destruct(){
		if($this->destructConn && $this->conn){
			$this->conn->close();
			$this->conn = null;
		}
	}

	public function populateGuids($collId = null): void
	{
		set_time_limit(1000);
		
		$this->echoStr('Starting batch GUID processing (' .date('Y-m-d h:i:s A').")\n");

		$this->echoStr('Populating collection GUIDs (all collections by default)');
		$sql = 'SELECT collid FROM omcollections WHERE collectionguid IS NULL ';
		$rs = $this->conn->query($sql);
		$recCnt = 0;
		if($rs->num_rows){
			while($r = $rs->fetch_object()){
				$guid = UuidService::getUuidV4();
				$insSql = 'UPDATE omcollections SET collectionguid = "'.$guid.'" '.
					'WHERE collectionguid IS NULL AND collid = '.$r->collid;
				if(!$this->conn->query($insSql)){
					$this->echoStr('ERROR: Populating GUID.');
				}
				$recCnt++;
			}
			$rs->free();
		}
		$this->echoStr("Finished: $recCnt collection records processed\n");
		
		$this->echoStr("Populating occurrence GUIDs\n");
		$sql = 'SELECT o.occid '.
			'FROM omoccurrences o LEFT JOIN guidoccurrences g ON o.occid = g.occid '.
			'WHERE g.occid IS NULL ';
		if($collId) {
			$sql .= 'AND o.collid = ' . $collId;
		}
		$rs = $this->conn->query($sql);
		$recCnt = 0;
		if($rs->num_rows){
			while($r = $rs->fetch_object()){
				$guid = UuidService::getUuidV4();
				$insSql = 'INSERT INTO guidoccurrences(guid,occid) '.
					'VALUES("'.$guid.'",'.$r->occid.')';
				if(!$this->conn->query($insSql)){
					$this->echoStr('ERROR: Populating guids.');
				}
				$recCnt++;
				if($recCnt%1000 === 0) {
					$this->echoStr($recCnt . ' records processed');
				}
			}
			$rs->free();
		}
		$this->echoStr("Finished: $recCnt occurrence records processed\n");
		
		$this->echoStr("Populating determination GUIDs\n");
		$sql = 'SELECT d.detid FROM omoccurdeterminations d LEFT JOIN guidoccurdeterminations g ON d.detid = g.detid ';
		if($collId) {
			$sql .= 'INNER JOIN omoccurrences o ON d.occid = o.occid ';
		}
		$sql .= 'WHERE g.detid IS NULL ';
		if($collId) {
			$sql .= 'AND o.collid = ' . $collId;
		}
		$rs = $this->conn->query($sql);
		$recCnt = 0;
		if($rs->num_rows){
			while($r = $rs->fetch_object()){
				$guid = UuidService::getUuidV4();
				$insSql = 'INSERT INTO guidoccurdeterminations(guid,detid) '.
					'VALUES("'.$guid.'",'.$r->detid.')';
				if(!$this->conn->query($insSql)){
					$this->echoStr('ERROR: Populating determination guids.');
				}
				$recCnt++;
				if($recCnt%1000 === 0) {
					$this->echoStr($recCnt . ' records processed');
				}
			}
			$rs->free();
		}
		$this->echoStr("Finished: $recCnt determination records processed\n");
		
		$this->echoStr("Populating image GUIDs\n");
		$sql = 'SELECT i.imgid FROM images i LEFT JOIN guidimages g ON i.imgid = g.imgid ';
		if($collId) {
			$sql .= 'INNER JOIN omoccurrences o ON i.occid = o.occid ';
		}
		$sql .= 'WHERE g.imgid IS NULL ';
		if($collId) {
			$sql .= 'AND o.collid = ' . $collId;
		}
		$rs = $this->conn->query($sql);
		$recCnt = 0;
		if($rs->num_rows){
			while($r = $rs->fetch_object()){
				$guid = UuidService::getUuidV4();
				$insSql = 'INSERT INTO guidimages(guid,imgid) '.
					'VALUES("'.$guid.'",'.$r->imgid.')';
				if(!$this->conn->query($insSql)){
					$this->echoStr('ERROR: Populating image guids.');
				}
				$recCnt++;
				if($recCnt%1000 === 0) {
					$this->echoStr($recCnt . ' records processed');
				}
			}
			$rs->free();
		}
		$this->echoStr("Finished: $recCnt image records processed\n");
		
		$this->echoStr('GUID batch processing complete (' .date('Y-m-d h:i:s A').")\n");
	}

    public function getOccurrenceCount($collId = null): int
	{
		$retCnt = 0;
		$sql = 'SELECT COUNT(o.occid) as reccnt '.
			'FROM omoccurrences o LEFT JOIN guidoccurrences g ON o.occid = g.occid '.
			'WHERE g.occid IS NULL ';
		if($collId) {
			$sql .= 'AND o.collid = ' . $collId;
		}
		//echo $sql;
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retCnt = $r->reccnt;
		}
		$rs->free();
		return $retCnt;
	}

	public function getDeterminationCount($collId = null): int
	{
		$retCnt = 0;
		$sql = 'SELECT COUNT(d.detid) as reccnt '.
			'FROM omoccurdeterminations d LEFT JOIN guidoccurdeterminations g ON d.detid = g.detid ';
		if($collId) {
			$sql .= 'INNER JOIN omoccurrences o ON d.occid = o.occid ';
		}
		$sql .= 'WHERE g.detid IS NULL ';
		if($collId) {
			$sql .= 'AND o.collid = ' . $collId;
		}
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retCnt = $r->reccnt;
		}
		$rs->free();
		return $retCnt;
	}

	public function getImageCount($collId = null): int
	{
		$retCnt = 0;
		$sql = 'SELECT COUNT(i.imgid) as reccnt '.
			'FROM images i LEFT JOIN guidimages g ON i.imgid = g.imgid ';
		if($collId) {
			$sql .= 'INNER JOIN omoccurrences o ON i.occid = o.occid ';
		}
		$sql .= 'WHERE g.imgid IS NULL ';
		if($collId) {
			$sql .= 'AND o.collid = ' . $collId;
		}
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retCnt = $r->reccnt;
		}
		$rs->free();
		return $retCnt;
	}
	
	public function getCollectionName($collId): string
	{
		$retStr = '';
		$sql = 'SELECT CONCAT(collectionname," (",CONCAT_WS("-",institutioncode,collectioncode),")") as collname '.
			'FROM omcollections WHERE collid = '.$collId;
		//echo $sql;
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retStr = $r->collname;
		}
		$rs->free();
		return $retStr;
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
