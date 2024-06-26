<?php
include_once(__DIR__ . '/../services/DbService.php');
include_once(__DIR__ . '/../services/SanitizerService.php');

/*
SuperAdmin			Edit all data and assign new permissions

RareSppAdmin		Add or remove species from rare species list
RareSppReadAll		View and map rare species collection data for all collections
RareSppReader-#		View and map rare species collecton data for specific collections
CollAdmin-#			Upload records; modify metadata
CollEditor-#		Edit collection records
CollTaxon-#:#		Edit collection records within taxonomic speciality

ClAdmin-#			Checklist write access
ProjAdmin-#			Project admin access
KeyAdmin			Edit identification key characters and character states
KeyEditor			Edit identification key data
TaxonProfile		Modify decriptions; add images;
Taxonomy			Add names; edit name; change taxonomy
*/

class PermissionsManager{

	private $conn;

	public function __construct() {
		$connection = new DbService();
		$this->conn = $connection->getConnection();
	}

	public function __destruct(){
 		if($this->conn) {
			$this->conn->close();
		}
	}

	public function getUserPermissions($uid): array
	{
		$perArr = array();
		if(is_numeric($uid)){
			$sql = 'SELECT r.role, r.tablepk, CONCAT_WS(", ",u.lastname,u.firstname) AS assignedby, r.initialtimestamp '.
				'FROM userroles AS r LEFT JOIN users AS u ON r.uidassignedby = u.uid '.
				'WHERE (r.uid = '.$uid.') ';
			$result = $this->conn->query($sql);
			while($row = $result->fetch_object()){
				$assignedBy = 'assigned by: '.($row->assignedby?$row->assignedby.' ('.$row->initialtimestamp.')':'unknown');
				if($row->tablepk){
					$perArr[$row->role][(int)$row->tablepk]['aby'] = $assignedBy;
				}
				else{
					$perArr[$row->role]['aby'] = $assignedBy;
					$perArr[$row->role]['role'] = $row->role;
				}
			}
			$result->free();

			if(array_key_exists('CollAdmin',$perArr)){
				$sql = 'SELECT c.collid, c.collectionname FROM omcollections c '.
					'WHERE (c.collid IN('.implode(',',array_keys($perArr['CollAdmin'])).')) '.
					'ORDER BY c.collectionname';
				$result = $this->conn->query($sql);
				while($row = $result->fetch_object()){
					$perArr['CollAdmin'][(int)$row->collid]['name'] = $row->collectionname;
				}
				uasort($perArr['CollAdmin'], array($this,'sortByName'));
				$result->free();
			}
			if(array_key_exists('CollEditor',$perArr)){
				$sql = 'SELECT c.collid, c.collectionname FROM omcollections c '.
					'WHERE (c.collid IN('.implode(',',array_keys($perArr['CollEditor'])).')) '.
					'ORDER BY c.collectionname';
				$result = $this->conn->query($sql);
				while($row = $result->fetch_object()){
					$perArr['CollEditor'][(int)$row->collid]['name'] = $row->collectionname;
				}
				uasort($perArr['CollEditor'], array($this,'sortByName'));
				$result->free();
			}
			if(array_key_exists('RareSppReader',$perArr)){
				$sql = 'SELECT c.collid, c.collectionname FROM omcollections c '.
					'WHERE (c.collid IN('.implode(',',array_keys($perArr['RareSppReader'])).'))'.
					'ORDER BY c.collectionname';
				$result = $this->conn->query($sql);
				while($row = $result->fetch_object()){
					$perArr['RareSppReader'][(int)$row->collid]['name'] = $row->collectionname;
				}
				uasort($perArr['RareSppReader'], array($this,'sortByName'));
				$result->free();
			}

			if(array_key_exists('ClAdmin',$perArr)){
				$sql = 'SELECT cl.clid, cl.name FROM fmchecklists cl '.
					'WHERE (cl.clid IN('.implode(',',array_keys($perArr['ClAdmin'])).'))'.
					'ORDER BY cl.name';
				$result = $this->conn->query($sql);
				while($row = $result->fetch_object()){
					$perArr['ClAdmin'][(int)$row->clid]['name'] = $row->name;
				}
				uasort($perArr['ClAdmin'], array($this,'sortByName'));
				$result->free();
			}

			if(array_key_exists('ProjAdmin',$perArr)){
				$sql = 'SELECT pid, projname FROM fmprojects '.
					'WHERE (pid IN('.implode(',',array_keys($perArr['ProjAdmin'])).')) '.
					'ORDER BY projname';
				$result = $this->conn->query($sql);
				while($row = $result->fetch_object()){
					$perArr['ProjAdmin'][(int)$row->pid]['name'] = $row->projname;
				}
				uasort($perArr['ProjAdmin'], array($this,'sortByName'));
				$result->free();
			}
		}
		return $perArr;
	}

	public function deletePermission($id, $role, $tablePk, $secondaryVariable = null): string
	{
		$statusStr = '';
		if(is_numeric($id)){
			$sql = 'DELETE FROM userroles '.
				'WHERE (uid = '.$id.') AND (role = "'.$role.'") '.
				'AND (tablepk '.($tablePk?' = '.$tablePk:' IS NULL').') ';
			if($secondaryVariable){
				$sql .= 'AND (secondaryVariable = "'.$secondaryVariable.'") ';
			}
			$this->conn->query($sql);
		}
		return $statusStr;
	}

	public function addPermission($uid,$role,$tablePk,$secondaryVariable = null): string
	{
		$statusStr = '';
		if(is_numeric($uid)){
			$sql = 'SELECT uid,role,tablepk,secondaryVariable,uidassignedby '.
				'FROM userroles WHERE (uid = '.$uid.') AND (role = "'.$role.'") ';
			if($tablePk) {
				$sql .= 'AND (tablepk = ' . $tablePk . ') ';
			}
			if($secondaryVariable) {
				$sql .= 'AND (secondaryVariable = ' . $secondaryVariable . ') ';
			}
			$rs = $this->conn->query($sql);
			if(!$rs->num_rows){
				$sql1 = 'INSERT INTO userroles(uid,role,tablepk,secondaryVariable,uidassignedby) '.
					'VALUES('.$uid.',"'.$role.'",'.($tablePk?:'NULL').','.
					($secondaryVariable?'"'.$secondaryVariable.'"':'NULL').','.$GLOBALS['SYMB_UID'].')';
				if(!$this->conn->query($sql1)){
					$statusStr = 'ERROR adding user permission.';
				}
			}
			$rs->free();
		}
		return $statusStr;
	}

	public function getTaxonEditorArr($collid, $limitByColl = null): array
	{
		$pArr = array();
		$sql2 = 'SELECT uid, role, tablepk, secondaryvariable '.
			'FROM userroles WHERE role = ("CollTaxon") AND (tablepk = '.$collid.') ';
		$rs2 = $this->conn->query($sql2);
		while($r2 = $rs2->fetch_object()){
			if(($r2->role === 'CollTaxon') && ($r2->tablepk = $collid) && ($r2->secondaryvariable = 'all')){
				$pArr[$r2->uid]['all'] = 1;
			}
			else{
				$pArr[$r2->uid]['utid'][] = $r2->secondaryvariable;
			}
		}
		$rs2->free();
		$retArr = array();
		$sql = 'SELECT ut.idusertaxonomy, u.uid, CONCAT_WS(", ", lastname, firstname) as fullname, t.sciname, u.username '.
			'FROM usertaxonomy AS ut INNER JOIN users AS u ON ut.uid = u.uid '.
			'INNER JOIN taxa AS t ON ut.tid = t.tid '.
			'WHERE ut.editorstatus = "OccurrenceEditor" ';
		if($limitByColl && $pArr){
			$sql .= 'AND ut.uid IN('.implode(',',array_keys($pArr)).') ';
		}
		$sql .= 'ORDER BY u.lastname, u.firstname, t.sciname';
		//echo '<div>'.$sql.'</div>';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			if($limitByColl){
				if(isset($pArr[$r->uid])){
					if(isset($pArr[$r->uid]['all']) || in_array($r->idusertaxonomy, $pArr[$r->uid]['utid'], true)){
						$retArr[$r->uid]['username'] = $r->fullname.' ('.$r->username.')';
						$retArr[$r->uid][$r->idusertaxonomy] = $r->sciname;
					}
				}
			}
			else if(!isset($pArr[$r->uid]['utid']) || !in_array($r->idusertaxonomy, $pArr[$r->uid]['utid'], true)){
				$retArr[$r->uid]['username'] = $r->fullname.' ('.$r->username.')';
				$retArr[$r->uid][$r->idusertaxonomy] = $r->sciname;
			}
		}
		$rs->free();
		foreach($pArr as $uid => $upArr){
			if(array_key_exists('all',$upArr)) {
				$retArr[$uid]['all'] = 1;
			}
		}

		return $retArr;
	}

	public function getCollectionMetadata($targetCollid = null, $collTypeLimit = null): array
	{
		$retArr = array();
		$sql = 'SELECT collid, collectionname, institutioncode, collectioncode, colltype '.
			'FROM omcollections ';
		$sqlWhere = '';
		if($collTypeLimit === 'specimens'){
			$sqlWhere .= 'AND (colltype = "PreservedSpecimen") ';
		}
		elseif($collTypeLimit === 'observations'){
			$sqlWhere .= 'AND (colltype = "HumanObservation") ';
		}
		if($targetCollid){
			$sqlWhere .= 'AND (collid = '.$targetCollid.') ';
		}
		if($sqlWhere) {
			$sql .= 'WHERE ' . substr($sqlWhere, 4);
		}
		$sql .= 'ORDER BY collectionname';
		//echo $sql;
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[$r->collid]['collectionname'] = SanitizerService::cleanOutStr($r->collectionname);
			$retArr[$r->collid]['institutioncode'] = $r->institutioncode;
			$retArr[$r->collid]['collectioncode'] = $r->collectioncode;
			$retArr[$r->collid]['colltype'] = $r->colltype;
		}
		$rs->free();
		return $retArr;
	}

	public function getCollectionEditors($collid): array
	{
		$returnArr = array();
		if($collid){
			$sql = 'SELECT ur.uid, ur.role, ur.tablepk, CONCAT_WS(", ",u.lastname,u.firstname) AS uname, '.
				'CONCAT_WS(", ",u2.lastname,u2.firstname) AS assignedby, ur.initialtimestamp '.
				'FROM userroles AS ur INNER JOIN users AS u ON ur.uid = u.uid '.
				'LEFT JOIN users AS u2 ON ur.uidassignedby = u2.uid '.
				'WHERE (ur.role = "CollAdmin" AND ur.tablepk = '.$collid.') OR (ur.role = "CollEditor" AND ur.tablepk = '.$collid.') '.
				'OR (ur.role = "RareSppReader" AND ur.tablepk = '.$collid.') '.
				'ORDER BY u.lastname,u.firstname';
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$pGroup = 'rarespp';
				if($r->role === 'CollAdmin') {
					$pGroup = 'admin';
				}
				elseif($r->role === 'CollEditor') {
					$pGroup = 'editor';
				}
				$outStr = '<span title="assigned by: '.($r->assignedby?$r->assignedby.' ('.$r->initialtimestamp.')':'unknown').'">'.SanitizerService::cleanOutStr($r->uname).'</span>';
				$returnArr[$pGroup][$r->uid] = $outStr;
			}
			$rs->free();
		}
		return $returnArr;
	}

	public function getUsers($listType, $searchTermIn = null): array
	{
		$retArr = array();
		$sql = 'SELECT uid, CONCAT_WS(", ",lastname,firstname) AS uname, username '.
			'FROM users ';
        if($listType === 'confirmed'){
            $sql .= 'WHERE (validated = "1") ';
        }
        elseif($listType === 'unconfirmed'){
            $sql .= 'WHERE (validated <> "1") ';
        }
		if($searchTermIn){
			if($listType === 'confirmed' || $listType === 'unconfirmed'){
                $sql .= 'AND (';
            }
            else{
                $sql .= 'WHERE (';
            }
            $searchTerm = SanitizerService::cleanInStr($this->conn,$searchTermIn);
			$sql .= '(lastname LIKE "'.$searchTerm.'%") ';
			if(strlen($searchTerm) > 1) {
				$sql .= "OR (username LIKE '" . $searchTerm . "%') ";
			}
            $sql .= ')';
		}
		$sql .= 'ORDER BY lastname, firstname';
		//echo "<div>".$sql."</div>";
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[$r->uid] = SanitizerService::cleanOutStr($r->uname.($r->username?' ('.$r->username.')':''));
		}
		$rs->free();
		return $retArr;
	}

	public function getProjectArr($pidKeys): array
	{
		$returnArr = array();
		$sql = 'SELECT pid, projname FROM fmprojects ';
		if($pidKeys) {
			$sql .= 'WHERE (pid NOT IN(' . implode(',', $pidKeys) . ')) ';
		}
		$sql .= 'ORDER BY projname';
		//echo $sql;
		$result = $this->conn->query($sql);
		while($row = $result->fetch_object()){
			$returnArr[$row->pid] = $row->projname;
		}
		$result->free();
		return $returnArr;
	}

	public function getChecklistArr($clKeys): array
	{
		$returnArr = array();
		$sql = 'SELECT cl.clid, cl.name FROM fmchecklists cl ';
		if($clKeys) {
			$sql .= 'WHERE (cl.access != "private") AND (cl.clid NOT IN(' . implode(',', $clKeys) . ')) ';
		}
		$sql .= 'ORDER BY cl.name';
		//echo $sql;
		$result = $this->conn->query($sql);
		while($row = $result->fetch_object()){
			$returnArr[$row->clid] = $row->name;
		}
		$result->free();
		return $returnArr;
	}

    private function sortByName($a, $b): int
    {
        $retVal = null;
	    if(!isset($a['name'])) {
            $retVal = -1;
        }
        elseif(isset($b['name'])) {
            $retVal = strcmp($a['name'], $b['name']);
        }
	    else{
            $retVal = 1;
        }
        return $retVal;
    }

    public function validatePermission($permissions, $key): array
    {
        $returnArr = array();
        if(is_array($permissions)){
            if($GLOBALS['IS_ADMIN']){
                $returnArr = $permissions;
            }
            else{
                foreach($permissions as $permission){
                    if(array_key_exists($permission, $GLOBALS['USER_RIGHTS']) && (!$key || !is_array($GLOBALS['USER_RIGHTS'][$permission]) || in_array((int)$key, $GLOBALS['USER_RIGHTS'][$permission], true))){
                        $returnArr[] = $permission;
                    }
                }
            }
        }
        elseif($GLOBALS['IS_ADMIN'] || (array_key_exists($permissions, $GLOBALS['USER_RIGHTS']) && (!$key || !is_array($GLOBALS['USER_RIGHTS'][$permissions]) || in_array((int)$key, $GLOBALS['USER_RIGHTS'][$permissions], true)))){
            $returnArr[] = $permissions;
        }
        return $returnArr;
    }
}
