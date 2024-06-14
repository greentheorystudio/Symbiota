<?php
include_once(__DIR__ . '/../services/DbConnectionService.php');
include_once(__DIR__ . '/../services/SanitizerService.php');

class InstitutionManager {

	private $conn;
	private $iid;
	private $collid;
	private $errorStr;
	
	public function __construct(){
		$connection = new DbConnectionService();
		$this->conn = $connection->getConnection();
	}

	public function __destruct(){
		if($this->conn) {
			$this->conn->close();
		}
	}

	public function getInstitutionData(): array
	{
		$retArr = array();
		if($this->iid){
			$sql = 'SELECT iid, institutioncode, institutionname, institutionname2, address1, address2, city, '.
				'stateprovince, postalcode, country, phone, contact, email, url, notes '.
				'FROM institutions '.
				'WHERE iid = '.$this->iid;
			//echo $sql;
			$rs = $this->conn->query($sql);
			while($row = $rs->fetch_assoc()){
				$retArr = SanitizerService::cleanOutArray($row);
			}
			$rs->free();
		}
		return $retArr;
	}

	public function submitInstitutionEdits($postData): bool
	{
		$status = true;
		if($postData['institutioncode'] && $postData['institutionname']){
			$sql = 'UPDATE institutions SET '.
				'institutioncode = '.($postData['institutioncode']?'"'.SanitizerService::cleanInStr($this->conn,$postData['institutioncode']).'"':'NULL').','.
				'institutionname = "'.SanitizerService::cleanInStr($this->conn,$postData['institutionname']).'",'.
				'institutionname2 = '.($postData['institutionname2']?'"'.SanitizerService::cleanInStr($this->conn,$postData['institutionname2']).'"':'NULL').','.
				'address1 = '.($postData['address1']?'"'.SanitizerService::cleanInStr($this->conn,$postData['address1']).'"':'NULL').','.
				'address2 = '.($postData['address2']?'"'.SanitizerService::cleanInStr($this->conn,$postData['address2']).'"':'NULL').','.
				'city = '.($postData['city']?'"'.SanitizerService::cleanInStr($this->conn,$postData['city']).'"':'NULL').','.
				'stateprovince = '.($postData['stateprovince']?'"'.SanitizerService::cleanInStr($this->conn,$postData['stateprovince']).'"':'NULL').','.
				'postalcode = '.($postData['postalcode']?'"'.SanitizerService::cleanInStr($this->conn,$postData['postalcode']).'"':'NULL').','.
				'country = '.($postData['country']?'"'.SanitizerService::cleanInStr($this->conn,$postData['country']).'"':'NULL').','.
				'phone = '.($postData['phone']?'"'.SanitizerService::cleanInStr($this->conn,$postData['phone']).'"':'NULL').','.
				'contact = '.($postData['contact']?'"'.SanitizerService::cleanInStr($this->conn,$postData['contact']).'"':'NULL').','.
				'email = '.($postData['email']?'"'.SanitizerService::cleanInStr($this->conn,$postData['email']).'"':'NULL').','.
				'url = '.($postData['url']?'"'.SanitizerService::cleanInStr($this->conn,$postData['url']).'"':'NULL').','.
				'notes = '.($postData['notes']?'"'.SanitizerService::cleanInStr($this->conn,$postData['notes']).'"':'NULL').' '.
				'WHERE iid = '.$postData['iid'];
			//echo "<div>$sql</div>"; exit;
			if(!$this->conn->query($sql)){
				$status = false;
				$this->errorStr = 'ERROR editing institution.';
			}
		}
		return $status;
	}

	public function submitInstitutionAdd($postData): int
    {
		$newIID = 0;
		$sql = 'INSERT INTO institutions (institutioncode, institutionname, institutionname2, address1, address2, city, '.
			'stateprovince, postalcode, country, phone, contact, email, url, notes) '.
			'VALUES ('.($postData['institutioncode']?'"'.SanitizerService::cleanInStr($this->conn,$postData['institutioncode']).'"':'NULL').',"'.
			SanitizerService::cleanInStr($this->conn,$postData['institutionname']).'",'.
			($postData['institutionname2']?'"'.SanitizerService::cleanInStr($this->conn,$postData['institutionname2']).'"':'NULL').','.
			($postData['address1']?'"'.SanitizerService::cleanInStr($this->conn,$postData['address1']).'"':'NULL').','.
			($postData['address2']?'"'.SanitizerService::cleanInStr($this->conn,$postData['address2']).'"':'NULL').','.
			($postData['city']?'"'.SanitizerService::cleanInStr($this->conn,$postData['city']).'"':'NULL').','.
			($postData['stateprovince']?'"'.SanitizerService::cleanInStr($this->conn,$postData['stateprovince']).'"':'NULL').','.
			($postData['postalcode']?'"'.SanitizerService::cleanInStr($this->conn,$postData['postalcode']).'"':'NULL').','.
			($postData['country']?'"'.SanitizerService::cleanInStr($this->conn,$postData['country']).'"':'NULL').','.
			($postData['phone']?'"'.SanitizerService::cleanInStr($this->conn,$postData['phone']).'"':'NULL').','.
			($postData['contact']?'"'.SanitizerService::cleanInStr($this->conn,$postData['contact']).'"':'NULL').','.
			($postData['email']?'"'.SanitizerService::cleanInStr($this->conn,$postData['email']).'"':'NULL').','.
			($postData['url']?'"'.$postData['url'].'"':'NULL').','.
			($postData['notes']?'"'.SanitizerService::cleanInStr($this->conn,$postData['notes']).'"':'NULL').') ';
		//echo "<div>$sql</div>"; exit;
		if($this->conn->query($sql)){
			$newIID = $this->conn->insert_id;
			if($newIID && $postData['targetcollid']){
				$sql2 = 'UPDATE omcollections SET iid = '.$newIID.' WHERE (iid IS NULL) AND (collid = '.$postData['targetcollid'].')';
				$this->conn->query($sql2);
			}
		}
		else{
			$this->errorStr = 'ERROR creating institution.';
		}
		return $newIID;
	}

	public function deleteInstitution($delIid): bool
	{
		$status = true;
		$sql = 'SELECT collid, CollectionName, InstitutionCode, CollectionCode '.
			'FROM omcollections WHERE iid = '.$delIid.' ORDER BY CollectionName,InstitutionCode,CollectionCode';
		//echo $sql;
		$rs = $this->conn->query($sql);
		if($rs->num_rows){
			$status = false;
            $this->errorStr = 'ERROR deleting institution: Following collections need to be unlinked to institution before deletion is allowed';
			$this->errorStr .= '<ul style="margin-left:20px">';
			while($r = $rs->fetch_object()){
                $code = '';
                $name = $r->CollectionName;
                if($r->InstitutionCode){
                    $code .= $r->InstitutionCode;
                }
                if($r->CollectionCode){
                    $code .= ($code?':':'') . $r->CollectionCode;
                }
                if($code){
                    $name .= ' ' . $code;
                }
                $this->errorStr .= '<li>'.$name.'</li>';
			}
			$this->errorStr .= '</ul><br/>';
		}
		$rs->free();
		if(!$status) {
			return false;
		}
		
		$sql = 'SELECT loanid '.
			'FROM omoccurloans '.
			'WHERE iidOwner = '.$delIid.' OR iidBorrower = '.$delIid;
		$rs = $this->conn->query($sql);
		if($rs->num_rows){
			$status = false;
			$this->errorStr = 'ERROR deleting institution: Institution is linked to '.$rs->num_rows.' loan records';
		}
		$rs->free();

		if($status){
			$sql = 'DELETE FROM institutions WHERE iid = '.$delIid;
			//echo $sql; exit;
			if(!$this->conn->query($sql)){
				$status = false;
				$this->errorStr = 'ERROR deleting institution.';
			}
		}
		return $status;
	}
	
	public function removeCollection($collid): bool
	{
		$status = true;
		$sql = 'UPDATE omcollections SET iid = NULL WHERE collid = '.$collid;
		//echo $sql; exit;
		if(!$this->conn->query($sql)){
			$status = false;
			$this->errorStr = 'ERROR removing collection from institution.';
		}
		return $status;
	}
	
	public function addCollection($collid,$iid): bool
	{
		$status = true;
		if(is_numeric($collid) && is_numeric($iid)){
			$sql = 'UPDATE omcollections SET iid = '.$iid.' WHERE collid = '.$collid;
			//echo $sql; exit;
			if(!$this->conn->query($sql)){
				$status = false;
				$this->errorStr = 'ERROR adding collection to institution.';
			}
		}
		return $status;
	}
	
	public function setInstitutionId($id): void
	{
		if(is_numeric($id)){
			$this->iid = $id;
		}
	}

	public function getErrorStr(){
		return $this->errorStr;
	} 

	public function getInstitutionList(): array
	{
		$retArr = array();
		$sql = 'SELECT i.iid, c.collid, i.institutioncode, i.institutionname '.
			'FROM institutions i LEFT JOIN omcollections c ON i.iid = c.iid '.
			'ORDER BY i.institutionname, i.institutioncode';
		//echo $sql;
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			if(isset($retArr[$r->iid])){
				$collStr = $retArr[$r->iid]['collid'].','.$r->collid;
				$retArr[$r->iid]['collid'] = $collStr;
                $retArr[$r->iid]['institutionname'] = $r->institutionname;
                $retArr[$r->iid]['institutioncode'] = $r->institutioncode;
			}
			else{
				$retArr[$r->iid] = SanitizerService::cleanOutArray($r);
			}
		}
		$rs->free();
		return $retArr;
	}

	public function getCollectionList(): array
	{
		$retArr = array();
		$sql = 'SELECT collid, iid, collectionname, institutioncode, collectioncode '.
			'FROM omcollections '.
			'ORDER BY collectionname,institutioncode';
		//echo $sql; exit;
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$code = '';
            $collName = $r->collectionname;
            if($r->institutioncode){
                $code .= $r->institutioncode;
            }
            if($r->collectioncode){
                $code .= ($code?'-':'') . $r->collectioncode;
            }
            if($code){
                $collName = ' (' . $r->collectionname . ')';
            }
            $retArr[$r->collid]['name'] = $collName;
			$retArr[$r->collid]['iid'] = $r->iid;
		}
		$rs->free();
		return $retArr;
	}
}
