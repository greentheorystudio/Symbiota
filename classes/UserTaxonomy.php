<?php
include_once(__DIR__ . '/DbConnection.php');
include_once(__DIR__ . '/ProfileManager.php');

class UserTaxonomy {

	private $conn;
	
	public function __construct() {
		$connection = new DbConnection();
		$this->conn = $connection->getConnection();
	}

	public function __destruct(){
 		if($this->conn) {
			$this->conn->close();
		}
	}
	
	public function getTaxonomyEditors(): array
	{
		$retArr = array();
		$sql = 'SELECT ut.idusertaxonomy, u.uid, CONCAT_WS(", ", u.lastname, u.firstname) AS fullname, t.sciname, ut.editorstatus, '.
			'ut.geographicscope, ut.notes, u.username '.
			'FROM usertaxonomy AS ut INNER JOIN users AS u ON ut.uid = u.uid '.
			'INNER JOIN taxa AS t ON ut.tid = t.tid '.
			'ORDER BY u.lastname, u.firstname, t.sciname';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$editorStatus = $r->editorstatus;
			if(!$editorStatus) {
				$editorStatus = 'RegionOfInterest';
			}
			$retArr[$editorStatus][$r->uid]['username'] = $r->fullname.' ('.$r->username.')';
			$retArr[$editorStatus][$r->uid][$r->idusertaxonomy]['sciname'] = $r->sciname;
			$retArr[$editorStatus][$r->uid][$r->idusertaxonomy]['geoscope'] = $r->geographicscope;
			$retArr[$editorStatus][$r->uid][$r->idusertaxonomy]['notes'] = $r->notes;
		}
		$rs->free();
		return $retArr;
	} 

	public function deleteUser($utid,$uid,$editorStatus): string
	{
		$profileManager = new ProfileManager();
		$profileManager->setUid($uid);
		return $profileManager->deleteUserTaxonomy($utid,$editorStatus);
	}

	public function addUser($uid, $taxa, $editorStatus, $geographicScope, $notes): string
	{
		$profileManager = new ProfileManager();
		$profileManager->setUid($uid);
		return $profileManager->addUserTaxonomy($taxa, $editorStatus, $geographicScope, $notes);
	}

	public function getUserArr(): array
	{
		$retArr = array();
		$sql = 'SELECT u.uid, CONCAT_WS(", ",u.lastname,u.firstname,CONCAT(" (",u.username,")")) AS fullname '.
			'FROM users AS u '.
			'ORDER BY u.lastname,u.firstname,u.username ';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[$r->uid] = $r->fullname;
		}
		$rs->free();
		return $retArr;
	}

}
