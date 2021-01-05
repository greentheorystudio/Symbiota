<?php
include_once(__DIR__ . '/DbConnection.php');

class SiteMapManager{
	
	private $conn;
	private $collArr = array();
	private $obsArr = array();
	private $genObsArr = array();
	
	public function __construct() {
		$connection = new DbConnection();
		$this->conn = $connection->getConnection();
	}

	public function __destruct(){
 		if(!($this->conn === false)) {
			$this->conn->close();
		}
	}

	public function setCollectionList(): void
	{
		global $USER_RIGHTS, $IS_ADMIN;
		$adminArr = array();
		$editorArr = array();
		$sql = 'SELECT c.collid, CONCAT_WS(":",c.institutioncode, c.collectioncode) AS ccode, c.collectionname, c.colltype '.
			'FROM omcollections c ';
		if(!$IS_ADMIN){
			if(array_key_exists('CollAdmin',$USER_RIGHTS)){
				$adminArr = $USER_RIGHTS['CollAdmin'];
			}
			if(array_key_exists('CollEditor',$USER_RIGHTS)){
				$editorArr = $USER_RIGHTS['CollEditor'];
			}
			if($adminArr || $editorArr){
				$sql .= 'WHERE (c.collid IN('.implode(',',array_merge($adminArr,$editorArr)).')) ';
			}
			else{
				$sql = '';
			}
		}
		if($sql){
			$sql .= 'ORDER BY c.collectionname';
			//echo "<div>".$sql."</div>";
			$rs = $this->conn->query($sql);
			if($rs){
				while($row = $rs->fetch_object()){
					$name = $row->collectionname.($row->ccode? ' (' .$row->ccode. ')' : '');
					$isCollAdmin = ($IS_ADMIN|| in_array($row->collid, $adminArr, true) ?1:0);
					if($row->colltype === 'Observations'){
						$this->obsArr[$row->collid]['name'] = $name;
						$this->obsArr[$row->collid]['isadmin'] = $isCollAdmin; 
					}
					elseif($row->colltype === 'General Observations'){
						$this->genObsArr[$row->collid]['name'] = $name;
						$this->genObsArr[$row->collid]['isadmin'] = $isCollAdmin; 
					}
					else{
						$this->collArr[$row->collid]['name'] = $name;
						$this->collArr[$row->collid]['isadmin'] = $isCollAdmin; 
					}
				}
				$rs->close();
			}
		}
	}
	
	public function getCollArr(): array
	{
		return $this->collArr;
	}

	public function getObsArr(): array
	{
		return $this->obsArr;
	}

	public function getGenObsArr(): array
	{
		return $this->genObsArr;
	}

	public function getChecklistList($clArr): array
	{
		global $IS_ADMIN;
		$returnArr = array();
		$sql = 'SELECT clid, name, access FROM fmchecklists ';
		if(!$IS_ADMIN && $clArr){
			$sql .= 'WHERE (access LIKE "public%" OR clid IN('.implode(',',$clArr).')) ';
		}
		else{
			$sql .= 'WHERE (access LIKE "public%") ';
		}
		$sql .= 'ORDER BY name';
		//echo "<div>".$sql."</div>";
		$rs = $this->conn->query($sql);
		while($row = $rs->fetch_object()){
			$clName = $row->name.($row->access === 'private'?' (limited access)':'');
			$returnArr[$row->clid] = $clName;
		}
		$rs->close();
		return $returnArr;
	}

	public function getProjectList($projArr = ''): array
	{
		$returnArr = array();
		$sql = 'SELECT p.pid, p.projname, p.managers FROM fmprojects p '.
			'WHERE p.ispublic = 1 ';
		if($projArr){
			$sql .= 'AND (p.pid IN('.implode((array)',',$projArr).')) ';
		}
		$sql .= 'ORDER BY p.projname';
		//echo '<div>'.$sql.'</div>';
		$rs = $this->conn->query($sql);
		if($rs){
			while($row = $rs->fetch_object()){
				$returnArr[$row->pid]['name'] = $row->projname;
				$returnArr[$row->pid]['managers'] = $row->managers;
			}
			$rs->close();
		}
		return $returnArr;
	}
	
	public function getSchemaVersion(): string
	{
		$result = 'No Schema Version Found';
		$sql = 'SELECT versionnumber, dateapplied FROM schemaversion ORDER BY dateapplied DESC LIMIT 1 ';
		$statement = $this->conn->prepare($sql);
		$statement->execute();
		$statement->bind_result($version,$dateapplied);
		while ($statement->fetch())  { 
			$result = $version;
		}
		$statement->close();
		return $result;		
	}
	
}
