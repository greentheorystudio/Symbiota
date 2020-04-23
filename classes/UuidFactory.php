<?php
include_once('DbConnection.php');

class UuidFactory {

	public const METHOD = 'aes-256-cbc';
	private $silent = 0;
	private $conn;
	private $destructConn = true;

	public function __construct($con = null){
		if($con){
			$this->conn = $con;
			$this->destructConn = false;
		}
		else{
            $connection = new DbConnection();
		    $this->conn = $connection->getConnection();
		}
	}

	public function __destruct(){
		if($this->destructConn && !($this->conn === null)){
			$this->conn->close();
			$this->conn = null;
		}
	}

	public function populateGuids($collId = 0): void
	{
		set_time_limit(1000);
		
		$this->echoStr('Starting batch GUID processing (' .date('Y-m-d h:i:s A').")\n");

		$this->echoStr('Populating collection GUIDs (all collections by default)');
		$sql = 'SELECT collid FROM omcollections WHERE collectionguid IS NULL ';
		$rs = $this->conn->query($sql);
		$recCnt = 0;
		if($rs->num_rows){
			while($r = $rs->fetch_object()){
				$guid = self::getUuidV4();
				$insSql = 'UPDATE omcollections SET collectionguid = "'.$guid.'" '.
					'WHERE collectionguid IS NULL AND collid = '.$r->collid;
				if(!$this->conn->query($insSql)){
					$this->echoStr('ERROR: '.$this->conn->error);
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
				$guid = self::getUuidV4();
				$insSql = 'INSERT INTO guidoccurrences(guid,occid) '.
					'VALUES("'.$guid.'",'.$r->occid.')';
				if(!$this->conn->query($insSql)){
					$this->echoStr('ERROR: occur guids'.$this->conn->error);
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
				$guid = self::getUuidV4();
				$insSql = 'INSERT INTO guidoccurdeterminations(guid,detid) '.
					'VALUES("'.$guid.'",'.$r->detid.')';
				if(!$this->conn->query($insSql)){
					$this->echoStr('ERROR: det guids '.$this->conn->error);
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
				$guid = self::getUuidV4();
				$insSql = 'INSERT INTO guidimages(guid,imgid) '.
					'VALUES("'.$guid.'",'.$r->imgid.')';
				if(!$this->conn->query($insSql)){
					$this->echoStr('ERROR: image guids; '.$this->conn->error);
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

    public function getOccurrenceCount($collId = 0): int
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

	public function getDeterminationCount($collId = 0): int
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

	public function getImageCount($collId = 0): int
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

	public static function getUuidV4(): string
	{
		$data = null;
		if(function_exists('openssl_random_pseudo_bytes')){
			$secure = false;
			$dataSize = openssl_cipher_iv_length(self::METHOD);
			do {
				$data = openssl_random_pseudo_bytes($dataSize, $secure);
			} while(!$data || !$secure);
		}
		if(!$data && file_exists('/dev/urandom')){
			$data = file_get_contents('/dev/urandom', NULL, NULL, 0, 16);
		}
		if(!$data && file_exists('/dev/random')){
			$data = file_get_contents('/dev/random', NULL, NULL, 0, 16);
		}
		if(!$data){
			for($cnt = 0; $cnt < 16; $cnt ++) {
				try {
					$data .= chr(random_int(0, 255));
				} catch (Exception $e) {}
			}
		}
		if(!$data) {
			return '';
		}

		$data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
		$data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10

		return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
	}

}
