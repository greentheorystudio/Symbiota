<?php
include_once(__DIR__ . '/DbConnection.php');

class SpecUpload{

	protected $conn;
	protected $collId;
	protected $uspid;
	protected $collMetadataArr = array();
	
	protected $title = '';
	protected $platform;
	protected $server;
	protected $port;
	protected $username;
	protected $password;
	protected $code;
	protected $path;
	protected $pKField;
	protected $schemaName;
	protected $queryStr;
	protected $storedProcedure;
	protected $lastUploadDate;
	protected $uploadType;

	protected $verboseMode = 1;
	private $logFH;
	protected $errorStr;

	protected $DIRECTUPLOAD = 1, $DIGIRUPLOAD = 2, $FILEUPLOAD = 3, $STOREDPROCEDURE = 4, $SCRIPTUPLOAD = 5, $DWCAUPLOAD = 6, $SKELETAL = 7, $IPTUPLOAD = 8, $NFNUPLOAD = 9;
	
	public function __construct() {
		$connection = new DbConnection();
		$this->conn = $connection->getConnection();
	}
	
	public function __destruct(){
 		if($this->conn) {
			$this->conn->close();
		}
		if(($this->verboseMode === 2) && $this->logFH) {
			fclose($this->logFH);
		}
	}
	
	public function setCollId($id): void
	{
		if(is_numeric($id)){
			$this->collId = $id;
			$this->setCollInfo();
		}
	}
	
	public function setUspid($id): void
	{
		if($id && is_numeric($id)){
			$this->uspid = $id;
		}
	}

	public function getUploadList(): array
	{
		$returnArr = array();
		if($this->collId){
			$sql = 'SELECT usp.uspid, usp.uploadtype, usp.title '.
				'FROM uploadspecparameters usp '.
				'WHERE (usp.collid = '.$this->collId.') '.
				'ORDER BY usp.uploadtype,usp.title';
			//echo $sql;
			$result = $this->conn->query($sql);
			while($row = $result->fetch_object()){
				$uploadType = $row->uploadtype;
				$uploadStr = '';
				if($uploadType == $this->DIRECTUPLOAD){
					$uploadStr = 'Direct Upload';
				}
				elseif($uploadType == $this->DIGIRUPLOAD){
					$uploadStr = 'DiGIR Provider Upload';
				}
				elseif($uploadType == $this->FILEUPLOAD){
					$uploadStr = 'File Upload';
				}
				elseif($uploadType == $this->SKELETAL){
					$uploadStr = 'Skeletal File Upload';
				}
				elseif($uploadType == $this->NFNUPLOAD){
					$uploadStr = 'NfN File Upload';
				}
				elseif($uploadType == $this->STOREDPROCEDURE){
					$uploadStr = 'Stored Procedure';
				}
				elseif($uploadType == $this->DWCAUPLOAD){
					$uploadStr = 'Darwin Core Archive Upload';
				}
				elseif($uploadType == $this->IPTUPLOAD){
					$uploadStr = 'IPT Resource';
				}
				$returnArr[$row->uspid]['title'] = $row->title.' ('.$uploadStr.' - #'.$row->uspid.')';
				$returnArr[$row->uspid]['uploadtype'] = $row->uploadtype;
			}
			$result->free();
		}
		return $returnArr;
	}

	private function setCollInfo(): void
	{
		if($this->collId){
			$sql = 'SELECT DISTINCT c.collid, c.collectionname, c.institutioncode, c.collectioncode, c.icon, c.colltype, c.managementtype, cs.uploaddate, c.securitykey, c.guidtarget '.
				'FROM omcollections c LEFT JOIN omcollectionstats cs ON c.collid = cs.collid '.
				'WHERE (c.collid = '.$this->collId.')';
			//echo $sql;
			$result = $this->conn->query($sql);
			while($row = $result->fetch_object()){
				$this->collMetadataArr['collid'] = $row->collid;
				$this->collMetadataArr['name'] = $row->collectionname;
				$this->collMetadataArr['institutioncode'] = $row->institutioncode;
				$this->collMetadataArr['collectioncode'] = $row->collectioncode;
				$dateStr = ($row->uploaddate?date('d F Y g:i:s', strtotime($row->uploaddate)): '');
				$this->collMetadataArr['uploaddate'] = $dateStr;
				$this->collMetadataArr['colltype'] = $row->colltype;
				$this->collMetadataArr['managementtype'] = $row->managementtype;
				$this->collMetadataArr['securitykey'] = $row->securitykey;
				$this->collMetadataArr['guidtarget'] = $row->guidtarget;
			}
			$result->free();
		}
	}
	
	public function getCollInfo($fieldStr = ''){
		if(!$this->collMetadataArr) {
			$this->setCollInfo();
		}
		if($fieldStr){
			if(array_key_exists($fieldStr,$this->collMetadataArr)){
				return $this->collMetadataArr[$fieldStr];
			}
			return '';			
		}
		return $this->collMetadataArr;
	}

	public function validateSecurityKey($k): bool
	{
		if(!$this->collId){
			$sql = 'SELECT collid '.
			'FROM omcollections '.
    		'WHERE securitykey = "'.$k.'"';
			//echo $sql;
			$rs = $this->conn->query($sql);
	    	if($r = $rs->fetch_object()){
	    		$this->setCollId($r->collid);
	    	}
	    	else{
	    		return false;
	    	}
			$rs->free();
		}
		elseif(!isset($this->collMetadataArr['securitykey'])){
			$this->setCollInfo();
		}
		return $k == $this->collMetadataArr['securitykey'];
	}

	public function exportPendingImport($searchVariables): array
	{
		$retArr = array();
		if($this->collId){
			if(!$searchVariables) {
				$searchVariables = 'TOTAL_TRANSFER';
			}
			$fileName = $searchVariables.'_'.$this->collId.'_'.'upload.csv';
			
			header ('Content-Type: text/csv');
			header ('Content-Disposition: attachment; filename="'.$fileName.'"');
			$outstream = fopen('php://output', 'wb');
			$outputHeader = true;

			$sql = $this->getPendingImportSql($searchVariables) ;
			//echo "<div>".$sql."</div>"; exit;
			$rs = $this->conn->query($sql);
			if($rs->num_rows){
				while($r = $rs->fetch_assoc()){
					if($outputHeader){
						fputcsv($outstream,array_keys($r));
						$outputHeader = false;
					}
					fputcsv($outstream,$r);
				}
			}
			else{
				echo "Recordset is empty.\n";
			}
			$rs->free();
			fclose($outstream);
		}
		return $retArr;
	}

	public function getPendingImportData($start, $limit, $searchVariables = ''): array
	{
		$retArr = array();
		if($this->collId){
			$sql = $this->getPendingImportSql($searchVariables) ;
			if($limit) {
				$sql .= 'LIMIT ' . $start . ',' . $limit;
			}
			//echo "<div>".$sql."</div>"; exit;
			$rs = $this->conn->query($sql);
			while($row = $rs->fetch_assoc()){
				$retArr[] = array_change_key_case($row);
			}
			$rs->free();
		}
		return $retArr;
	}

	private function getPendingImportSql($searchVariables): string
	{
		$occFieldArr = array('catalognumber', 'othercatalognumbers', 'occurrenceid','family', 'scientificname', 'sciname',
			'scientificnameauthorship', 'identifiedby', 'dateidentified', 'identificationreferences',
			'identificationremarks', 'taxonremarks', 'identificationqualifier', 'typestatus', 'recordedby', 'recordnumber',
			'associatedcollectors', 'eventdate', 'year', 'month', 'day', 'startdayofyear', 'enddayofyear',
			'verbatimeventdate', 'habitat', 'substrate', 'fieldnumber','occurrenceremarks', 'associatedtaxa', 'verbatimattributes',
			'dynamicproperties', 'reproductivecondition', 'cultivationstatus', 'establishmentmeans',
			'lifestage', 'sex', 'individualcount', 'samplingprotocol', 'preparations',
			'country', 'stateprovince', 'county', 'municipality', 'locality', 'localitysecurity', 'localitysecurityreason',
			'decimallatitude', 'decimallongitude','geodeticdatum', 'coordinateuncertaintyinmeters', 'footprintwkt',
			'locationremarks', 'verbatimcoordinates', 'georeferencedby', 'georeferenceprotocol', 'georeferencesources',
			'georeferenceverificationstatus', 'georeferenceremarks', 'minimumelevationinmeters', 'maximumelevationinmeters',
			'verbatimelevation', 'disposition', 'language', 'duplicatequantity', 'genericcolumn1', 'genericcolumn2',
			'labelproject','basisofrecord','ownerinstitutioncode', 'processingstatus', 'recordenteredby');
		$sql = 'SELECT occid, dbpk, '.implode(',',$occFieldArr).' FROM uploadspectemp '.
				'WHERE collid IN('.$this->collId.') ';
		if($searchVariables){
			if($searchVariables === 'matchappend'){
				$sql = 'SELECT DISTINCT u.occid, u.dbpk, u.'.implode(',u.',$occFieldArr).' '.
						'FROM uploadspectemp u INNER JOIN omoccurrences o ON u.collid = o.collid '.
						'WHERE (u.collid IN('.$this->collId.')) AND (u.occid IS NULL) AND (u.catalogNumber = o.catalogNumber OR u.othercatalogNumbers = o.othercatalogNumbers) ';
			}
			elseif($searchVariables === 'sync'){
				$sql = 'SELECT DISTINCT u.occid, u.dbpk, u.'.implode(',u.',$occFieldArr).' '.
						'FROM uploadspectemp u INNER JOIN omoccurrences o ON (u.catalogNumber = o.catalogNumber) AND (u.collid = o.collid) '.
						'WHERE (u.collid IN('.$this->collId.')) AND (u.occid IS NULL) AND (u.catalogNumber IS NOT NULL) '.
						'AND (o.catalogNumber IS NOT NULL) AND (o.dbpk IS NULL) ';
			}
			elseif($searchVariables === 'exist'){
				$sql = 'SELECT DISTINCT o.occid, o.dbpk, o.'.implode(',o.',$occFieldArr).' '.
						'FROM omoccurrences o LEFT JOIN uploadspectemp u  ON (o.occid = u.occid) '.
						'WHERE (o.collid IN('.$this->collId.')) AND (u.occid IS NULL) ';
			}
			elseif($searchVariables === 'dupdbpk'){
				$sql = 'SELECT DISTINCT u.occid, u.dbpk, u.'.implode(',u.',$occFieldArr).' FROM uploadspectemp u WHERE u.dbpk IN('.
						'SELECT dbpk FROM uploadspectemp '.
						'GROUP BY dbpk, collid, basisofrecord '.
						'HAVING (Count(*)>1) AND (collid IN('.$this->collId.'))) ';
			}
			else{
				$varArr = explode(';',$searchVariables);
				foreach($varArr as $varStr){
					if(strpos($varStr,':')){
						$vArr = explode(':',$varStr);
						$sql .= 'AND '.$vArr[0];
						switch($vArr[1]){
							case 'ISNULL':
								$sql .= ' IS NULL ';
								break;
							case 'ISNOTNULL':
								$sql .= ' IS NOT NULL ';
								break;
							default:
								$sql .= ' = "'.$vArr[1].'" ';
						}
					}
				}
			}
		}
		return $sql;
	}

	public function readUploadParameters(): void
	{
    	if($this->uspid){
			$sql = 'SELECT usp.collid, usp.title, usp.Platform, usp.server, usp.port, usp.Username, usp.Password, usp.SchemaName, '.
	    		'usp.code, usp.path, usp.pkfield, usp.querystr, usp.cleanupsp, cs.uploaddate, usp.uploadtype '.
				'FROM uploadspecparameters usp LEFT JOIN omcollectionstats cs ON usp.collid = cs.collid '.
	    		'WHERE (usp.uspid = '.$this->uspid.')';
			//echo $sql;
			$result = $this->conn->query($sql);
	    	if($row = $result->fetch_object()){
	    		if(!$this->collId) {
					$this->collId = $row->collid;
				}
	    		$this->title = $row->title;
	    		$this->platform = $row->Platform;
	    		$this->server = $row->server;
	    		$this->port = $row->port;
	    		$this->username = $row->Username;
	    		$this->password = $row->Password;
	    		$this->schemaName = $row->SchemaName;
	    		$this->code = $row->code;
	    		if(!$this->path) {
					$this->path = $row->path;
				}
	    		$this->pKField = strtolower($row->pkfield);
	    		$this->queryStr = $row->querystr;
	    		$this->storedProcedure = $row->cleanupsp;
	    		$this->lastUploadDate = $row->uploaddate;
	    		$this->uploadType = (int)$row->uploadtype;
	    		if(!$this->lastUploadDate) {
					$this->lastUploadDate = date('Y-m-d H:i:s');
				}
	    	}
	    	$result->free();
    	}
    }

    public function editUploadProfile($profileArr): bool
	{
    	$sql = 'UPDATE uploadspecparameters SET title = "'.$this->cleanInStr($profileArr['title']).'"'.
			', platform = '.($profileArr['platform']?'"'.$profileArr['platform'].'"':'NULL').
			', server = '.($profileArr['server']?'"'.$profileArr['server'].'"':'NULL').
			', port = '.($profileArr['port']?:'NULL').
			', username = '.($profileArr['username']?'"'.$profileArr['username'].'"':'NULL').
			', password = '.($profileArr['password']?'"'.$profileArr['password'].'"':'NULL').
			', schemaname = '.($profileArr['schemaname']?'"'.$profileArr['schemaname'].'"':'NULL').
			', code = '.($profileArr['code']?'"'.$profileArr['code'].'"':'NULL').
			', path = '.($profileArr['path']?'"'.$profileArr['path'].'"':'NULL').
			', pkfield = '.($profileArr['pkfield']?'"'.$profileArr['pkfield'].'"':'NULL').
			', querystr = '.($profileArr['querystr']?'"'.$this->cleanInStr($profileArr['querystr']).'"':'NULL').
			', cleanupsp = '.($profileArr['cleanupsp']?'"'.$profileArr['cleanupsp'].'"':'NULL').' '.
			'WHERE (uspid = '.$this->uspid.')';
		//echo $sql;
		if(!$this->conn->query($sql)){
			$this->errorStr = '<div>Error Editing Upload Parameters: ' .$this->conn->error."</div><div>$sql</div>";
			return false;
		}
		return true;
	}

    public function createUploadProfile($profileArr){
		$sql = 'INSERT INTO uploadspecparameters(collid, uploadtype, title, platform, server, port, code, path, '.
			'pkfield, username, password, schemaname, cleanupsp, querystr) VALUES ('.$this->collId.','.
			$profileArr['uploadtype'].',"'.$this->cleanInStr($profileArr['title']).'",'.
			(isset($profileArr['platform'])&&$profileArr['platform']?'"'.$this->cleanInStr($profileArr['platform']).'"':'NULL').','.
			(isset($profileArr['server'])&&$profileArr['platform']?'"'.$this->cleanInStr($profileArr['server']).'"':'NULL').','.
			(isset($profileArr['port'])&&is_numeric($profileArr['port'])?$profileArr['port']:'NULL').','.
			(isset($profileArr['code'])&&$profileArr['code']?'"'.$this->cleanInStr($profileArr['code']).'"':'NULL').','.
			(isset($profileArr['path'])&&$profileArr['path']?'"'.$this->cleanInStr($profileArr['path']).'"':'NULL').','.
			(isset($profileArr['pkfield'])&&$profileArr['pkfield']?'"'.$this->cleanInStr($profileArr['pkfield']).'"':'NULL').','.
			(isset($profileArr['username'])&&$profileArr['username']?'"'.$this->cleanInStr($profileArr['username']).'"':'NULL').','.
			(isset($profileArr['password'])&&$profileArr['password']?'"'.$this->cleanInStr($profileArr['password']).'"':'NULL').','.
			(isset($profileArr['schemaname'])&&$profileArr['schemaname']?'"'.$this->cleanInStr($profileArr['schemaname']).'"':'NULL').','.
			(isset($profileArr['cleanupsp'])&&$profileArr['cleanupsp']?'"'.$this->cleanInStr($profileArr['cleanupsp']).'"':'NULL').','.
			(isset($profileArr['querystr'])&&$profileArr['querystr']?'"'.$this->cleanInStr($profileArr['querystr']).'"':'NULL').')';
		//echo $sql;
		if($this->conn->query($sql)){
			return $this->conn->insert_id;
		}

		$this->errorStr = '<div>Error Adding Upload Parameters: '.$this->conn->error.'</div><div style="margin-left:10px;">SQL: '.$sql.'</div>';
		return false;
	}

    public function deleteUploadProfile($uspid): bool
	{
		$sql = 'DELETE FROM uploadspecparameters WHERE (uspid = '.$uspid.')';
		if(!$this->conn->query($sql)){
			$this->errorStr = '<div>Error Adding Upload Parameters: '.$this->conn->error.'</div><div>'.$sql.'</div>';
			return false;
		}
		return true;
	}

	public function getUspid(){
		return $this->uspid;
	}
	
	public function getTitle(): string
	{
		return $this->title;
	}

	public function getPlatform(){
		return $this->platform;
	}

	public function getServer(){
		return $this->server;
	}
	
	public function getPort(){
		return $this->port;
	}
	
	public function getUsername(){
		return $this->username;
	}
	
	public function getPassword(){
		return $this->password;
	}
	
	public function getCode(){
		return $this->code;
	}
	
	public function getPath(){
		return $this->path;
	}

	public function setPath($p): void
	{
		$this->path = $p;
	}

	public function getPKField(){
		return $this->pKField;
	}

	public function getSchemaName(){
		return $this->schemaName;
	}

	public function getQueryStr(){
		return $this->queryStr;
	}

	public function getStoredProcedure(){
		return $this->storedProcedure;
	}
	
	public function getUploadType(){
		return $this->uploadType;
	}
	
	public function setUploadType($uploadType): void
	{
		if(is_numeric($uploadType)){
			$this->uploadType = $uploadType;
		}
	}
	
	public function getErrorStr(){
		return $this->errorStr;
	}
	
	public function setVerboseMode($vMode, $logTitle = ''): void
	{
		if(is_numeric($vMode)){
			$this->verboseMode = $vMode;
			if(($this->verboseMode == 2) && $GLOBALS['SERVER_ROOT']) {
				$GLOBALS['LOG_PATH'] = $GLOBALS['SERVER_ROOT'];
				if(substr($GLOBALS['SERVER_ROOT'],-1) !== '/' && substr($GLOBALS['SERVER_ROOT'],-1) !== '\\') {
                    $GLOBALS['LOG_PATH'] .= '/';
                }
				$GLOBALS['LOG_PATH'] .= 'content/logs/';
				if($logTitle){
					$GLOBALS['LOG_PATH'] .= $logTitle;
				}
				else{
					$GLOBALS['LOG_PATH'] .= 'dataupload';
				}
				$GLOBALS['LOG_PATH'] .= '_'.date('Ymd'). '.log';
				$this->logFH = fopen($GLOBALS['LOG_PATH'], 'ab');
				fwrite($this->logFH, 'Start time: ' .date('Y-m-d h:i:s A')."\n");
			}
		}
	}

	protected function outputMsg($str, $indent = 0): void
	{
		if($this->verboseMode == 1){
			echo $str;
			flush();
		}
		elseif($this->verboseMode == 2){
			if($this->logFH) {
				fwrite($this->logFH, ($indent ? str_repeat("\t", $indent) : '') . strip_tags($str) . "\n");
			}
		}
	}
	
	protected function cleanInStr($inStr){
		$retStr = trim($inStr);
		$retStr = str_replace(array(chr(10), chr(11), chr(13), chr(20), chr(30)), ' ', $retStr);
		$retStr = preg_replace('/\s\s+/', ' ',$retStr);
		$retStr = $this->conn->real_escape_string($retStr);
		return $retStr;
	}
}
