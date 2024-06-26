<?php
include_once(__DIR__ . '/../services/DbService.php');
include_once(__DIR__ . '/../services/SanitizerService.php');

class KeyCharDeficitManager{
	
	private $con;
	private $taxaCount;
	private $project;
	private $language = 'English';
	
	public function __construct(){
		$connection = new DbService();
		$this->con = $connection->getConnection();
	}
	
	public function __destruct(){
		if($this->con) {
			$this->con->close();
		}
	}
	
	public function setLanguage($lang): void
	{
		$this->language = SanitizerService::cleanInStr($this->con,$lang);
	}
	
	public function setProject($proj): void
	{
		$this->project = SanitizerService::cleanInStr($this->con,$proj);
	}
	
	public function getClQueryList(): array
	{
		$returnList = array();

		$sql = 'SELECT DISTINCT cl.Name, cl.CLID ' .
			'FROM (fmchecklists cl INNER JOIN fmchklstprojlink cpl ON cl.CLID = cpl.clid) ' .
			'INNER JOIN fmprojects p ON cpl.pid = p.pid ';
		if($this->project) {
			$sql .= 'WHERE ' . ((int)$this->project ? '(p.pid = ' . $this->project . ') ' : "(p.projname = '" . $this->project . "') ");
		}
		$sql .= 'ORDER BY cl.Name';
		$result = $this->con->query($sql);
		while($row = $result->fetch_object()){
			$returnList[$row->CLID] = $row->Name;
		}
		$result->free();
		return $returnList;
	}
	
	public function getTaxaQueryList(): array
	{
		$returnList = array();

		$sql = 'SELECT DISTINCT t.RankId, t.SciName, t.TID ' .
			'FROM taxa t INNER JOIN kmchartaxalink ctl ON t.TID = ctl.TID ' .
			'ORDER BY t.RankId, t.SciName';

		$result = $this->con->query($sql);
		while($row = $result->fetch_object()){
			$returnList[$row->TID] = $row->SciName;
		}
		$result->free();
		return $returnList;
	}

	public function getCharList($cfVal, $cidVal): array
	{
		$returnArray = array();
		if($cfVal){
			$strFrag = implode(',',$this->getParents($cfVal));
			
			$sql = 'SELECT DISTINCT ch.headingname, c.CID, c.CharName ' .
				'FROM (kmchartaxalink ctl INNER JOIN kmcharacters c ON ctl.CID = c.CID) INNER JOIN kmcharheading ch ON c.hid = ch.hid ' .
				"WHERE (((c.CID) Not In (SELECT DISTINCT CID FROM kmchartaxalink WHERE ((TID In ($strFrag)) ".
				"AND (Relation='exclude')))) AND ((c.chartype)='UM' Or (c.chartype)='OM') AND (c.defaultlang='".
				$this->language."') AND (ch.language='".$this->language."') AND (ctl.TID In ($strFrag))) ".
				'ORDER BY c.hid, c.CID';
			//echo $sql;
			$headingArray = array();
			$result = $this->con->query($sql);
			while($row = $result->fetch_object()){
				$headingArray[$row->headingname][$row->CID] = $row->CharName;
			}
			$result->close();
			
			ksort($headingArray);
			foreach($headingArray as $h => $charData){
				$returnArray[] = "<div style='margin-top:1em;'>$h</div>";
				ksort($charData);
				foreach($charData as $cidKey => $charValue){
					$returnArray[] = "<div> <input name='cid' type='radio' value='".$cidKey."' ".($cidKey === $cidVal? 'checked' : '').">$charValue</div>";
				}
			}
		}
		return $returnArray;
	}
	
	private function getParents($t): array
	{
		$parentList = array();
		$targetTid = $t;
		$parentList[] = $targetTid;
		while($targetTid){
			$sql = 'SELECT parenttid FROM taxa WHERE TID = '.$targetTid.' ';
			//echo $sql;
			$result = $this->con->query($sql);
		    if ($row = $result->fetch_object()){
		    	if($targetTid === $row->parenttid){
		    		break;
		    	}
				$targetTid = $row->parenttid;
				if($targetTid) {
					$parentList[] = $targetTid;
				}
		    }
			$result->close();
		}
		return $parentList;
	}
	
	public function getTaxaList($cidVal, $cfVal, $clVal): array
	{
		$returnArray = array();
		$sppStr = $this->getChildren($cidVal, $cfVal, $clVal);
		$sql = 'SELECT DISTINCT t.TID, t.family, t.SciName FROM taxa AS t ' .
			'LEFT JOIN (SELECT DISTINCT d1.TID FROM kmdescr AS d1 WHERE (d1.CID = ' .$cidVal. ')) AS d ON t.TID = d.TID ' .
			'WHERE t.TID IN(' .$sppStr. ') AND ISNULL(d.TID) ' .
			'ORDER BY t.family, t.SciName';
		//echo $sql;
		$result = $this->con->query($sql);
		$this->taxaCount = 0;
		while($row = $result->fetch_object()){
			$returnArray[$row->family][$row->TID] = $row->SciName;
			$this->taxaCount++;
		}
		$result->free();
		return $returnArray;
	}
	
	private function getChildren($cidVal, $cfVal, $clVal): string
	{
		$excludeArray = array();
 		$sqlEx = 'SELECT c.TID FROM kmchartaxalink c WHERE (c.CID = '.$cidVal.") AND c.Relation = 'exclude'";
		$resultEx = $this->con->query($sqlEx);
		while($row = $resultEx->fetch_object()){
 			$excludeArray[] = $row->TID;
		}
		$excludeStr = implode(',',$excludeArray);
		$resultEx->close();
		
		$children = array();
		$targetStr = $cfVal;
		do{
			if(isset($targetList)) {
				unset($targetList);
			}
			$targetList = array();
			$sql = 'SELECT DISTINCT t.TID, t.rankid, cl.clid FROM taxa AS t ' .
				'LEFT JOIN (SELECT ctl.tid, ctl.clid From fmchklsttaxalink ctl WHERE ctl.clid = ' .$clVal. ') AS cl ON t.TID = cl.tid ' .
				'WHERE t.parenttid IN(' .$targetStr. ') ';
			if($excludeStr) {
				$sql .= 'AND t.TID NOT IN(' . $excludeStr . ') ';
			}
			//echo $sql."<br/><br/>";
			$rankId = 0;
			$result = $this->con->query($sql);
			while($row = $result->fetch_object()){
				$rankId = $row->rankid;
				$targetList[] = $row->TID;
				if($rankId === 220 && $row->clid) {
					$children[] = $row->TID;
				}
			}
			if($targetList){
				$targetStr = implode(',', $targetList);
			}
		}while($targetList && $rankId > 10);
		return implode(',',$children);
	}

	public function getTaxaCount(){
		return $this->taxaCount;
	}
}
