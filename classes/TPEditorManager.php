<?php
include_once('DbConnection.php');

class TPEditorManager {

 	protected $tid;
	protected $sciName;
	protected $author;
	protected $parentTid;
	protected $family;
	protected $rankId;
	protected $language = 'English';
 	protected $submittedTid;
 	protected $submittedSciName;
	protected $taxonCon;
	protected $errorStr = '';
	
 	public function __construct(){
		$connection = new DbConnection();
 		$this->taxonCon = $connection->getConnection();
 	}
 	
 	public function __destruct(){
		if(!($this->taxonCon === null)) {
			$this->taxonCon->close();
		}
	}
 	
 	public function setTid($t){
		if(is_numeric($t)){
			$sql = 'SELECT t.tid, ts.family, t.SciName, t.Author, t.RankId, ts.ParentTID, t.SecurityStatus, ts.TidAccepted '. 
				'FROM taxstatus ts INNER JOIN taxa t ON ts.tid = t.TID '.
				'WHERE (ts.taxauthid = 1) AND (t.TID = '.$t.')';
		}
		else{
			$sql = 'SELECT t.tid, ts.family, t.SciName, t.Author, t.RankId, ts.ParentTID, t.SecurityStatus, ts.TidAccepted '. 
				'FROM taxstatus ts INNER JOIN taxa t ON ts.tid = t.TID '.
				'WHERE (ts.taxauthid = 1) AND (t.sciname = "'.$this->taxonCon->real_escape_string($t).'")';
		}
		if($sql){
			$result = $this->taxonCon->query($sql);
			if($row = $result->fetch_object()){
				if($row->tid === $row->TidAccepted){
					$this->tid = $row->tid;
					$this->sciName = $row->SciName;
					$this->family = $row->family;
					$this->author = $row->Author;
					$this->rankId = $row->RankId;
					$this->parentTid = $row->ParentTID;
				}
				else{
					$this->submittedTid = $row->tid;
					$this->submittedSciName = $row->SciName;
					$this->tid = $row->TidAccepted;
					$sqlNew = 'SELECT ts.family, t.SciName, t.Author, t.RankId, ts.ParentTID, t.SecurityStatus, ts.TidAccepted ' .
						'FROM taxstatus ts INNER JOIN taxa t ON ts.tid = t.TID ' .
						'WHERE (ts.taxauthid = 1) AND (t.TID = ' .$this->tid. ')';
					$resultNew = $this->taxonCon->query($sqlNew);
					if($rowNew = $resultNew->fetch_object()){
						$this->sciName = $rowNew->SciName;
						$this->family = $rowNew->family;
						$this->author = $rowNew->Author;
						$this->rankId = $rowNew->RankId;
						$this->parentTid = $rowNew->ParentTID;
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
		$sql = 'SELECT t2.tid, t2.SciName, ts.SortSequence ' .
			'FROM (taxa t1 INNER JOIN taxstatus ts ON t1.tid = ts.tidaccepted) ' .
			'INNER JOIN taxa t2 ON ts.tid = t2.tid ' .
			'WHERE (ts.taxauthid = 1) AND (ts.tid <> ts.TidAccepted) AND (t1.tid = ' .$this->tid. ') ' .
			'ORDER BY ts.SortSequence, t2.SciName';
		//echo $sql."<br>";
		$result = $this->taxonCon->query($sql);
		while($row = $result->fetch_object()){
			$synArr[$row->tid]['sciname'] = $row->SciName;
			$synArr[$row->tid]['sortsequence'] = $row->SortSequence;
		}
		$result->close();
 		return $synArr;
 	}
 	
	public function editSynonymSort($synSort): string
	{
		$status = '';
		foreach($synSort as $editKey => $editValue){
			if(is_numeric($editKey) && is_numeric($editValue)){
				$sql = 'UPDATE taxstatus SET SortSequence = '.$editValue.' WHERE (tid = '.$editKey.') AND (TidAccepted = '.$this->tid.')';
				//echo $sql."<br>";
				if(!$this->taxonCon->query($sql)){
					$status .= $this->taxonCon->error."\nSQL: ".$sql. ';<br/> ';
				}
			}
		}
		if($status) {
			$status = 'Errors with editVernacularSort method:<br/> ' . $status;
		}
		return $status;
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
	
	public function editVernacular($inArray): string
	{
		$editArr = $this->cleanInArray($inArray);
		$vid = $editArr['vid'];
		unset($editArr['vid']);
		$setFrag = '';
		foreach($editArr as $keyField => $value){
			$setFrag .= ','.$keyField.' = "'.$value.'" ';
		}
		$sql = 'UPDATE taxavernaculars SET '.substr($setFrag,1).' WHERE (vid = '.$this->taxonCon->real_escape_string($vid).')';
		//echo $sql;
		$status = '';
		if(!$this->taxonCon->query($sql)){
			$status = 'Error:editingVernacular: ' .$this->taxonCon->error."\nSQL: ".$sql;
		}
		return $status;
	}
	
	public function addVernacular($inArray): string
	{
		$newVerns = $this->cleanInArray($inArray);
		$sql = 'INSERT INTO taxavernaculars (tid,'.implode(',',array_keys($newVerns)).') VALUES ('.$this->getTid().',"'.implode('","',$newVerns).'")';
		//echo $sql;
		$status = '';
		if(!$this->taxonCon->query($sql)){
			$status = 'Error:addingNewVernacular: ' .$this->taxonCon->error."\nSQL: ".$sql;
		}
		return $status;
	}
	
	public function deleteVernacular($delVid): string
	{
		$status = '';
		if(is_numeric($delVid)){
			$sql = 'DELETE FROM taxavernaculars WHERE (VID = '.$delVid.')';
			//echo $sql;
			if(!$this->taxonCon->query($sql)){
				$status = 'Error:deleteVernacular: ' .$this->taxonCon->error."\nSQL: ".$sql;
			}
			else{
				$status = '';
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

 	public function setLanguage($lang): string
	{
 		return $this->language = $this->taxonCon->real_escape_string($lang);
 	}
 	
 	public function getErrorStr(): string
	{
 		return $this->errorStr;
 	}
 	
 	protected function cleanInArray($arr): array
	{
 		$newArray = array();
 		foreach($arr as $key => $value){
 			$newArray[$this->cleanInStr($key)] = $this->cleanInStr($value);
 		}
 		return $newArray;
 	}
	
	protected function cleanInStr($str){
		$newStr = trim($str);
		$newStr = preg_replace('/\s\s+/', ' ',$newStr);
		$newStr = $this->taxonCon->real_escape_string($newStr);
		return $newStr;
	}
}
