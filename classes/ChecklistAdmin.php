<?php
include_once(__DIR__ . '/../models/Permissions.php');
include_once(__DIR__ . '/../services/DbService.php');
include_once(__DIR__ . '/../services/SanitizerService.php');

class ChecklistAdmin{

	private $conn;
	private $clid;
	private $clName;

	public function __construct() {
        $connection = new DbService();
	    $this->conn = $connection->getConnection();
	}

	public function __destruct(){
 		if($this->conn) {
            $this->conn->close();
        }
	}

	public function getMetaData(): array
    {
		$retArr = array();
		if($this->clid){
			$sql = 'SELECT c.clid, c.name, c.locality, c.publication, ' .
                'c.abstract, c.authors, c.parentclid, c.notes, ' .
                'c.latcentroid, c.longcentroid, c.pointradiusmeters, c.access, c.defaultsettings, ' .
                'c.searchterms, c.datelastmodified, c.uid, c.type, c.initialtimestamp, c.footprintwkt ' .
                'FROM fmchecklists AS c WHERE c.clid = ' .$this->clid.' ';
	 		$result = $this->conn->query($sql);
			if($row = $result->fetch_object()){
				$this->clName = SanitizerService::cleanOutStr($row->name);
				$retArr['locality'] = SanitizerService::cleanOutStr($row->locality);
				$retArr['notes'] = SanitizerService::cleanOutStr($row->notes);
				$retArr['type'] = $row->type;
				$retArr['publication'] = SanitizerService::cleanOutStr($row->publication);
				$retArr['abstract'] = SanitizerService::cleanOutStr($row->abstract);
				$retArr['authors'] = SanitizerService::cleanOutStr($row->authors);
				$retArr['parentclid'] = $row->parentclid;
				$retArr['uid'] = $row->uid;
				$retArr['latcentroid'] = $row->latcentroid;
				$retArr['longcentroid'] = $row->longcentroid;
				$retArr['pointradiusmeters'] = $row->pointradiusmeters;
				$retArr['access'] = $row->access;
				$retArr['defaultsettings'] = $row->defaultsettings;
				$retArr['searchterms'] = $row->searchterms;
				$retArr['datelastmodified'] = $row->datelastmodified;
                $retArr['footprintwkt'] = $row->footprintwkt;
				$retArr['hasfootprintwkt'] = ($row->footprintwkt?'1':'0');
			}
			$result->free();
		}
		return $retArr;
	}

	public function createChecklist($postArr){
		$defaultViewArr = array();
        $defaultViewArr['thesfilter'] = array_key_exists('thesfilter',$postArr)?1:0;
        $defaultViewArr['showsynonyms'] = array_key_exists('showsynonyms',$postArr)?1:0;
		$defaultViewArr['ddetails'] = array_key_exists('ddetails',$postArr)?1:0;
		$defaultViewArr['dcommon'] = array_key_exists('dcommon',$postArr)?1:0;
		$defaultViewArr['dimages'] = array_key_exists('dimages',$postArr)?1:0;
		$defaultViewArr['dvouchers'] = array_key_exists('dvouchers',$postArr)?1:0;
		$defaultViewArr['dauthors'] = array_key_exists('dauthors',$postArr)?1:0;
		$defaultViewArr['dalpha'] = array_key_exists('dalpha',$postArr)?1:0;
		$defaultViewArr['activatekey'] = array_key_exists('activatekey',$postArr)?1:0;
		if($defaultViewArr) {
            $postArr['defaultsettings'] = json_encode($defaultViewArr);
        }
        $sql = 'INSERT INTO fmchecklists(name,authors,type,locality,publication,abstract,notes,latcentroid,longcentroid,'.
            'pointradiusmeters,footprintwkt,parentclid,access,uid,defaultsettings) '.
            'VALUES('.
            ($postArr['name']?'"'.SanitizerService::cleanInStr($this->conn,$postArr['name']).'"':'NULL').','.
            ($postArr['authors']?'"'.SanitizerService::cleanInStr($this->conn,$postArr['authors']).'"':'NULL').','.
            ((array_key_exists('type',$postArr) && $postArr['type'])?'"'.SanitizerService::cleanInStr($this->conn,$postArr['type']).'"':'NULL').','.
            ($postArr['locality']?'"'.SanitizerService::cleanInStr($this->conn,$postArr['locality']).'"':'NULL').','.
            ($postArr['publication']?'"'.SanitizerService::cleanInStr($this->conn,$postArr['publication']).'"':'NULL').','.
            ($postArr['abstract']?'"'.SanitizerService::cleanInStr($this->conn,$postArr['abstract']).'"':'NULL').','.
            ($postArr['notes']?'"'.SanitizerService::cleanInStr($this->conn,$postArr['notes']).'"':'NULL').','.
            ($postArr['latcentroid']?SanitizerService::cleanInStr($this->conn,$postArr['latcentroid']):'NULL').','.
            ($postArr['longcentroid']?SanitizerService::cleanInStr($this->conn,$postArr['longcentroid']):'NULL').','.
            ($postArr['pointradiusmeters']?SanitizerService::cleanInStr($this->conn,$postArr['pointradiusmeters']):'NULL').','.
            ($postArr['footprintwkt']?"'".SanitizerService::cleanInStr($this->conn,$postArr['footprintwkt'])."'":'NULL').','.
            ($postArr['parentclid']?SanitizerService::cleanInStr($this->conn,$postArr['parentclid']):'NULL').','.
            (($GLOBALS['PUBLIC_CHECKLIST'] && $postArr['access'])?'"'.SanitizerService::cleanInStr($this->conn,$postArr['access']).'"':'NULL').','.
            $GLOBALS['SYMB_UID'].','.
            ($postArr['defaultsettings']?"'".$postArr['defaultsettings']."'":'NULL').
            ')';
        $newClId = 0;
		if($this->conn->query($sql)){
			$newClId = $this->conn->insert_id;
			$this->conn->query('INSERT INTO userroles (uid, role, tablename, tablepk) VALUES('.$GLOBALS['SYMB_UID'].',"ClAdmin","fmchecklists",'.$newClId.') ');
			(new Permissions)->setUserPermissions();
		}
		return $newClId;
	}

	public function editMetaData($postArr): string
    {
		$statusStr = '';
		$setSql = '';
		$defaultViewArr = array();
        $defaultViewArr['thesfilter'] = array_key_exists('thesfilter',$postArr)?1:0;
        $defaultViewArr['showsynonyms'] = array_key_exists('showsynonyms',$postArr)?1:0;
		$defaultViewArr['ddetails'] = array_key_exists('ddetails',$postArr)?1:0;
		$defaultViewArr['dcommon'] = array_key_exists('dcommon',$postArr)?1:0;
		$defaultViewArr['dimages'] = array_key_exists('dimages',$postArr)?1:0;
		$defaultViewArr['dvouchers'] = array_key_exists('dvouchers',$postArr)?1:0;
		$defaultViewArr['dauthors'] = array_key_exists('dauthors',$postArr)?1:0;
		$defaultViewArr['dalpha'] = array_key_exists('dalpha',$postArr)?1:0;
		$defaultViewArr['activatekey'] = array_key_exists('activatekey',$postArr)?1:0;
		if($defaultViewArr) {
            $postArr['defaultsettings'] = json_encode($defaultViewArr);
        }

		$fieldArr = array('name'=>'s','authors'=>'s','type'=>'s','locality'=>'s','publication'=>'s','abstract'=>'s','notes'=>'s','latcentroid'=>'n',
			'longcentroid'=>'n','pointradiusmeters'=>'n','parentclid'=>'n','access'=>'s','defaultsettings'=>'s');
		foreach($fieldArr as $fieldName => $fieldType){
			if($fieldName === 'defaultsettings'){
                $setSql .= ', '.$fieldName." = '".strip_tags($postArr['defaultsettings'], '<i><u><b><a>')."'";
            }
            else{
                $v = SanitizerService::cleanInStr($this->conn,$postArr[$fieldName]);
                if($fieldName !== 'abstract') {
                    $v = strip_tags($v, '<i><u><b><a>');
                }
                if($v){
                    if($fieldType === 's' || ($fieldType === 'n' && is_numeric($v))){
                        $setSql .= ', '.$fieldName.' = "'.$v.'"';
                    }
                    else{
                        $setSql .= ', '.$fieldName.' = NULL';
                    }
                }
                else{
                    $setSql .= ', '.$fieldName.' = NULL';
                }
            }
		}
		$sql = 'UPDATE fmchecklists SET '.substr($setSql,2).' WHERE clid = '.$this->clid.' ';
		//echo $sql; exit;
		if($this->conn->query($sql)){
			if(($postArr['type'] === 'rarespp') && $postArr['locality']) {
                $sql = 'UPDATE omoccurrences AS o INNER JOIN taxa AS t ON o.tid = t.tid '.
                    'INNER JOIN fmchklsttaxalink AS cl ON t.tidaccepted = cl.tid '.
                    'SET o.localitysecurity = 1 '.
                    'WHERE cl.clid = '.$this->clid.' AND o.stateprovince = "'.$postArr['locality'].'" AND ISNULL(o.localitySecurityReason) ';
                if(!$this->conn->query($sql)){
                    $statusStr = 'Error updating rare state species.';
                }
            }
		}
		else{
			$statusStr = 'Error: unable to update checklist metadata.';
		}
		return $statusStr;
	}

	public function deleteChecklist($delClid){
		$statusStr = true;
		$sql1 = 'SELECT uid FROM userroles '.
			'WHERE role = "ClAdmin" AND tablename = "fmchecklists" AND tablepk = "'.$delClid.'" AND uid <> '.$GLOBALS['SYMB_UID'];
		$rs1 = $this->conn->query($sql1);
		if($rs1->num_rows === 0){
			$sql2 = 'DELETE FROM fmvouchers WHERE clid = ' .$delClid.' ';
			if($this->conn->query($sql2)){
				$sql3 = 'DELETE FROM fmchklsttaxalink WHERE clid = ' .$delClid.' ';
				if($this->conn->query($sql3)){
					$sql4 = 'DELETE FROM fmchecklists WHERE clid = ' .$delClid.' ';
					if($this->conn->query($sql4)){
						$sql5 = 'DELETE FROM userroles WHERE role = "ClAdmin" AND tablename = "fmchecklists" AND tablepk = "'.$delClid.'" ';
						$this->conn->query($sql5);
					}
					else{
						$statusStr = 'ERROR attempting to delete checklist.';
					}
				}
				else{
					$statusStr = 'ERROR attempting to delete checklist taxa links.';
				}
			}
			else{
				$statusStr = 'ERROR attempting to delete checklist vouchers.';
			}
		}
		else{
			$statusStr = 'Checklist cannot be deleted until all editors are removed. Remove editors and then try again.';
		}
		$rs1->free();
		return $statusStr;
	}

	public function getFootprintWkt(): string
    {
		$retStr = '';
		if($this->clid){
			$sql = 'SELECT footprintwkt FROM fmchecklists WHERE clid = '.$this->clid.' ';
			$rs = $this->conn->query($sql);
			if($r = $rs->fetch_object()){
				$retStr = $r->footprintwkt;
			}
			$rs->free();
		}
		return $retStr;
	}

	public function savePolygon($polygonStr): bool
    {
		$status = true;
		if($this->clid){
			$sql = 'UPDATE fmchecklists SET footprintwkt = '.($polygonStr?'"'.SanitizerService::cleanInStr($this->conn,$polygonStr).'"':'NULL').' WHERE clid = '.$this->clid.' ';
			if(!$this->conn->query($sql)){
				echo 'ERROR saving polygon to checklist.';
				$status = false;
			}
		}
		return $status;
	}

	public function getChildrenChecklist(): array
    {
		$retArr = array();
		$targetStr = $this->clid;
		do{
			$sql = 'SELECT c.clid, c.name, child.clid AS pclid '.
				'FROM fmchklstchildren AS child INNER JOIN fmchecklists AS c ON child.clidchild = c.clid '.
				'WHERE child.clid IN('.trim($targetStr,',').') '.
				'ORDER BY c.name ';
			$rs = $this->conn->query($sql);
			$targetStr = '';
			while($r = $rs->fetch_object()){
				$retArr[$r->clid]['name'] = $r->name;
				$retArr[$r->clid]['pclid'] = $r->pclid;
				$targetStr .= ','.$r->clid;
			}
			$rs->free();
		}
		while($targetStr);
		asort($retArr);
		return $retArr;
	}

	public function getParentChecklists(): array
    {
		$retArr = array();
		$targetStr = $this->clid;
		do{
			$sql = 'SELECT c.clid, c.name, child.clid AS pclid '.
				'FROM fmchklstchildren AS child INNER JOIN fmchecklists AS c ON child.clid = c.clid '.
				'WHERE child.clidchild IN('.trim($targetStr,',').') ';
			$rs = $this->conn->query($sql);
			$targetStr = '';
			while($r = $rs->fetch_object()){
				$retArr[$r->clid] = $r->name;
				$targetStr .= ','.$r->clid;
			}
			if($targetStr) {
                $targetStr = substr($targetStr, 1);
            }
			$rs->free();
		}while($targetStr);
		asort($retArr);
		return $retArr;
	}

	public function getChildSelectArr(): array
    {
		$retArr = array();
		$clidStr = '';
		if(isset($GLOBALS['USER_RIGHTS']) && $GLOBALS['USER_RIGHTS']['ClAdmin']){
			$clidStr = implode(',',$GLOBALS['USER_RIGHTS']['ClAdmin']);
		}
		if($clidStr){
			$sql = 'SELECT clid, name '.
				'FROM fmchecklists '.
				'WHERE clid <> '.$this->clid.' AND clid IN('.$clidStr.') '.
				'ORDER BY name';
			//echo $sql;
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$retArr[$r->clid] = $r->name;
			}
			$rs->free();
		}
		return $retArr;
	}

	public function addChildChecklist($clidAdd): string
    {
		$statusStr = '';
		$sql = 'INSERT INTO fmchklstchildren(clid, clidchild, modifieduid) '.
			'VALUES('.$this->clid.','.$clidAdd.','.$GLOBALS['SYMB_UID'].') ';
		//echo $sql;
		if(!$this->conn->query($sql)){
			$statusStr = 'ERROR adding child checklist link';
		}
		return $statusStr;
	}

	public function deleteChildChecklist($clidDel): string
    {
		$statusStr = '';
		$sql = 'DELETE FROM fmchklstchildren WHERE clid = '.$this->clid.' AND clidchild = '.$clidDel;
		//echo $sql;
		if(!$this->conn->query($sql)){
			$statusStr = 'ERROR deleting child checklist link';
		}
		return $statusStr;
	}

	public function addNewSpecies($dataArr, $setRareSpp = null){
		if(!$this->clid) {
            return 'ERROR adding species: checklist identifier not set';
        }
		$insertStatus = false;
		$colSql = '';
		$valueSql = '';
		foreach($dataArr as $k =>$v){
			$colSql .= ','.$k;
			if($v){
				if(is_numeric($v)){
					$valueSql .= ','.$v;
				}
				else{
					$valueSql .= ',"'.SanitizerService::cleanInStr($this->conn,$v).'"';
				}
			}
			else{
				$valueSql .= ',NULL';
			}
		}
		$sql = 'INSERT INTO fmchklsttaxalink (clid'.$colSql.') '.
			'VALUES ('.$this->clid.$valueSql.')';
		if($this->conn->query($sql)){
			if($setRareSpp){
				$clMeta = $this->getMetaData();
				$state = $clMeta['locality'];
				if($state && $dataArr['tid']){
					$sqlRare = 'UPDATE omoccurrences AS o INNER JOIN taxa AS t ON o.tid = t.tid '.
						'SET o.localitysecurity = 1 '.
						'WHERE (ISNULL(o.localitysecurity) OR o.localitysecurity = 0) AND ISNULL(o.localitySecurityReason) '.
						'AND o.stateprovince = "'.$state.'" AND t.tidaccepted = '.$dataArr['tid'].' ';
					//echo $sqlRare; exit;
					$this->conn->query($sqlRare);
				}
			}
		}
		else{
			$insertStatus = 'ERROR: unable to add species.';
		}
		return $insertStatus;
	}

	public function getEditors(): array
    {
		$editorArr = array();
		$sql = 'SELECT u.uid, CONCAT(CONCAT_WS(", ",u.lastname,u.firstname)," (",u.username,")") AS uname '.
			'FROM userroles AS ur INNER JOIN users AS u ON ur.uid = u.uid '.
			'WHERE ur.role = "ClAdmin" AND ur.tablename = "fmchecklists" AND ur.tablepk = '.$this->clid.' '.
			'ORDER BY u.lastname,u.firstname';
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
				$uName = $r->uname;
				if(strlen($uName) > 60) {
                    $uName = substr($uName, 0, 60);
                }
				$editorArr[$r->uid] = $r->uname;
			}
			$rs->free();
		}
		return $editorArr;
	}

	public function addEditor($u): string
    {
		$statusStr = '';
		if(is_numeric($u) && $this->clid){
			$sql = 'INSERT INTO userroles(uid,role,tablename,tablepk) '.
				'VALUES('.$u.',"ClAdmin","fmchecklists",'.$this->clid.')';
			if(!$this->conn->query($sql)){
				$statusStr = 'ERROR: unable to add editor.';
			}
		}
		return $statusStr;
	}

	public function deleteEditor($u): string
    {
		$statusStr = '';
		$sql = 'DELETE FROM userroles '.
			'WHERE uid = '.$u.' AND role = "ClAdmin" AND tablename = "fmchecklists" AND tablepk = '.$this->clid.' ';
		if(!$this->conn->query($sql)){
			$statusStr = 'ERROR: unable to remove editor.';
		}
		return $statusStr;
	}

	public function getReferenceChecklists(): array
    {
		$retArr = array();
		$sql = 'SELECT clid, name FROM fmchecklists WHERE access = "public" ';
		if(isset($GLOBALS['USER_RIGHTS']['ClAdmin'])){
			$clidStr = implode(',',$GLOBALS['USER_RIGHTS']['ClAdmin']);
			if($clidStr) {
                $sql .= 'OR clid IN(' . $clidStr . ') ';
            }
		}
		$sql .= 'ORDER BY name';
		//echo $sql;
		$rs = $this->conn->query($sql);
		while($row = $rs->fetch_object()){
			$retArr[$row->clid] = $row->name;
		}
		$rs->free();
		return $retArr;
	}

	public function getTaxa(): array
    {
		$retArr = array();
		$sql = 'SELECT t.tid, t.sciname '.
			'FROM fmchklsttaxalink AS l INNER JOIN taxa AS t ON l.tid = t.tid '.
			'WHERE l.clid = '.$this->clid.' ORDER BY t.sciname';
		//echo $sql;
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[$r->tid] = $r->sciname;
		}
		$rs->free();
		return $retArr;
	}

	public function getUserList(): array
    {
		$returnArr = array();
		$sql = 'SELECT u.uid, CONCAT(CONCAT_WS(", ",u.lastname,u.firstname)," (",u.username,")") AS uname '.
			'FROM users AS u '.
			'ORDER BY u.lastname,u.firstname';
		//echo $sql;
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$returnArr[$r->uid] = $r->uname;
		}
		$rs->free();
		return $returnArr;
	}

	public function getInventoryProjects(): array
    {
		$retArr = array();
		if($this->clid){
			$sql = 'SELECT p.pid, p.projname '.
				'FROM fmprojects AS p INNER JOIN fmchklstprojlink AS pl ON p.pid = pl.pid '.
				'WHERE pl.clid = '.$this->clid.' ORDER BY p.projname';
			//echo $sql;
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$retArr[$r->pid] = $r->projname;
			}
			$rs->free();
		}
		return $retArr;
	}

	public function getVoucherProjects(): array
    {
		$retArr = array();
		$runQuery = true;
		$sql = 'SELECT collid, collectionname '.
			'FROM omcollections WHERE colltype = "HumanObservation" ';
		if(!array_key_exists('SuperAdmin',$GLOBALS['USER_RIGHTS'])){
			$collInStr = '';
			foreach($GLOBALS['USER_RIGHTS'] as $k => $v){
				if($k === 'CollAdmin' || $k === 'CollEditor'){
					$collInStr .= ','.implode(',',$v);
				}
			}
			if($collInStr){
				$sql .= 'AND collid IN ('.substr($collInStr,1).') ';
			}
			else{
				$runQuery = false;
			}
		}
		$sql .= 'ORDER BY colltype,collectionname';
		//echo $sql;
		if($runQuery && $rs = $this->conn->query($sql)) {
            while($r = $rs->fetch_object()){
                $retArr[$r->collid] = $r->collectionname;
            }
            $rs->free();
        }
		return $retArr;
	}

	public function getManagementLists($uid): array
    {
		$returnArr = array();
		if(is_numeric($uid)){
			$clStr = '';
			$projStr = '';
			$sql = 'SELECT role,tablepk FROM userroles '.
				'WHERE uid = '.(int)$uid.' AND (role = "ClAdmin" OR role = "ProjAdmin") ';
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				if($r->role === 'ClAdmin') {
                    $clStr .= ',' . $r->tablepk;
                }
				if($r->role === 'ProjAdmin') {
                    $projStr .= ',' . $r->tablepk;
                }
			}
			$rs->free();
			if($clStr){
                $returnArr['cl'] = array();
                $sql = 'SELECT clid, name FROM fmchecklists '.
						'WHERE clid IN('.substr($clStr,1).') '.
						'ORDER BY name';
				$rs = $this->conn->query($sql);
				while($row = $rs->fetch_object()){
					$nodeArr = array();
                    $nodeArr['clid'] = $row->clid;
                    $nodeArr['name'] = $row->name;
                    $returnArr['cl'][] = $nodeArr;
				}
				$rs->free();
			}
			if($projStr){
                $returnArr['proj'] = array();
                $sql = 'SELECT pid, projname '.
						'FROM fmprojects '.
						'WHERE pid IN('.substr($projStr,1).') '.
						'ORDER BY projname';
				$rs = $this->conn->query($sql);
				while($row = $rs->fetch_object()){
                    $nodeArr = array();
                    $nodeArr['pid'] = $row->pid;
                    $nodeArr['projname'] = $row->projname;
                    $returnArr['proj'][] = $nodeArr;
				}
				$rs->free();
			}
		}
		return $returnArr;
	}

	public function setClid($clid): void
    {
		if(is_numeric($clid)){
			$this->clid = $clid;
		}
	}

	public function getClName(){
		return $this->clName;
	}
}
