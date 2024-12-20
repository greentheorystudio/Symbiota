<?php
include_once(__DIR__ . '/../services/DbService.php');
include_once(__DIR__ . '/OccurrenceMaintenance.php');
include_once(__DIR__ . '/../services/UuidService.php');
include_once(__DIR__ . '/../services/SanitizerService.php');

class OccurrenceCollectionProfile {

	private $conn;
	private $collid;
	private $errorStr;
    private $organizationKey;
    private $installationKey;
    private $datasetKey;
    private $endpointKey;
    private $idigbioKey;

	public function __construct(){
		$connection = new DbService();
		$this->conn = $connection->getConnection();
	}

	public function __destruct(){
		if($this->conn) {
			$this->conn->close();
		}
	}

	public function setCollid($collid): bool
	{
		if(is_numeric($collid)){
			$this->collid = $collid;
			return true;
		}
		return false;
	}

	public function getCollectionMetadata(): array
	{
		$retArr = array();
        $sql = 'SELECT c.collid, c.institutioncode, c.CollectionCode, c.CollectionName, c.collectionid, '.
			'c.FullDescription, c.Homepage, c.individualurl, c.Contact, c.email, c.datarecordingmethod, c.defaultRepCount, '.
			'c.latitudedecimal, c.longitudedecimal, c.icon, c.colltype, c.managementtype, c.isPublic, '.
			'c.guidtarget, c.rights, c.rightsholder, c.accessrights, c.dwcaurl, c.securitykey, c.collectionguid, s.uploaddate '.
			'FROM omcollections AS c LEFT JOIN omcollectionstats AS s ON c.collid = s.collid ';
		if($this->collid){
			$sql .= 'WHERE c.collid = '.$this->collid.' ';
		}
		else{
            if(!$GLOBALS['IS_ADMIN']){
                $sql .= 'WHERE c.isPublic = 1 ';
                if($GLOBALS['PERMITTED_COLLECTIONS']){
                    $sql .= 'OR c.collid IN('.implode(',', $GLOBALS['PERMITTED_COLLECTIONS']).') ';
                }
            }
            $sql .= 'ORDER BY c.SortSeq, c.CollectionName';
		}
		//echo $sql;
		$rs = $this->conn->query($sql);
		while($row = $rs->fetch_object()){
			if(!$this->collid || $GLOBALS['IS_ADMIN'] || (int)$row->isPublic === 1 || in_array((int)$this->collid, $GLOBALS['PERMITTED_COLLECTIONS'], true)){
                $retArr[$row->collid]['collid'] = $row->collid;
                $retArr[$row->collid]['institutioncode'] = $row->institutioncode;
                $retArr[$row->collid]['collectioncode'] = $row->CollectionCode;
                $retArr[$row->collid]['collectionname'] = $row->CollectionName;
                $retArr[$row->collid]['collectionid'] = $row->collectionid;
                $retArr[$row->collid]['fulldescription'] = $row->FullDescription;
                $retArr[$row->collid]['homepage'] = $row->Homepage;
                $retArr[$row->collid]['individualurl'] = $row->individualurl;
                $retArr[$row->collid]['contact'] = $row->Contact;
                $retArr[$row->collid]['email'] = $row->email;
                $retArr[$row->collid]['latitudedecimal'] = $row->latitudedecimal;
                $retArr[$row->collid]['longitudedecimal'] = $row->longitudedecimal;
                $retArr[$row->collid]['icon'] = ($GLOBALS['CLIENT_ROOT'] && strncmp($row->icon, '/', 1) === 0) ? ($GLOBALS['CLIENT_ROOT'] . $row->icon) : $row->icon;
                $retArr[$row->collid]['colltype'] = $row->colltype;
                $retArr[$row->collid]['managementtype'] = $row->managementtype;
                $retArr[$row->collid]['datarecordingmethod'] = $row->datarecordingmethod;
                $retArr[$row->collid]['defaultRepCount'] = $row->defaultRepCount;
                $retArr[$row->collid]['guidtarget'] = $row->guidtarget;
                $retArr[$row->collid]['rights'] = $row->rights;
                $retArr[$row->collid]['rightsholder'] = $row->rightsholder;
                $retArr[$row->collid]['accessrights'] = $row->accessrights;
                $retArr[$row->collid]['dwcaurl'] = $row->dwcaurl;
                $retArr[$row->collid]['skey'] = $row->securitykey;
                $retArr[$row->collid]['guid'] = $row->collectionguid;
                $retArr[$row->collid]['isPublic'] = $row->isPublic;
                $uDate = '';
                if($row->uploaddate){
                    $uDate = $row->uploaddate;
                    $month = substr($uDate,5,2);
                    $day = substr($uDate,8,2);
                    $year = substr($uDate,0,4);
                    $uDate = date('j F Y',mktime(0,0,0,$month,$day,$year));
                }
                $retArr[$row->collid]['uploaddate'] = $uDate;
            }
		}
		$rs->free();
		if($this->collid){
			if(!$retArr[$this->collid]['guid']){
				$guid= UuidService::getUuidV4();
				$retArr[$this->collid]['guid'] = $guid;
				$sql = 'UPDATE omcollections SET collectionguid = "'.$guid.'" '.
					'WHERE collectionguid IS NULL AND collid = '.$this->collid;
				$this->conn->query($sql);
			}
			if(!$retArr[$this->collid]['skey']){
				$guid2 = UuidService::getUuidV4();
				$retArr[$this->collid]['skey'] = $guid2;
				$sql = 'UPDATE omcollections SET securitykey = "'.$guid2.'" '.
					'WHERE securitykey IS NULL AND collid = '.$this->collid;
				$this->conn->query($sql);
			}
		}
		return $retArr;
	}

	public function getCollectionCategories(): array
	{
		$retArr = array();
		if($this->collid){
			$sql = 'SELECT c.ccpk, c.category FROM omcollcatlink l INNER JOIN omcollcategories c ON l.ccpk = c.ccpk WHERE (l.collid = '.$this->collid.') ';
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$retArr[$r->ccpk] = $r->ccpk;
			}
			$rs->free();
		}
		return $retArr;
	}

	public function submitCollEdits($postArr): string
    {
        $status = 'Edits saved';
		if($this->collid){
			$instCode = SanitizerService::cleanInStr($this->conn,$postArr['institutioncode']);
			$collCode = SanitizerService::cleanInStr($this->conn,$postArr['collectioncode']);
            $collGUID = SanitizerService::cleanInStr($this->conn,$postArr['collectionid']);
			$coleName = SanitizerService::cleanInStr($this->conn,$postArr['collectionname']);
			$fullDesc = SanitizerService::cleanInStr($this->conn,$postArr['fulldescription']);
			$homepage = SanitizerService::cleanInStr($this->conn,$postArr['homepage']);
			$contact = SanitizerService::cleanInStr($this->conn,$postArr['contact']);
			$email = SanitizerService::cleanInStr($this->conn,$postArr['email']);
            $dataRecordingMethod = SanitizerService::cleanInStr($this->conn,$postArr['datarecordingmethod']);
            $defaultRepCount = (array_key_exists('defaultRepCount',$postArr) && (int)$postArr['defaultRepCount'] > 0)?$postArr['defaultRepCount']:'0';
			$gbifPublish = (array_key_exists('publishToGbif',$postArr)?$postArr['publishToGbif']:'NULL');
            $idigPublish = (array_key_exists('publishToIdigbio',$postArr)?$postArr['publishToIdigbio']:'NULL');
			$guidTarget = (array_key_exists('guidtarget',$postArr)?$postArr['guidtarget']:'');
			$rights = SanitizerService::cleanInStr($this->conn,$postArr['rights']);
			$rightsHolder = SanitizerService::cleanInStr($this->conn,$postArr['rightsholder']);
			$accessRights = SanitizerService::cleanInStr($this->conn,$postArr['accessrights']);
            $isPublic = ((array_key_exists('isPublic',$postArr) && (int)$postArr['isPublic'] === 1)?'1':'0');
			if($_FILES['iconfile']['name']){
				$icon = $this->addIconImageFile();
			}
			else{
				$icon = SanitizerService::cleanInStr($this->conn,$postArr['iconurl']);
			}
			$indUrl = SanitizerService::cleanInStr($this->conn,$postArr['individualurl']);

			$sql = 'UPDATE omcollections '.
				'SET institutioncode = '.($instCode?'"'.$instCode.'"':'NULL').','.
				'collectioncode = '.($collCode?'"'.$collCode.'"':'NULL').','.
                'collectionid = '.($collGUID?'"'.$collGUID.'"':'NULL').','.
				'collectionname = "'.$coleName.'",'.
				'fulldescription = '.($fullDesc?'"'.$fullDesc.'"':'NULL').','.
				'homepage = '.($homepage?'"'.$homepage.'"':'NULL').','.
				'contact = '.($contact?'"'.$contact.'"':'NULL').','.
				'email = '.($email?'"'.$email.'"':'NULL').','.
				'latitudedecimal = '.($postArr['latitudedecimal']?:'NULL').','.
				'longitudedecimal = '.($postArr['longitudedecimal']?:'NULL').','.
                'datarecordingmethod = "'.$dataRecordingMethod.'",'.
                'defaultRepCount = "'.$defaultRepCount.'",';
            if(array_key_exists('publishToGbif',$postArr)){
                $sql .= 'publishToGbif = '.$gbifPublish.',';
            }
            if(array_key_exists('publishToIdigbio',$postArr)){
                $sql .= 'publishToIdigbio = '.$idigPublish.',';
            }
            $sql .= 'isPublic = '.$isPublic.','.
                'guidtarget = '.($guidTarget?'"'.$guidTarget.'"':'NULL').','.
				'rights = '.($rights?'"'.$rights.'"':'NULL').','.
				'rightsholder = '.($rightsHolder?'"'.$rightsHolder.'"':'NULL').','.
				'accessrights = '.($accessRights?'"'.$accessRights.'"':'NULL').', '.
				'icon = '.($icon?'"'.$icon.'"':'NULL').', '.
				'individualurl = '.($indUrl?'"'.$indUrl.'"':'NULL').' ';
			if(array_key_exists('colltype',$postArr)){
				$sql .= ',managementtype = "'.$postArr['managementtype'].'",'.
					'colltype = "'.$postArr['colltype'].'" ';
			}
			$sql .= 'WHERE (collid = '.$this->collid.')';
			//echo $sql; exit;
			if($this->conn->query($sql)){
				if(isset($postArr['ccpk']) && $postArr['ccpk']){
                    $rs = $this->conn->query('SELECT ccpk FROM omcollcatlink WHERE collid = '.$this->collid);
                    if($r = $rs->fetch_object()){
                        if(($r->ccpk !== $postArr['ccpk']) && !$this->conn->query('UPDATE omcollcatlink SET ccpk = ' . $postArr['ccpk'] . ' WHERE ccpk = ' . $r->ccpk . ' AND collid = ' . $this->collid)) {
                            $status = 'ERROR updating collection category link.';
                        }
                    }
                    else if(!$this->conn->query('INSERT INTO omcollcatlink (ccpk,collid) VALUES('.$postArr['ccpk'].','.$this->collid.')')){
                        $status = 'ERROR inserting collection category link(1).';
                    }
                }
                else{
                    $this->conn->query('DELETE FROM omcollcatlink WHERE collid = '.$this->collid);
                }
			}
			else{
                $status = 'ERROR updating collection.';
            }
        }
		return $status;
	}

    public function submitCollAdd($postArr): string
    {
		$instCode = SanitizerService::cleanInStr($this->conn,$postArr['institutioncode']);
		$collCode = SanitizerService::cleanInStr($this->conn,$postArr['collectioncode']);
        $collGUID = SanitizerService::cleanInStr($this->conn,$postArr['collectionid']);
		$coleName = SanitizerService::cleanInStr($this->conn,$postArr['collectionname']);
		$fullDesc = SanitizerService::cleanInStr($this->conn,$postArr['fulldescription']);
		$homepage = SanitizerService::cleanInStr($this->conn,$postArr['homepage']);
		$contact = SanitizerService::cleanInStr($this->conn,$postArr['contact']);
		$email = SanitizerService::cleanInStr($this->conn,$postArr['email']);
		$rights = SanitizerService::cleanInStr($this->conn,$postArr['rights']);
		$rightsHolder = SanitizerService::cleanInStr($this->conn,$postArr['rightsholder']);
		$accessRights = SanitizerService::cleanInStr($this->conn,$postArr['accessrights']);
		$gbifPublish = (array_key_exists('publishToGbif',$postArr)?$postArr['publishToGbif']:0);
        $idigPublish = (array_key_exists('publishToIdigbio',$postArr)?$postArr['publishToIdigbio']:0);
        $guidTarget = (array_key_exists('guidtarget',$postArr)?$postArr['guidtarget']:'');
		if($_FILES['iconfile']['name']){
			$icon = $this->addIconImageFile();
		}
		else{
			$icon = array_key_exists('iconurl',$postArr)?SanitizerService::cleanInStr($this->conn,$postArr['iconurl']):'';
		}
		$managementType = array_key_exists('managementtype',$postArr)?SanitizerService::cleanInStr($this->conn,$postArr['managementtype']):'';
        $dataRecordingMethod = array_key_exists('datarecordingmethod',$postArr)?SanitizerService::cleanInStr($this->conn,$postArr['datarecordingmethod']):'';
        $defaultRepCount = (array_key_exists('defaultRepCount',$postArr) && (int)$postArr['defaultRepCount'] > 0)?$postArr['defaultRepCount']:'0';
		$collType = array_key_exists('colltype',$postArr)?SanitizerService::cleanInStr($this->conn,$postArr['colltype']):'';
		$guid = array_key_exists('collectionguid',$postArr)?SanitizerService::cleanInStr($this->conn,$postArr['collectionguid']):'';
		if(!$guid) {
			$guid = UuidService::getUuidV4();
		}
		$indUrl = array_key_exists('individualurl',$postArr)?SanitizerService::cleanInStr($this->conn,$postArr['individualurl']):'';
		$isPublic = ((array_key_exists('isPublic',$postArr) && (int)$postArr['isPublic'] === 1)?'1':'0');

		$sql = 'INSERT INTO omcollections(institutioncode,collectioncode,collectionname,fulldescription,collectionid,homepage,'.
			'contact,email,latitudedecimal,longitudedecimal,publishToGbif,'.
            (array_key_exists('publishToIdigbio',$postArr)?'publishToIdigbio,':'').
            'guidtarget,rights,rightsholder,accessrights,icon,'.
			'managementtype,datarecordingmethod,defaultRepCount,colltype,collectionguid,isPublic,individualurl) '.
			'VALUES ('.($instCode?'"'.$instCode.'"':'NULL').','.
			($collCode?'"'.$collCode.'"':'NULL').','.
            '"'.$coleName.'",'.
			($fullDesc?'"'.$fullDesc.'"':'NULL').','.
            ($collGUID?'"'.$collGUID.'"':'NULL').','.
			($homepage?'"'.$homepage.'"':'NULL').','.
			($contact?'"'.$contact.'"':'NULL').','.
			($email?'"'.$email.'"':'NULL').','.
			($postArr['latitudedecimal']?:'NULL').','.
			($postArr['longitudedecimal']?:'NULL').','.
            $gbifPublish.','.
            (array_key_exists('publishToIdigbio',$postArr)?$idigPublish.',':'').
			($guidTarget?'"'.$guidTarget.'"':'NULL').','.
			($rights?'"'.$rights.'"':'NULL').','.
			($rightsHolder?'"'.$rightsHolder.'"':'NULL').','.
			($accessRights?'"'.$accessRights.'"':'NULL').','.
			($icon?'"'.$icon.'"':'NULL').','.
			($managementType?'"'.$managementType.'"':'"Snapshot"').','.
            ($dataRecordingMethod?'"'.$dataRecordingMethod.'"':'"specimen"').','.
            ($defaultRepCount?'"'.$defaultRepCount.'"':'NULL').','.
			($collType?'"'.$collType.'"':'PreservedSpecimen').','.
			'"'.$guid.'",'.
            $isPublic.','.
            ($indUrl?'"'.$indUrl.'"':'NULL').') ';
		//echo "<div>$sql</div>";
		if($this->conn->query($sql)){
			$cid = $this->conn->insert_id;
			$sql = 'INSERT INTO omcollectionstats(collid,recordcnt,uploadedby) '.
				'VALUES('.$cid.',0,"'.$GLOBALS['SYMB_UID'].'")';
			$this->conn->query($sql);
			if(isset($postArr['ccpk']) && $postArr['ccpk']){
				$sql = 'INSERT INTO omcollcatlink (ccpk,collid) VALUES('.$postArr['ccpk'].','.$cid.')';
				if(!$this->conn->query($sql)){
					return 'ERROR inserting collection category link(2).';
				}
			}
			$this->collid = $cid;
		}
		else{
			$cid = 'ERROR inserting new collection.';
		}
		$this->conn->close();
		return $cid;
	}

	private function addIconImageFile(): string
	{
		$targetPath = $GLOBALS['SERVER_ROOT'].'/content/collicon/';
		$urlBase = '/content/collicon/';
		$fileName = basename($_FILES['iconfile']['name']);
		$imgExt = '';
		if($p = strrpos($fileName, '.')) {
			$imgExt = strtolower(substr($fileName, $p));
		}
		$fileName = str_replace(array('%20', '%23', ' ', '__'), '_',$fileName);
		if(strlen($fileName) > 30) {
			$fileName = substr($fileName, 0, 30);
		}
		$fileName .= $imgExt;

		$fullUrl = '';
		if(is_writable($targetPath) && move_uploaded_file($_FILES['iconfile']['tmp_name'], $targetPath . $fileName)) {
            $fullUrl = $urlBase . $fileName;
        }

		return $fullUrl;
	}

	public function getAddress(): array
	{
		$retArr = array();
		if($this->collid){
			$sql = 'SELECT i.iid, i.institutioncode, i.institutionname, i.institutionname2, i.address1, i.address2, '.
					'i.city, i.stateprovince, i.postalcode, i.country, i.phone, i.contact, i.email, i.url, i.notes '.
					'FROM institutions i INNER JOIN omcollections c ON i.iid = c.iid '.
					'WHERE (c.collid = '.$this->collid. ') ';
			//echo $sql;
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$retArr['iid'] = $r->iid;
				$retArr['institutioncode'] = $r->institutioncode;
				$retArr['institutionname'] = $r->institutionname;
				$retArr['institutionname2'] = $r->institutionname2;
				$retArr['address1'] = $r->address1;
				$retArr['address2'] = $r->address2;
				$retArr['city'] = $r->city;
				$retArr['stateprovince'] = $r->stateprovince;
				$retArr['postalcode'] = $r->postalcode;
				$retArr['country'] = $r->country;
				$retArr['phone'] = $r->phone;
				$retArr['contact'] = $r->contact;
				$retArr['email'] = $r->email;
				$retArr['url'] = $r->url;
				$retArr['notes'] = $r->notes;
			}
			$rs->free();
		}
		return $retArr;
	}

	public function linkAddress($addIID): bool
	{
		$status = false;
		if($this->collid && is_numeric($addIID)){
			$sql = 'UPDATE omcollections SET iid = '.$addIID.' WHERE collid = '.$this->collid;
			if($this->conn->query($sql)){
				$status = true;
			}
			else{
				$this->errorStr = 'ERROR linking institution address.';
			}
			$this->conn->close();
		}
		return $status;
	}

	public function removeAddress($removeIID): bool
	{
		$status = false;
		if($this->collid && is_numeric($removeIID)){
			$sql = 'UPDATE omcollections SET iid = NULL '.
				'WHERE collid = '.$this->collid.' AND iid = '.$removeIID;
			if($this->conn->query($sql)){
				$status = true;
			}
			else{
				$this->errorStr = 'ERROR removing institution address.';
			}
			$this->conn->close();
		}
		return $status;
	}

	public function triggerGBIFCrawl($datasetKey): void
	{
        $loginStr = $GLOBALS['GBIF_USERNAME'].':'.$GLOBALS['GBIF_PASSWORD'];
        $url = 'http://api.gbif.org/v1/dataset/'.$datasetKey.'/crawl';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $loginStr);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Accept: application/json')
        );

        curl_exec($ch);
    }

    public function batchTriggerGBIFCrawl($collIdArr): void
	{
        $collIdStr = implode(',',$collIdArr);
        $sql = 'SELECT CollID, publishToGbif, aggKeysStr '.
            'FROM omcollections '.
            'WHERE CollID IN('.$collIdStr.') ';
        //echo $sql; exit;
        $rs = $this->conn->query($sql);
        while($row = $rs->fetch_object()){
            $publishGBIF = $row->publishToGbif;
            $gbifKeyArr = $row->aggKeysStr;
            if($publishGBIF && $gbifKeyArr){
                $gbifKeyArr = json_decode($gbifKeyArr, true);
                if($gbifKeyArr['endpointKey']){
                    $this->triggerGBIFCrawl($gbifKeyArr['datasetKey']);
                }
            }
        }
        $rs->free();
    }

    public function setAggKeys($aggKeyStr): void
	{
        $aggKeyArr = json_decode($aggKeyStr, true);
        if($aggKeyArr['organizationKey']){
            $this->organizationKey = $aggKeyArr['organizationKey'];
        }
        if($aggKeyArr['installationKey']){
            $this->installationKey = $aggKeyArr['installationKey'];
        }
        if($aggKeyArr['datasetKey']){
            $this->datasetKey = $aggKeyArr['datasetKey'];
        }
        if($aggKeyArr['endpointKey']){
            $this->endpointKey = $aggKeyArr['endpointKey'];
        }
        if($aggKeyArr['idigbioKey']){
            $this->idigbioKey = $aggKeyArr['idigbioKey'];
        }
    }

    public function updateAggKeys($collId){
        $aggKeyArr = array();
        $status = true;
        $aggKeyArr['organizationKey'] = $this->organizationKey;
        $aggKeyArr['installationKey'] = $this->installationKey;
        $aggKeyArr['datasetKey'] = $this->datasetKey;
        $aggKeyArr['endpointKey'] = $this->endpointKey;
        $aggKeyArr['idigbioKey'] = $this->idigbioKey;
        $aggKeyStr = json_encode($aggKeyArr);
        $sql = 'UPDATE omcollections '.
            "SET aggKeysStr = '".$aggKeyStr."' ".
            'WHERE (collid = '.$collId.')';
        //echo $sql; exit;
        if(!$this->conn->query($sql)){
            $status = 'ERROR saving key.';
            return $status;
        }

		$this->conn->close();
		return $status;
	}

    public function getInstallationKey(){
        return $this->installationKey;
    }

    public function getDatasetKey(){
        return $this->datasetKey;
    }

    public function getEndpointKey(){
        return $this->endpointKey;
    }

    public function getIdigbioKey(){
        return $this->idigbioKey;
    }

    public function getCollPubArr($collId): array
	{
        $returnArr = array();
        $aggKeyStr = '';
        $sql = 'SELECT CollID, publishToGbif, publishToIdigbio, aggKeysStr, collectionguid '.
            'FROM omcollections '.
            'WHERE CollID IN('.$collId.') ';
        //echo $sql; exit;
        $rs = $this->conn->query($sql);
        while($row = $rs->fetch_object()){
            $returnArr[$row->CollID]['publishToGbif'] = $row->publishToGbif;
            $returnArr[$row->CollID]['publishToIdigbio'] = $row->publishToIdigbio;
            $returnArr[$row->CollID]['collectionguid'] = $row->collectionguid;
            $aggKeyStr = $row->aggKeysStr;
        }
        $rs->free();

        if($aggKeyStr){
            $this->setAggKeys($aggKeyStr);
        }

        return $returnArr;
    }

    public function getGbifInstKey(): string
	{
        $sql = 'SELECT aggKeysStr '.
            'FROM omcollections '.
            'WHERE aggKeysStr IS NOT NULL ';
        //echo $sql; exit;
        $rs = $this->conn->query($sql);
        while($row = $rs->fetch_object()){
            $returnArr = json_decode($row->aggKeysStr, true);
            if($returnArr['installationKey']){
                return $returnArr['installationKey'];
            }
        }
        $rs->free();

        return '';
    }

    public function getGeographyStats($country,$state): array
	{
		$retArr = array();
		if($state){
			$sql = 'SELECT o.county as termstr, Count(*) AS cnt '.
				'FROM omoccurrences o '.
				'WHERE (o.CollID = '.$this->collid.') '.($country?'AND (o.country = "'.SanitizerService::cleanInStr($this->conn,$country).'") ':'').
				'AND (o.stateprovince = "'.SanitizerService::cleanInStr($this->conn,$state).'") AND (o.county IS NOT NULL) '.
				'GROUP BY o.StateProvince, o.county';
		}
		elseif($country){
			$sql = 'SELECT o.stateprovince as termstr, Count(*) AS cnt '.
				'FROM omoccurrences o '.
				'WHERE (o.CollID = '.$this->collid.') AND (o.StateProvince IS NOT NULL) AND (o.country = "'.SanitizerService::cleanInStr($this->conn,$country).'") '.
				'GROUP BY o.StateProvince, o.country';
		}
		else{
			$sql = 'SELECT o.country as termstr, Count(*) AS cnt '.
				'FROM omoccurrences o '.
				'WHERE (o.CollID = '.$this->collid.') AND (o.Country IS NOT NULL) '.
				'GROUP BY o.Country ';
		}
		//echo $sql; exit;
		$rs = $this->conn->query($sql);
		while($row = $rs->fetch_object()){
			$t = $row->termstr;
			$cnt = $row->cnt;
			if($state){
				$t = trim(str_ireplace(array(' county',' co.',' counties'),'',$t));
				if(array_key_exists($t, $retArr)) {
					$cnt += $retArr[$t];
				}
			}
			if($t) {
				$retArr[$t] = $cnt;
			}
		}
		$rs->free();
		ksort($retArr);
		return $retArr;
	}

	public function getTaxonomyStats(): array
	{
		$retArr = array();
		$sql = 'SELECT family, count(*) as cnt FROM omoccurrences o  WHERE o.family IS NOT NULL AND collid = '.$this->collid.' GROUP BY family';
		//echo $sql; exit;
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[ucwords($r->family)] = $r->cnt;
		}
		$rs->free();
		return $retArr;
	}

	public function getBasicStats(): array
	{
		$retArr = array();
		if($this->collid){
			$sql = 'SELECT uploaddate, recordcnt, georefcnt, familycnt, genuscnt, speciescnt, dynamicProperties FROM omcollectionstats WHERE collid = '.$this->collid;
			$rs = $this->conn->query($sql);
			if($row = $rs->fetch_object()){
				$uDate = '';
				if($row->uploaddate){
					$uDate = $row->uploaddate;
					$month = substr($uDate,5,2);
					$day = substr($uDate,8,2);
					$year = substr($uDate,0,4);
					$uDate = date('j F Y',mktime(0,0,0,$month,$day,$year));
				}
				$retArr['uploaddate'] = $uDate;
				$retArr['recordcnt'] = $row->recordcnt;
				$retArr['georefcnt'] = $row->georefcnt;
				$retArr['familycnt'] = $row->familycnt;
				$retArr['genuscnt'] = $row->genuscnt;
				$retArr['speciescnt'] = $row->speciescnt;
				$retArr['dynamicProperties'] = $row->dynamicProperties;
			}
			$rs->free();
		}
		return $retArr;
	}

	public function updateStatistics($verbose = null): void
	{
		$occurMaintenance = new OccurrenceMaintenance();
		if($verbose){
			echo '<ul>';
			$occurMaintenance->setVerbose(true);
			echo '<li>General cleaning in preparation for collecting stats...</li>';
			flush();
		}
		$occurMaintenance->generalOccurrenceCleaning($this->collid);
		if($verbose){
			echo '<li>Updating statistics...</li>';
			flush();
		}
		$occurMaintenance->updateCollectionStats($this->collid, true);
		if($verbose){
			echo '<li>Finished updating collection statistics</li>';
			flush();
		}
	}

	public function getStatCollectionList($catId = null): array
	{
		$sql = 'SELECT c.collid, c.institutioncode, c.collectioncode, c.collectionname, c.icon, c.colltype, c.ccpk, '.
			'cat.category, cat.icon AS caticon, cat.acronym '.
			'FROM omcollections c LEFT JOIN omcollcategories cat ON c.ccpk = cat.ccpk '.
			'ORDER BY cat.category, c.CollectionName ';
		//echo "<div>SQL: ".$sql."</div>";
		$result = $this->conn->query($sql);
		$collArr = array();
		while($r = $result->fetch_object()){
			$collType = '';
			if(stripos($r->colltype, 'observation') !== false) {
				$collType = 'obs';
			}
			if(stripos($r->colltype, 'specimen')) {
				$collType = 'spec';
			}
			if($collType){
				if($r->ccpk){
					if(!isset($collArr[$collType]['cat'][$r->ccpk]['name'])){
						$collArr[$collType]['cat'][$r->ccpk]['name'] = $r->category;
						$collArr[$collType]['cat'][$r->ccpk]['icon'] = $r->caticon;
						$collArr[$collType]['cat'][$r->ccpk]['acronym'] = $r->acronym;
					}
					$collArr[$collType]['cat'][$r->ccpk][$r->collid]['instcode'] = $r->institutioncode;
					$collArr[$collType]['cat'][$r->ccpk][$r->collid]['collcode'] = $r->collectioncode;
					$collArr[$collType]['cat'][$r->ccpk][$r->collid]['collname'] = $r->collectionname;
					$collArr[$collType]['cat'][$r->ccpk][$r->collid]['icon'] = ($GLOBALS['CLIENT_ROOT'] && strncmp($r->icon, '/', 1) === 0) ? ($GLOBALS['CLIENT_ROOT'] . $r->icon) : $r->icon;
				}
				else{
					$collArr[$collType]['coll'][$r->collid]['instcode'] = $r->institutioncode;
					$collArr[$collType]['coll'][$r->collid]['collcode'] = $r->collectioncode;
					$collArr[$collType]['coll'][$r->collid]['collname'] = $r->collectionname;
					$collArr[$collType]['coll'][$r->collid]['icon'] = ($GLOBALS['CLIENT_ROOT'] && strncmp($r->icon, '/', 1) === 0) ? ($GLOBALS['CLIENT_ROOT'] . $r->icon) : $r->icon;
				}
			}
		}
		$result->free();

		$retArr = array();
		if(isset($collArr['spec']['cat'][$catId])){
			$retArr['spec']['cat'][$catId] = $collArr['spec']['cat'][$catId];
			unset($collArr['spec']['cat'][$catId]);
		}
		elseif(isset($collArr['obs']['cat'][$catId])){
			$retArr['obs']['cat'][$catId] = $collArr['obs']['cat'][$catId];
			unset($collArr['obs']['cat'][$catId]);
		}
		foreach($collArr as $t => $tArr){
			foreach($tArr as $g => $gArr){
				foreach($gArr as $id => $idArr){
					$retArr[$t][$g][$id] = $idArr;
				}
			}
		}
		return $retArr;
	}

	public function batchUpdateStatistics($collId): void
	{
		echo 'Updating collection statistics...';
		echo '<ul>';
		flush();
		$occurMaintenance = new OccurrenceMaintenance();
		$sql = 'SELECT collid, collectionname FROM omcollections WHERE collid IN('.$collId.') ';
		//echo $sql;
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			echo '<li style="margin-left:15px;">Cleaning statistics for: '.$r->collectionname.'</li>';
			flush();
			$occurMaintenance->updateCollectionStats($r->collid, true);
		}
		$rs->free();
		echo '<li>Statistics update complete!</li>';
		echo '</ul>';
		flush();
	}

	public function runStatistics($collId): array
	{
		$returnArr = array();
		$sql = 'SELECT c.collid, c.CollectionName, IFNULL(cs.recordcnt,0) AS recordcnt, IFNULL(cs.georefcnt,0) AS georefcnt, ' .
			'cs.dynamicProperties ' .
			'FROM omcollections AS c INNER JOIN omcollectionstats AS cs ON c.collid = cs.collid ' .
			'WHERE c.collid IN(' .$collId. ') ';
		//echo $sql;
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$returnArr[$r->CollectionName]['collid'] = $r->collid;
			$returnArr[$r->CollectionName]['CollectionName'] = $r->CollectionName;
			$returnArr[$r->CollectionName]['recordcnt'] = $r->recordcnt;
			$returnArr[$r->CollectionName]['georefcnt'] = $r->georefcnt;
			$returnArr[$r->CollectionName]['dynamicProperties'] = $r->dynamicProperties;
		}
		$sql2 = 'SELECT c.CollectionName, COUNT(DISTINCT o.family) AS FamilyCount, '.
			'COUNT(DISTINCT CASE WHEN t.RankId >= 180 THEN t.UnitName1 ELSE NULL END) AS GeneraCount, '.
			'COUNT(DISTINCT CASE WHEN t.RankId = 220 THEN t.SciName ELSE NULL END) AS SpeciesCount, '.
			'COUNT(DISTINCT CASE WHEN t.RankId >= 220 THEN t.SciName ELSE NULL END) AS TotalTaxaCount, '.
			'COUNT(DISTINCT i.occid) AS OccurrenceImageCount '.
			'FROM omoccurrences AS o LEFT JOIN taxa AS t ON o.tid = t.TID '.
			'INNER JOIN omcollections AS c ON o.collid = c.CollID '.
			'LEFT JOIN images AS i ON o.occid = i.occid '.
			'WHERE c.CollID IN('.$collId.') '.
			'GROUP BY c.CollectionName ';
		//echo $sql2;
		$rs = $this->conn->query($sql2);
		while($r = $rs->fetch_object()){
			$returnArr[$r->CollectionName]['familycnt'] = $r->FamilyCount;
			$returnArr[$r->CollectionName]['genuscnt'] = $r->GeneraCount;
			$returnArr[$r->CollectionName]['speciescnt'] = $r->SpeciesCount;
			$returnArr[$r->CollectionName]['TotalTaxaCount'] = $r->TotalTaxaCount;
			$returnArr[$r->CollectionName]['OccurrenceImageCount'] = $r->OccurrenceImageCount;
		}
		$sql3 = 'SELECT COUNT(DISTINCT o.family) AS FamilyCount, '.
			'COUNT(DISTINCT CASE WHEN t.RankId >= 180 THEN t.UnitName1 ELSE NULL END) AS GeneraCount, '.
			'COUNT(DISTINCT CASE WHEN t.RankId = 220 THEN t.SciName ELSE NULL END) AS SpeciesCount, '.
			'COUNT(DISTINCT CASE WHEN t.RankId >= 220 THEN t.SciName ELSE NULL END) AS TotalTaxaCount, '.
            'COUNT(DISTINCT CASE WHEN i.occid IS NOT NULL THEN i.occid ELSE NULL END) AS TotalImageCount '.
			'FROM omoccurrences AS o LEFT JOIN taxa AS t ON o.tid = t.TID '.
			'LEFT JOIN images AS i ON o.occid = i.occid '.
			'WHERE o.collid IN('.$collId.') ';
		//echo $sql3;
		$rs = $this->conn->query($sql3);
		while($r = $rs->fetch_object()){
			$returnArr['familycnt'] = $r->FamilyCount;
			$returnArr['genuscnt'] = $r->GeneraCount;
			$returnArr['speciescnt'] = $r->SpeciesCount;
			$returnArr['TotalTaxaCount'] = $r->TotalTaxaCount;
			$returnArr['TotalImageCount'] = $r->TotalImageCount;
		}
		$rs->free();

		return $returnArr;
	}

    public function runStatisticsQuery($collId,$taxon,$country): array
	{
        $returnArr = array();
        $pTID = '';
        $sqlFrom = 'FROM omoccurrences AS o LEFT JOIN taxa AS t ON o.tid = t.TID '.
            'LEFT JOIN omcollections AS c ON o.collid = c.CollID ';
        $sqlWhere = 'WHERE o.collid IN('.$collId.') ';
        if($taxon){
            $sql = 'SELECT TID FROM taxa WHERE SciName = "'.$taxon.'" ';
            $rs = $this->conn->query($sql);
            while($r = $rs->fetch_object()){
                $pTID = $r->TID;
            }
            $sqlWhere .= 'AND (o.sciname = "'.$taxon.'" OR (o.tid IN(SELECT DISTINCT tid FROM taxaenumtree WHERE parenttid IN('.$pTID.')))) ';
        }
        if($country){
            $sqlWhere .= 'AND o.country = "'.$country.'" ';
        }
        $sql2 = 'SELECT c.CollID, c.CollectionName, COUNT(DISTINCT o.occid) AS SpecimenCount, COUNT(o.decimalLatitude) AS GeorefCount, '.
            'COUNT(DISTINCT o.family) AS FamilyCount, COUNT(DISTINCT t.UnitName1) AS GeneraCount, COUNT(o.typeStatus) AS TypeCount, '.
            'COUNT(CASE WHEN t.RankId >= 220 THEN o.occid ELSE NULL END) AS SpecimensCountID, '.
            'COUNT(DISTINCT CASE WHEN t.RankId = 220 THEN t.SciName ELSE NULL END) AS SpeciesCount, '.
            'COUNT(DISTINCT CASE WHEN t.RankId >= 220 THEN t.SciName ELSE NULL END) AS TotalTaxaCount, '.
            'COUNT(CASE WHEN ISNULL(o.family) THEN o.occid ELSE NULL END) AS SpecimensNullFamily, '.
            'COUNT(CASE WHEN ISNULL(o.country) THEN o.occid ELSE NULL END) AS SpecimensNullCountry, '.
            'COUNT(CASE WHEN ISNULL(o.decimalLatitude) THEN o.occid ELSE NULL END) AS SpecimensNullLatitude ';
        $sql2 .= $sqlFrom.$sqlWhere;
        $sql2 .= 'GROUP BY c.CollectionName ';
        //echo 'sql2: '.$sql2;
        $rs = $this->conn->query($sql2);
        while($r = $rs->fetch_object()){
            $returnArr[$r->CollectionName]['CollID'] = $r->CollID;
            $returnArr[$r->CollectionName]['CollectionName'] = $r->CollectionName;
            $returnArr[$r->CollectionName]['recordcnt'] = $r->SpecimenCount;
            $returnArr[$r->CollectionName]['georefcnt'] = $r->GeorefCount;
            $returnArr[$r->CollectionName]['speciesID'] = $r->SpecimensCountID;
            $returnArr[$r->CollectionName]['familycnt'] = $r->FamilyCount;
            $returnArr[$r->CollectionName]['genuscnt'] = $r->GeneraCount;
            $returnArr[$r->CollectionName]['speciescnt'] = $r->SpeciesCount;
            $returnArr[$r->CollectionName]['TotalTaxaCount'] = $r->TotalTaxaCount;
            $returnArr[$r->CollectionName]['types'] = $r->TypeCount;
            $returnArr[$r->CollectionName]['SpecimensNullFamily'] = $r->SpecimensNullFamily;
            $returnArr[$r->CollectionName]['SpecimensNullCountry'] = $r->SpecimensNullCountry;
            $returnArr[$r->CollectionName]['SpecimensNullLatitude'] = $r->SpecimensNullLatitude;
        }
        $sql3 = 'SELECT o.family, COUNT(o.occid) AS SpecimensPerFamily, COUNT(o.decimalLatitude) AS GeorefSpecimensPerFamily, '.
            'COUNT(CASE WHEN t.RankId >= 220 THEN o.occid ELSE NULL END) AS IDSpecimensPerFamily, '.
            'COUNT(CASE WHEN t.RankId >= 220 AND o.decimalLatitude IS NOT NULL THEN o.occid ELSE NULL END) AS IDGeorefSpecimensPerFamily ';
        $sql3 .= $sqlFrom.$sqlWhere;
        $sql3 .= 'GROUP BY o.family ';
        //echo 'sql3: '.$sql3;
        $rs = $this->conn->query($sql3);
        while($r = $rs->fetch_object()){
            if($r->family){
                $returnArr['families'][$r->family]['SpecimensPerFamily'] = $r->SpecimensPerFamily;
                $returnArr['families'][$r->family]['GeorefSpecimensPerFamily'] = $r->GeorefSpecimensPerFamily;
                $returnArr['families'][$r->family]['IDSpecimensPerFamily'] = $r->IDSpecimensPerFamily;
                $returnArr['families'][$r->family]['IDGeorefSpecimensPerFamily'] = $r->IDGeorefSpecimensPerFamily;
            }
        }
        $sql4 = 'SELECT o.country, COUNT(o.occid) AS CountryCount, COUNT(o.decimalLatitude) AS GeorefSpecimensPerCountry, '.
            'COUNT(CASE WHEN t.RankId >= 220 THEN o.occid ELSE NULL END) AS IDSpecimensPerCountry, '.
            'COUNT(CASE WHEN t.RankId >= 220 AND o.decimalLatitude IS NOT NULL THEN o.occid ELSE NULL END) AS IDGeorefSpecimensPerCountry ';
        $sql4 .= $sqlFrom.$sqlWhere;
        $sql4 .= 'GROUP BY o.country ';
        //echo 'sql4: '.$sql4;
        $rs = $this->conn->query($sql4);
        while($r = $rs->fetch_object()){
            if($r->country){
                $returnArr['countries'][$r->country]['CountryCount'] = $r->CountryCount;
                $returnArr['countries'][$r->country]['GeorefSpecimensPerCountry'] = $r->GeorefSpecimensPerCountry;
                $returnArr['countries'][$r->country]['IDSpecimensPerCountry'] = $r->IDSpecimensPerCountry;
                $returnArr['countries'][$r->country]['IDGeorefSpecimensPerCountry'] = $r->IDGeorefSpecimensPerCountry;
            }
        }
        $sql5 = 'SELECT c.CollID, c.CollectionName, '.
            'COUNT(DISTINCT CASE WHEN i.occid IS NOT NULL THEN i.occid ELSE NULL END) AS TotalImageCount ';
        $sql5 .= $sqlFrom;
        $sql5 .= 'LEFT JOIN images AS i ON o.occid = i.occid ';
        $sql5 .= $sqlWhere;
        $sql5 .= 'GROUP BY c.CollectionName ';
        //echo 'sql5: '.$sql5;
        $rs = $this->conn->query($sql5);
        while($r = $rs->fetch_object()){
            $returnArr[$r->CollectionName]['OccurrenceImageCount'] = $r->TotalImageCount;
        }
        $rs->free();

        return $returnArr;
    }

	public function getYearStatsHeaderArr($months): array
	{
		$dateArr = array();
		$a = $months + 1;
        $reps = $a;
		for ($i = 0; $i < $reps; $i++) {
			$timestamp = mktime(0, 0, 0, date('n') - $i, 1);
			$dateArr[$a] = date('Y', $timestamp).'-'.date('n', $timestamp);
			$a--;
		}
		ksort($dateArr);

		return $dateArr;
	}

	public function getYearStatsDataArr($collId,$days): array
	{
		$statArr = array();
		$sql = 'SELECT c.collid, c.collectionname '.
			'FROM omoccurrences AS o INNER JOIN omcollections AS c ON o.collid = c.collid '.
			'LEFT JOIN images AS i ON o.occid = i.occid '.
			'WHERE o.collid in('.$collId.') AND ((o.dateLastModified IS NOT NULL AND datediff(curdate(), o.dateLastModified) < '.$days.') OR (datediff(curdate(), i.InitialTimeStamp) < '.$days.')) '.
			'ORDER BY c.collectionname ';
		//echo $sql;
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$statArr[$r->collid]['collectionname'] = $r->collectionname;
		}

		$sql = 'SELECT c.collid, CONCAT_WS("-",year(o.dateEntered),month(o.dateEntered)) as dateEntered, '.
			'c.collectionname, month(o.dateEntered) as monthEntered, year(o.dateEntered) as yearEntered, COUNT(o.occid) AS speccnt '.
			'FROM omoccurrences AS o INNER JOIN omcollections AS c ON o.collid = c.collid '.
			'WHERE o.collid in('.$collId.') AND o.dateEntered IS NOT NULL AND datediff(curdate(), o.dateEntered) < '.$days.' '.
			'GROUP BY yearEntered,monthEntered,o.collid ORDER BY c.collectionname ';
		//echo $sql;
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$statArr[$r->collid]['stats'][$r->dateEntered]['speccnt'] = $r->speccnt;
		}

		$sql = 'SELECT c.collid, CONCAT_WS("-",year(o.dateLastModified),month(o.dateLastModified)) as dateEntered, '.
			'c.collectionname, month(o.dateLastModified) as monthEntered, year(o.dateLastModified) as yearEntered, '.
			'COUNT(CASE WHEN o.processingstatus = "unprocessed" THEN o.occid ELSE NULL END) AS unprocessedCount, '.
			'COUNT(CASE WHEN o.processingstatus = "stage 1" THEN o.occid ELSE NULL END) AS stage1Count, '.
			'COUNT(CASE WHEN o.processingstatus = "stage 2" THEN o.occid ELSE NULL END) AS stage2Count, '.
			'COUNT(CASE WHEN o.processingstatus = "stage 3" THEN o.occid ELSE NULL END) AS stage3Count '.
			'FROM omoccurrences AS o INNER JOIN omcollections AS c ON o.collid = c.collid '.
			'WHERE o.collid in('.$collId.') AND o.dateLastModified IS NOT NULL AND datediff(curdate(), o.dateLastModified) < '.$days.' '.
			'GROUP BY yearEntered,monthEntered,o.collid ORDER BY c.collectionname ';
		//echo $sql;
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$statArr[$r->collid]['stats'][$r->dateEntered]['unprocessedCount'] = $r->unprocessedCount;
			$statArr[$r->collid]['stats'][$r->dateEntered]['stage1Count'] = $r->stage1Count;
			$statArr[$r->collid]['stats'][$r->dateEntered]['stage2Count'] = $r->stage2Count;
			$statArr[$r->collid]['stats'][$r->dateEntered]['stage3Count'] = $r->stage3Count;
		}

		$sql2 = 'SELECT c.collid, CONCAT_WS("-",year(i.InitialTimeStamp),month(i.InitialTimeStamp)) as dateEntered, '.
			'c.collectionname, month(i.InitialTimeStamp) as monthEntered, year(i.InitialTimeStamp) as yearEntered, '.
			'COUNT(i.imgid) AS imgcnt '.
			'FROM omoccurrences AS o INNER JOIN omcollections AS c ON o.collid = c.collid '.
			'LEFT JOIN images AS i ON o.occid = i.occid '.
			'WHERE o.collid in('.$collId.') AND datediff(curdate(), i.InitialTimeStamp) < '.$days.' '.
			'GROUP BY yearEntered,monthEntered,o.collid ORDER BY c.collectionname ';
		//echo $sql2;
		$rs = $this->conn->query($sql2);
		while($r = $rs->fetch_object()){
			$statArr[$r->collid]['stats'][$r->dateEntered]['imgcnt'] = $r->imgcnt;
		}

		$sql3 = 'SELECT c.collid, CONCAT_WS("-",year(e.InitialTimeStamp),month(e.InitialTimeStamp)) as dateEntered, '.
			'c.collectionname, month(e.InitialTimeStamp) as monthEntered, year(e.InitialTimeStamp) as yearEntered, '.
			'COUNT(DISTINCT e.occid) AS georcnt '.
			'FROM omoccurrences AS o INNER JOIN omcollections AS c ON o.collid = c.collid '.
			'LEFT JOIN omoccuredits AS e ON o.occid = e.occid '.
			'WHERE o.collid in('.$collId.') AND datediff(curdate(), e.InitialTimeStamp) < '.$days.' '.
			'AND ((e.FieldName = "decimallongitude" AND e.FieldValueNew IS NOT NULL) '.
			'OR (e.FieldName = "decimallatitude" AND e.FieldValueNew IS NOT NULL)) '.
			'GROUP BY yearEntered,monthEntered,o.collid ORDER BY c.collectionname ';
		//echo $sql2;
		$rs = $this->conn->query($sql3);
		while($r = $rs->fetch_object()){
			$statArr[$r->collid]['stats'][$r->dateEntered]['georcnt'] = $r->georcnt;
		}
		$rs->free();

		return $statArr;
	}

    public function getOrderStatsDataArr($collId): array
	{
        $statsArr = array();
        $sql = 'SELECT (CASE WHEN t.RankId = 100 THEN t.SciName WHEN t2.RankId = 100 THEN t2.SciName ELSE NULL END) AS SciName, '.
            'COUNT(DISTINCT o.occid) AS SpecimensPerOrder, '.
            'COUNT(DISTINCT CASE WHEN o.decimalLatitude IS NOT NULL THEN o.occid ELSE NULL END) AS GeorefSpecimensPerOrder, '.
            'COUNT(DISTINCT CASE WHEN t2.RankId >= 220 THEN o.occid ELSE NULL END) AS IDSpecimensPerOrder, '.
            'COUNT(DISTINCT CASE WHEN t2.RankId >= 220 AND o.decimalLatitude IS NOT NULL THEN o.occid ELSE NULL END) AS IDGeorefSpecimensPerOrder '.
            'FROM omoccurrences AS o LEFT JOIN taxaenumtree AS e ON o.tid = e.tid '.
            'LEFT JOIN taxa AS t ON e.parenttid = t.TID '.
            'LEFT JOIN taxa AS t2 ON t.tidaccepted = t2.TID '.
            'WHERE (o.collid IN('.$collId.')) AND (t.RankId = 100 OR t2.RankId = 100) '.
            'GROUP BY SciName ';
        $rs = $this->conn->query($sql);
        //echo $sql;
        while($r = $rs->fetch_object()){
            $order = str_replace(array('"',"'"), '',$r->SciName);
            if($order && (is_numeric($order) || is_string($order))){
                $statsArr[(int)$order]['SpecimensPerOrder'] = $r->SpecimensPerOrder;
                $statsArr[(int)$order]['GeorefSpecimensPerOrder'] = $r->GeorefSpecimensPerOrder;
                $statsArr[(int)$order]['IDSpecimensPerOrder'] = $r->IDSpecimensPerOrder;
                $statsArr[(int)$order]['IDGeorefSpecimensPerOrder'] = $r->IDGeorefSpecimensPerOrder;
            }
        }
        $rs->free();

        return $statsArr;
    }

    public function getInstitutionArr(): array
	{
    	$retArr = array();
    	$sql = 'SELECT iid,institutionname,institutioncode '.
      	'FROM institutions '.
      	'ORDER BY institutionname,institutioncode ';
    	$rs = $this->conn->query($sql);
    	while($r = $rs->fetch_object()){
    		$retArr[$r->iid] = $r->institutionname.($r->institutioncode?' ('.$r->institutioncode.')':'');
    	}
    	return $retArr;
    }

    public function getCategoryArr(): array
	{
    	$retArr = array();
    	$sql = 'SELECT ccpk, category '.
      	'FROM omcollcategories '.
      	'ORDER BY category ';
    	$rs = $this->conn->query($sql);
    	while($r = $rs->fetch_object()){
    		$retArr[$r->ccpk] = $r->category;
    	}
    	$rs->free();
    	return $retArr;
    }

    public function getErrorStr(){
		return $this->errorStr;
	}
}
