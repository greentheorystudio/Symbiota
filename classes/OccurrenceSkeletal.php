<?php
include_once($SERVER_ROOT.'/classes/DbConnection.php');
include_once($SERVER_ROOT.'/classes/UuidFactory.php');

class OccurrenceSkeletal {

	private $conn;
	private $collid;
	private $collectionMap = array();
	private $stateList;
	private $allowedFields;
	private $occidArr;
	private $errorStr = '';

	public function __construct(){
		$connection = new DbConnection();
		$this->conn = $connection->getConnection();
		$this->stateList = array('AK' => 'Alaska', 'AL' => 'Alabama', 'AZ' => 'Arizona', 'AR' => 'Arkansas', 'CA' => 'California',
			'CO' => 'Colorado', 'CT' => 'Connecticut', 'DE' => 'Delaware', 'DC' => 'District of Columbia', 'FL' => 'Florida',
			'GA' => 'Georgia', 'GU' => 'Guam', 'HI' => 'Hawaii', 'ID' => 'Idaho', 'IL' => 'Illinois', 'IN' => 'Indiana', 'IA' =>
			'Iowa', 'KS' => 'Kansas', 'KY' => 'Kentucky', 'LA' => 'Louisiana', 'ME' => 'Maine', 'MH' => 'Marshall Islands', 'MD' =>
			'Maryland', 'MA' => 'Massachusetts', 'MI' => 'Michigan', 'MN' => 'Minnesota', 'MS' => 'Mississippi', 'MO' => 'Missouri',
			'MT' => 'Montana', 'NE' => 'Nebraska', 'NV' => 'Nevada', 'NH' => 'New Hampshire', 'NJ' => 'New Jersey', 'NM' => 'New Mexico',
			'NY' => 'New York', 'NC' => 'North Carolina', 'ND' => 'North Dakota', 'MP' => 'Northern Mariana Islands', 'OH' => 'Ohio',
			'OK' => 'Oklahoma', 'OR' => 'Oregon', 'PW' => 'Palau', 'PA' => 'Pennsylvania', 'PR' => 'Puerto Rico', 'RI' => 'Rhode Island',
			'SC' => 'South Carolina', 'SD' => 'South Dakota', 'TN' => 'Tennessee', 'TX' => 'Texas', 'UT' => 'Utah', 'VT' => 'Vermont',
			'VI' => 'Virgin Islands', 'VA' => 'Virginia', 'WA' => 'Washington', 'WV' => 'West Virginia', 'WI' => 'Wisconsin', 'WY' =>  'Wyoming');
		$this->allowedFields = array('collid'=>'n','catalognumber'=>'s','othercatalognumbers'=>'s','sciname'=>'s','tidinterpreted'=>'s','family'=>'s',
			'scientificnameauthorship'=>'s','localitysecurity'=>'n','country'=>'s','stateprovince'=>'s','county'=>'s','processingstatus'=>'s',
			'recordedby'=>'s','recordnumber'=>'s','eventdate'=>'d','language'=>'s');
	}

	public function __destruct(){
		if(!($this->conn === null)) {
			$this->conn->close();
		}
	}

	public function addOccurrence($postArr): bool
	{
		global $USERNAME;
		$status = false;
		if($this->collid){
			$postArr = $this->cleanOccurrenceArr($postArr);
			$sql1 = '';
			$sql2 = '';
			foreach($this->allowedFields as $f => $dataType){
				$sql1 .= ','.$f;
				if(array_key_exists($f, $postArr) && ($postArr[$f] || $postArr[$f] === 0)){
					$v = $postArr[$f];
					if($dataType === 'n' && is_numeric($v)){
						$sql2 .= ','.$v;
					}
					else{
						$sql2 .= ',"'.$this->cleanInStr($v).'"';
					}
				}
				else{
					$sql2 .= ',NULL';
				}
			}
			$sql = 'INSERT INTO omoccurrences('.trim($sql1,' ,').',recordenteredby,dateentered) '.
				'VALUES('.trim($sql2,' ,').',"'.$USERNAME.'","'.date('Y-m-d H:i:s').'")';
			//echo $sql;
			if($this->conn->query($sql)){
				$status = true;
				$this->occidArr[] = $this->conn->insert_id;
				$this->conn->query('UPDATE omcollectionstats SET recordcnt = recordcnt + 1 WHERE collid = '.$this->collid);
				$guid = UuidFactory::getUuidV4();
				if(!$this->conn->query('INSERT INTO guidoccurrences(guid,occid) VALUES("'.$guid.'",'.$this->occidArr[0].')')){
					$this->errorStr = '(WARNING: Symbiota GUID mapping failed) ';
				}
			}
			else{
				$status = false;
				$this->errorStr = 'ERROR adding occurrence record: '.$this->conn->error;
			}
		}
		return $status;
	}

	public function updateOccurrence($postArr): bool
	{
		$status = false;
		if($this->occidArr){
			$postArr = $this->cleanOccurrenceArr($postArr);
			$sqlA = '';
			foreach($this->allowedFields as $f => $dataType){
				if(array_key_exists($f, $postArr) && ($postArr[$f] || $postArr[$f] === 0)){
					$sqlA .= ', '.$f.' = IFNULL('.$f.',';
					$v = $postArr[$f];
					if($dataType === 'n' && is_numeric($v)){
						$sqlA .= $v;
					}
					else{
						$sqlA .= '"'.$this->cleanInStr($v).'"';
					}
					$sqlA .= ')';
				}
			}
			if($sqlA){
				$sql = 'UPDATE omoccurrences SET '.trim($sqlA,' ,').' WHERE occid IN('.implode(',',$this->occidArr).')';
				//echo $sql; exit;
				if($this->conn->query($sql)){
					$status = true;
				}
				else{
					$this->errorStr = 'ERROR updating occurrence record: '.$this->conn->error;
				}
			}
		}
		return $status;
	}

	private function cleanOccurrenceArr($postArr){
		if(isset($postArr['stateprovince']) && $postArr['stateprovince'] && strlen($postArr['stateprovince']) === 2){
			$postArr['stateprovince'] = $this->translateStateAbbreviation($postArr['stateprovince']);
		}
		if((!isset($postArr['country']) || !$postArr['country']) && isset($postArr['stateprovince']) && $postArr['stateprovince']){
			$postArr['country'] = $this->getCountry($postArr['stateprovince']);
		}
		return $postArr;
	}

	public function catalogNumberExists($catNum): bool
	{
		$status = false;
		if($this->collid){
			$sql = 'SELECT occid FROM omoccurrences '.
				'WHERE (catalognumber = "'.$this->cleanInStr($catNum).'") AND (collid = '.$this->collid.')';
			//echo $sql;
			$rs = $this->conn->query($sql);
			while ($r = $rs->fetch_object()) {
				$this->occidArr[] = $r->occid;
				$status = true;
			}
			$rs->free();
		}
		return $status;
	}

	private function getCountry($state): string
	{
		$countryStr = '';
		if($state){
			if(in_array(ucwords($state), $this->stateList, true)){
				$countryStr = 'United States';
			}
			else{
				$sql = 'SELECT c.countryname '.
					'FROM lkupstateprovince s INNER JOIN lkupcountry c ON s.countryid = c.countryid '.
					'WHERE s.statename = "'.$state.'"';
				$rs = $this->conn->query($sql);
				if($r = $rs->fetch_object()) {
					$countryStr = $r->countryname;
				}
				$rs->free();
			}
		}
		return $countryStr;
	}

	private function translateStateAbbreviation($abbr){
		$stateStr = '';
		if(array_key_exists($abbr,$this->stateList)){
			$stateStr = $this->stateList[$abbr];
		}
		return $stateStr;
	}

	public function setCollId($id): void
	{
		if($id && is_numeric($id)){
			$this->collid = $id;
			$this->setCollectionMap();
		}
	}

	private function setCollectionMap(): void
	{
		if($this->collid){
			$sql = 'SELECT collid, collectionname, institutioncode, collectioncode, colltype, managementtype '.
				'FROM omcollections '.
				'WHERE (collid = '.$this->collid.')';
			$rs = $this->conn->query($sql);
			if($row = $rs->fetch_object()){
				$this->collectionMap['collectionname'] = $row->collectionname;
				$this->collectionMap['institutioncode'] = $row->institutioncode;
				$this->collectionMap['collectioncode'] = $row->collectioncode;
				$this->collectionMap['colltype'] = $row->colltype;
				$this->collectionMap['managementtype'] = $row->managementtype;
			}
			$rs->free();
		}
	}

	public function getCollectionMap(): array
	{
		return $this->collectionMap;
	}

	public function getLanguageArr(): array
	{
		$retArr = array();
		$sql = 'SELECT iso639_1, langname '.
			'FROM adminlanguages ';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[$r->iso639_1] = $r->langname;
		}
		$rs->free();
		asort($retArr);
		return $retArr;
	}

	public function getOccidArr(){
		return $this->occidArr;
	}

	public function getErrorStr(): string
	{
		return $this->errorStr;
	}

	private function cleanInStr($str){
		$newStr = trim($str);
		$newStr = preg_replace('/\s\s+/', ' ',$newStr);
		$newStr = $this->conn->real_escape_string($newStr);
		return $newStr;
	}
}
