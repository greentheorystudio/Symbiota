<?php
include_once(__DIR__ . '/../models/Permissions.php');
include_once(__DIR__ . '/../services/DbService.php');
include_once(__DIR__ . '/../services/SanitizerService.php');
 
class InventoryProjectManager {

	private $conn;
	private $pid;
	private $researchCoord = '';
	private $isPublic = 1;
	private $errorStr;

	public function __construct(){
        $connection = new DbService();
	    $this->conn = $connection->getConnection();
	}

	public function __destruct(){
		if($this->conn) {
			$this->conn->close();
		}
	}

	public function getProjectList(): array
	{
		$returnArr = array();
		$sql = 'SELECT pid, projname, managers, fulldescription '.
			'FROM fmprojects '.
			'WHERE ispublic = 1 '.
			'ORDER BY projname';
		$rs = $this->conn->query($sql);
		while($row = $rs->fetch_object()){
			$projId = $row->pid;
			$returnArr[$projId]['projname'] = $row->projname;
			$returnArr[$projId]['managers'] = $row->managers;
			$returnArr[$projId]['descr'] = $row->fulldescription;
		}
		$rs->free();
		return $returnArr;
	}

	public function getProjectData(): array
	{
		$returnArr = array();
		if($this->pid){
			$sql = 'SELECT pid, projname, managers, fulldescription, notes, '.
				'occurrencesearch, ispublic, sortsequence '.
				'FROM fmprojects '.
				'WHERE (pid = '.$this->pid.') ';
			$rs = $this->conn->query($sql);
			if($row = $rs->fetch_object()){
				$this->pid = $row->pid;
				$returnArr['projname'] = $row->projname;
				$returnArr['managers'] = $row->managers;
				$returnArr['fulldescription'] = $row->fulldescription;
				$returnArr['notes'] = $row->notes;
				$returnArr['occurrencesearch'] = $row->occurrencesearch;
				$returnArr['ispublic'] = $row->ispublic;
				$returnArr['sortsequence'] = $row->sortsequence;
				if($row->ispublic === 0){
					$this->isPublic = 0;
				}
			}
			$rs->free();
		}
		return $returnArr;
	}

	public function submitProjEdits($projArr): void
	{
		$fieldArr = array('projname', 'displayname', 'managers', 'fulldescription', 'notes', 'ispublic', 'parentpid', 'sortsequence');
		$sql = '';
		foreach($projArr as $field => $value){
			if(in_array($field, $fieldArr, true)){
				$sql .= ','.$field.' = "'.SanitizerService::cleanInStr($this->conn,$value).'"';
			}
		}
		$sql = 'UPDATE fmprojects SET '.substr($sql,1).' WHERE (pid = '.$this->pid.')';
		//echo $sql; exit;
		$this->conn->query($sql);
	}

	public function deleteProject($projID): bool
	{
		$status = true;
		if($projID && is_numeric($projID)){
			$sql = 'DELETE FROM fmprojects WHERE pid = '.$projID;
			//echo $sql; exit;
			if(!$this->conn->query($sql)){
				$status = false;
				$this->errorStr = 'ERROR deleting inventory project.';
			}
		}
		return $status;
	}

	public function addNewProject($projArr){
		$sql = 'INSERT INTO fmprojects(projname,managers,fulldescription,notes,ispublic) '.
			'VALUES("'.SanitizerService::cleanInStr($this->conn,$projArr['projname']).'",'.
			($projArr['managers']?'"'.SanitizerService::cleanInStr($this->conn,$projArr['managers']).'"':'NULL').','.
			($projArr['fulldescription']?'"'.SanitizerService::cleanInStr($this->conn,$projArr['fulldescription']).'"':'NULL').','.
			($projArr['notes']?'"'.SanitizerService::cleanInStr($this->conn,$projArr['notes']).'"':'NULL').','.
			(($GLOBALS['PUBLIC_CHECKLIST'] && is_numeric($projArr['ispublic']))?$projArr['ispublic']:'0').')';
		//echo $sql;
		if($this->conn->query($sql)){
			$this->pid = $this->conn->insert_id;
            $this->conn->query('INSERT INTO userroles (uid, role, tablename, tablepk) VALUES('.$GLOBALS['SYMB_UID'].',"ProjAdmin","fmprojects",'.$this->pid.') ');
            (new Permissions)->setUserPermissions();
		}
		else{
			$this->errorStr = 'ERROR creating new project.';
		}
		return $this->pid;
	}
	
	public function getResearchChecklists(): array
	{
		$retArr = array();
		if($this->pid){
			$sql = 'SELECT c.clid, c.name, c.latcentroid, c.longcentroid, c.access '.
				'FROM fmchklstprojlink cpl INNER JOIN fmchecklists c ON cpl.clid = c.clid '.
				'WHERE (cpl.pid = '.$this->pid.') AND ((c.access != "private")';
			if(array_key_exists('ClAdmin',$GLOBALS['USER_RIGHTS'])){
				$sql .= ' OR (c.clid IN ('.implode(',',$GLOBALS['USER_RIGHTS']['ClAdmin']).'))) ';
			}
			else{
				$sql .= ') ';
			}
			$sql .= 'ORDER BY c.SortSequence, c.name';
			//echo $sql;
			$rs = $this->conn->query($sql);
			while($row = $rs->fetch_object()){
                $coordArr = array();
                $projCoordArr = array();
                $retArr[$row->clid] = $row->name.($row->access === 'private'?' <span title="Viewable only to editors">(private)</span>':'');
                if($this->researchCoord){
                    $projCoordArr = json_decode($this->researchCoord, true);
                }
                if($row->latcentroid && $row->longcentroid){
                    $coordArr[] = (float)$row->latcentroid;
                    $coordArr[] = (float)$row->longcentroid;
                    $projCoordArr[] = $coordArr;
                    $this->researchCoord = json_encode($projCoordArr);
                }
			}
			$rs->free();
		}
		return $retArr;
	}
	
	public function getManagers(): array
	{
		$retArr = array();
		if($this->pid){
			$sql = 'SELECT u.uid, CONCAT_WS(", ", u.lastname, u.firstname) as fullname, u.username '.
				'FROM userroles r INNER JOIN users u ON r.uid = u.uid '.
				'WHERE r.role = "ProjAdmin" AND r.tablepk = '.$this->pid;
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$retArr[$r->uid] = $r->fullname.' ('.$r->username.')';
			}
			$rs->free();
			asort($retArr);
		}
		return $retArr;
	} 
	
	public function addManager($uid): bool
	{
		$status = false;
		if(is_numeric($uid) && $this->pid){
			$sql = 'INSERT INTO userroles(role,tablename,tablepk,uid) '.
				'VALUES("ProjAdmin","fmprojects",'.$this->pid.','.$uid.') ';
			if(!$this->conn->query($sql)){
				$this->errorStr = 'ERROR adding manager.';
			}
		}
		return $status;
	} 
	
	public function deleteManager($uid): bool
	{
		$status = true;
		if(is_numeric($uid) && $this->pid){
			$sql = 'DELETE FROM userroles WHERE (role = "ProjAdmin") AND (tablepk = '.$this->pid.') AND (uid = '.$uid.') ';
			//echo $sql;
			if(!$this->conn->query($sql)){
				$this->errorStr = 'ERROR removing manager.';
			}
		}
		return $status;
	}

	public function getPotentialManagerArr(): array
	{
		$retArr = array();
		$sql = 'SELECT u.uid, CONCAT_WS(", ", u.lastname, u.firstname) as fullname, u.username '.
			'FROM users u '.
			'ORDER BY u.lastname, u.firstname';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[$r->uid] = $r->fullname.' ('.$r->username.')';
		}
		$rs->free();
		return $retArr;
	}
	
	public function addChecklist($clid){
		if(is_numeric($clid)) {
            $sql = 'INSERT INTO fmchklstprojlink(pid,clid) VALUES('.$this->pid.','.$clid.') ';
            if(!$this->conn->query($sql)){
                return 'ERROR adding checklist to project.';
            }
		}
		return true;
	}

	public function deleteChecklist($clid){
		if(is_numeric($clid)) {
            $sql = 'DELETE FROM fmchklstprojlink WHERE (pid = '.$this->pid.') AND (clid = '.$clid.')';
            if($this->conn->query($sql)){
                return 'ERROR deleting checklist from project';
            }
		}
		return true;
	}

	public function getClAddArr(): array
	{
		$returnArr = array();
		$sql = 'SELECT c.clid, c.name, c.access '.
			'FROM fmchecklists c LEFT JOIN (SELECT clid FROM fmchklstprojlink WHERE (pid = '.$this->pid.')) pl ON c.clid = pl.clid '.
			'WHERE (pl.clid IS NULL) AND (c.access = "public" ';
		if(array_key_exists('ClAdmin',$GLOBALS['USER_RIGHTS'])){
			$sql .= ' OR (c.clid IN ('.implode(',',$GLOBALS['USER_RIGHTS']['ClAdmin']).'))) ';
		}
		else{
			$sql .= ') ';
		}
		$sql .= 'ORDER BY name';

		$rs = $this->conn->query($sql);
		while($row = $rs->fetch_object()){
			$returnArr[$row->clid] = $row->name.($row->access === 'private'?' (private)':'');
		}
		$rs->free();
		return $returnArr;
	}

	public function getClDeleteArr(): array
	{
		$returnArr = array();
		$sql = 'SELECT c.clid, c.name '.
			'FROM fmchecklists c INNER JOIN fmchklstprojlink pl ON c.clid = pl.clid '.
			'WHERE (pl.pid = '.$this->pid.') '.
			'ORDER BY name';
		$rs = $this->conn->query($sql);
		while($row = $rs->fetch_object()){
			$returnArr[$row->clid] = $row->name;
		}
		$rs->free();
		return $returnArr;
	}

	public function setPid($pid): void
	{
		if(is_numeric($pid)) {
			$this->pid = $pid;
		}
	}

	public function getErrorStr(){
		return $this->errorStr;
	}

    public function getResearchCoords(): string
    {
        return $this->researchCoord;
    }
}
