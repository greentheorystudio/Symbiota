<?php
include_once(__DIR__ . '/../services/DbConnectionService.php');

class SiteMapManager{
	
	private $conn;
	private $collArr = array();
	private $obsArr = array();
	private $genObsArr = array();
	
	public function __construct() {
		$connection = new DbConnectionService();
		$this->conn = $connection->getConnection();
	}

	public function __destruct(){
 		if($this->conn) {
			$this->conn->close();
		}
	}

	public function setCollectionList(): void
	{
		$adminArr = array();
		$editorArr = array();
		$sql = 'SELECT c.collid, CONCAT_WS(":",c.institutioncode, c.collectioncode) AS ccode, c.collectionname, c.colltype '.
			'FROM omcollections AS c ';
		if(!$GLOBALS['IS_ADMIN']){
			if(array_key_exists('CollAdmin',$GLOBALS['USER_RIGHTS'])){
				$adminArr = $GLOBALS['USER_RIGHTS']['CollAdmin'];
			}
			if(array_key_exists('CollEditor',$GLOBALS['USER_RIGHTS'])){
				$editorArr = $GLOBALS['USER_RIGHTS']['CollEditor'];
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
					$isCollAdmin = ($GLOBALS['IS_ADMIN']|| in_array($row->collid, $adminArr, true) ?1:0);
					if($row->colltype === 'HumanObservation'){
						$this->obsArr[$row->collid]['name'] = $name;
						$this->obsArr[$row->collid]['isadmin'] = $isCollAdmin; 
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

	public function getProjectList($projArr = null): array
	{
		$returnArr = array();
		$sql = 'SELECT p.pid, p.projname, p.managers FROM fmprojects AS p '.
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

    public function hasGlossary(): bool
    {
        $bool = false;
        if($rs = $this->conn->query('SELECT glossid FROM glossary LIMIT 1')){
            if($rs->fetch_object()) {
                $bool = true;
            }
            $rs->free();
        }
        return $bool;
    }
}
