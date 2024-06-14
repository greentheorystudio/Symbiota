<?php
include_once(__DIR__ . '/../services/DbConnectionService.php');
include_once(__DIR__ . '/../services/SanitizerService.php');

class TPEditorManager {

 	protected $tid;
	protected $sciName;
	protected $author;
	protected $parentTid;
	protected $family;
	protected $rankId;
	protected $submittedTid;
 	protected $submittedSciName;
	protected $taxonCon;
	protected $errorStr = '';
	
 	public function __construct(){
		$connection = new DbConnectionService();
 		$this->taxonCon = $connection->getConnection();
 	}
 	
 	public function __destruct(){
		if(!($this->taxonCon === null)) {
			$this->taxonCon->close();
		}
	}
 	
 	public function setTid($t){
		if(is_numeric($t)){
			$sql = 'SELECT tid, family, SciName, Author, RankId, parenttid, SecurityStatus, tidaccepted '.
				'FROM taxa WHERE TID = '.$t.' ';
		}
		else{
			$sql = 'SELECT tid, family, SciName, Author, RankId, parenttid, SecurityStatus, tidaccepted '.
				'FROM taxa WHERE sciname = "'.$this->taxonCon->real_escape_string($t).'" ';
		}
		if($sql){
			$result = $this->taxonCon->query($sql);
			if($row = $result->fetch_object()){
				if($row->tid === $row->tidaccepted){
					$this->tid = $row->tid;
					$this->sciName = $row->SciName;
					$this->family = $row->family;
					$this->author = $row->Author;
					$this->rankId = $row->RankId;
					$this->parentTid = $row->parenttid;
				}
				else{
					$this->submittedTid = $row->tid;
					$this->submittedSciName = $row->SciName;
					$this->tid = $row->tidaccepted;
					$sqlNew = 'SELECT family, SciName, Author, RankId, parenttid, SecurityStatus, tidaccepted ' .
						'FROM taxa WHERE TID = ' .$this->tid. ' ';
					$resultNew = $this->taxonCon->query($sqlNew);
					if($rowNew = $resultNew->fetch_object()){
						$this->sciName = $rowNew->SciName;
						$this->family = $rowNew->family;
						$this->author = $rowNew->Author;
						$this->rankId = $rowNew->RankId;
						$this->parentTid = $rowNew->parenttid;
					}
					$resultNew->close();
				}
			}
		    else{
		    	$this->sciName = 'unknown';
		    }
		    $result->free();
		}
		return $this->tid;
 	}
 	
 	public function getTid(){
 		return $this->tid;
 	}
 	
 	public function getSciName(){
 		return $this->sciName;
 	}
	
 	public function getSubmittedTid(){
 		return $this->submittedTid;
 	}
 	
 	public function getSubmittedSciName(){
 		return $this->submittedSciName;
 	}

	public function getSynonym(): array
	{
 		$synArr = array();
		$sql = 'SELECT t2.tid, t2.SciName ' .
			'FROM taxa AS t1 INNER JOIN taxa AS t2 ON t1.tidaccepted = t2.tid ' .
			'WHERE t1.tid <> t1.tidaccepted AND t1.tid = ' .$this->tid. ' ' .
			'ORDER BY t2.SciName';
		//echo $sql."<br>";
		$result = $this->taxonCon->query($sql);
		while($row = $result->fetch_object()){
			$synArr[$row->tid]['sciname'] = $row->SciName;
		}
		$result->close();
 		return $synArr;
 	}
 	
	public function getVernaculars(): array
	{
		$vernArr = array();
		$sql = 'SELECT v.VID, v.VernacularName, v.Language, v.Source, v.username, v.notes, v.SortSequence ' .
			'FROM taxavernaculars v ' .
			'WHERE (v.tid = ' .$this->tid. ') ';
		$sql .= 'ORDER BY v.Language, v.SortSequence';
		$result = $this->taxonCon->query($sql);
		$vernCnt = 0;
		while($row = $result->fetch_object()){
			$lang = $row->Language;
			$vernArr[$lang][$vernCnt]['vid'] = $row->VID;
			$vernArr[$lang][$vernCnt]['vernacularname'] = $row->VernacularName;
			$vernArr[$lang][$vernCnt]['source'] = $row->Source;
			$vernArr[$lang][$vernCnt]['username'] = $row->username;
			$vernArr[$lang][$vernCnt]['notes'] = $row->notes;
			$vernArr[$lang][$vernCnt]['language'] = $row->Language;
			$vernArr[$lang][$vernCnt]['sortsequence'] = $row->SortSequence;
			$vernCnt++;
		}
		$result->close();
		return $vernArr;
	}
	
	public function addVernacular($inArray): string
	{
		$newVerns = SanitizerService::cleanInArray($this->taxonCon,$inArray);
		$sql = 'INSERT INTO taxavernaculars (tid,'.implode(',',array_keys($newVerns)).') VALUES ('.$this->getTid().',"'.implode('","',$newVerns).'")';
		//echo $sql;
		$status = '';
		if(!$this->taxonCon->query($sql)){
			$status = 'Error:addingNewVernacular.';
		}
		return $status;
	}
	
	public function deleteVernacular($delVid): string
	{
		$status = '';
		if(is_numeric($delVid)){
			$sql = 'DELETE FROM taxavernaculars WHERE (VID = '.$delVid.')';
			//echo $sql;
			if($this->taxonCon->query($sql)) {
				$status = '';
			}
			else {
				$status = 'Error:deleteVernacular.';
			}
		}
		return $status;
	}

	public function getAuthor(){
 		return $this->author;
 	}
 
 	public function getFamily(){
 		return $this->family;
 	}
 
 	public function getRankId(){
 		return $this->rankId;
 	}
 
 	public function getParentTid(){
 		return $this->parentTid;
 	}

 	public function getErrorStr(): string
	{
 		return $this->errorStr;
 	}
}
