<?php
include_once(__DIR__ . '/../services/DbService.php');
include_once(__DIR__ . '/TaxonomyUtilities.php');

class TaxonomyDisplayManager{

	private $conn;
	private $taxaArr = array();
	private $targetStr = '';
	private $taxonRank = 0;
	private $displayAuthor = false;
	private $displayFullTree = false;
	private $displaySubGenera = false;

	public function __construct(){
        $connection = new DbService();
	    $this->conn = $connection->getConnection();
	}

 	public function __destruct(){
		if($this->conn) {
			$this->conn->close();
		}
	}

	public function displayTaxonomyHierarchy(): void
	{
		$hierarchyArr = $this->setTaxa();
		$this->echoTaxonArray($hierarchyArr);
	}

	private function setTaxa(): array
	{
		$subGenera = array();
		$taxaParentIndex = array();
		if($this->targetStr){
			$sql1 = 'SELECT DISTINCT t.tid, t.sciname, t.author, t.rankid, t.parenttid, t.tidaccepted, '.
                't1.sciname AS accSciname, t1.author AS accAuthor, t1.rankid AS accRankid, t1.parenttid AS accParentid '.
				'FROM taxa AS t LEFT JOIN taxa AS t1 ON t.tidaccepted = t1.tid ';
			if(is_numeric($this->targetStr)){
				$sql1 .= 'WHERE t.tidaccepted = '.$this->targetStr.' ';
			}
			else{
				if(strpos($this->targetStr, ' ') && !preg_match('/^[A-Z]+[a-z]+\s[A-Z]+/', $this->targetStr)){
					$sql1 .= 'WHERE ((t.sciname LIKE "'.$this->targetStr.'%") ';
				}
				else{
					$sql1 .= 'WHERE ((t.sciname = "'.$this->targetStr.'") ';
				}
				$sql1 .= 'OR (CONCAT(t.sciname," ",t.author) = "'.$this->targetStr.'")) ';
			}
			$sql1 .= 'ORDER BY t.rankid DESC ';
			
			//echo "<div>".$sql1."</div>"; exit;
			$rs1 = $this->conn->query($sql1);
			while($row1 = $rs1->fetch_object()){
				$tid = $row1->tid;
				$this->targetStr = $row1->sciname;
				if($tid === $row1->tidaccepted || !$row1->tidaccepted){
					$this->taxaArr[$tid]['sciname'] = $row1->sciname;
					$this->taxaArr[$tid]['author'] = $row1->author;
					$this->taxaArr[$tid]['parenttid'] = $row1->parenttid;
					$this->taxaArr[$tid]['rankid'] = $row1->rankid;
					if((int)$row1->rankid === 190) {
						$subGenera[] = $tid;
					}
					$this->taxonRank = $row1->rankid;
					$taxaParentIndex[$tid] = ($row1->parenttid?:0);
				}
				else{
					$synName = $row1->sciname;
					if($this->displayAuthor) {
						$synName .= ' ' . $row1->author;
					}
                    $this->taxaArr[$row1->tidaccepted]['sciname'] = $row1->accSciname;
                    $this->taxaArr[$row1->tidaccepted]['author'] = $row1->accAuthor;
                    $this->taxaArr[$row1->tidaccepted]['parenttid'] = $row1->accParentid;
                    $this->taxaArr[$row1->tidaccepted]['rankid'] = $row1->accRankid;
                    $this->taxonRank = $row1->accRankid;
                    $taxaParentIndex[$row1->tidaccepted] = ($row1->accParentid?:0);
					$this->taxaArr[$row1->tidaccepted]['synonyms'][$tid] = $synName;
				}
			}
			$rs1->free();
		}

		$hierarchyArr = array();
		if($this->taxaArr){
			$tidStr = implode(',',array_keys($this->taxaArr));
			$sql2 = 'SELECT DISTINCT t.tid, t.sciname, t.author, t.rankid, t.parenttid '.
				'FROM taxa AS t INNER JOIN taxaenumtree AS te ON t.tid = te.tid '.
				'WHERE t.tid = t.tidaccepted AND (te.parenttid IN('.$tidStr.') OR t.tid IN('.$tidStr.')) ';
			if($this->taxonRank < 140 && !$this->displayFullTree) {
				$sql2 .= 'AND t.rankid <= 140 ';
			}
			//echo $sql2."<br>";
			$rs2 = $this->conn->query($sql2);
			while($row2 = $rs2->fetch_object()){
				$tid = $row2->tid;
				$parentTid = $row2->parenttid;
				$this->taxaArr[$tid]['sciname'] = $row2->sciname;
				$this->taxaArr[$tid]['author'] = $row2->author;
				$this->taxaArr[$tid]['rankid'] = $row2->rankid;
				$this->taxaArr[$tid]['parenttid'] = $parentTid;
				if((int)$row2->rankid === 190) {
					$subGenera[] = $tid;
				}
				if($parentTid) {
					$taxaParentIndex[$tid] = $parentTid;
				}
			}
			$rs2->free();
			
			$sql3 = 'SELECT DISTINCT t.tid, t.sciname, t.author, t.rankid, t.parenttid '.
				'FROM taxa AS t INNER JOIN taxaenumtree AS te ON t.tid = te.parenttid '.
				'WHERE te.tid IN('.$tidStr.') ';
			//echo $sql3."<br>";
			$rs3 = $this->conn->query($sql3);
			while($row3 = $rs3->fetch_object()){
				$tid = $row3->tid;
				$parentTid = $row3->parenttid;
				$this->taxaArr[$tid]['sciname'] = $row3->sciname;
				$this->taxaArr[$tid]['author'] = $row3->author;
				$this->taxaArr[$tid]['rankid'] = $row3->rankid;
				$this->taxaArr[$tid]['parenttid'] = $parentTid;
				if((int)$row3->rankid === 190) {
					$subGenera[] = $tid;
				}
				if($parentTid) {
					$taxaParentIndex[$tid] = $parentTid;
				}
			}
			$rs3->free();
			
			$synTidStr = implode(',',array_keys($this->taxaArr));
			$sqlSyns = 'SELECT tidaccepted, tid, sciname, author, rankid FROM taxa '.
				'WHERE tid <> tidaccepted AND (tidaccepted IN('.$synTidStr.') OR tid IN('.$synTidStr.')) ';
			//echo $sqlSyns;
			$rsSyns = $this->conn->query($sqlSyns);
			while($row = $rsSyns->fetch_object()){
				$synName = $row->sciname;
				if((int)$row->rankid > 140){
					$synName = '<i>'.$row->sciname.'</i>';
				}
				if($this->displayAuthor) {
					$synName .= ' ' . $row->author;
				}
				$this->taxaArr[$row->tidaccepted]['synonyms'][$row->tid] = $synName;
			}
			$rsSyns->free();

			$orphanTaxa = array_unique(array_diff($taxaParentIndex,array_keys($taxaParentIndex)));
			if($orphanTaxa){
				$sqlOrphan = 'SELECT tid, sciname, author, parenttid, rankid FROM taxa ' .
                    'WHERE tid = tidaccepted AND tid IN(' .implode(',',$orphanTaxa). ') ';
				//echo $sqlOrphan;
				$rsOrphan = $this->conn->query($sqlOrphan);
				while($row4 = $rsOrphan->fetch_object()){
					$tid = $row4->tid;
					$taxaParentIndex[$tid] = $row4->parenttid;
					$this->taxaArr[$tid]['sciname'] = $row4->sciname;
					$this->taxaArr[$tid]['author'] = $row4->author;
					$this->taxaArr[$tid]['parenttid'] = $row4->parenttid;
					$this->taxaArr[$tid]['rankid'] = $row4->rankid;
					if((int)$row4->rankid === 190) {
						$subGenera[] = $tid;
					}
				}
				$rsOrphan->free();
			}
			
			while($leafTaxa = array_diff(array_keys($taxaParentIndex),$taxaParentIndex)){
				foreach($leafTaxa as $value){
					if(array_key_exists($value,$hierarchyArr)){
						$hierarchyArr[$taxaParentIndex[$value]][$value] = $hierarchyArr[$value];
						unset($hierarchyArr[$value]);
					}
					else{
						$hierarchyArr[$taxaParentIndex[$value]][$value] = $value;
					}
					unset($taxaParentIndex[$value]);
				}
			}
			foreach($subGenera as $subTid){
				if(!strpos($this->taxaArr[$subTid]['sciname'],'(')){
					$genusDisplay = $this->taxaArr[$this->taxaArr[$subTid]['parenttid']]['sciname'];
					$subGenusDisplay = $genusDisplay.' ('.$this->taxaArr[$subTid]['sciname'].')';
					$this->taxaArr[$subTid]['sciname'] = $subGenusDisplay;
				}
			}
			if($this->displaySubGenera && $subGenera){
				foreach($this->taxaArr as $tid => $tArr){
					if(in_array($tArr['parenttid'], $subGenera, true)){
						$sn = $this->taxaArr[$tid]['sciname'];
						$pos = strpos($sn, ' ', 2);
						if($pos) {
							$this->taxaArr[$tid]['sciname'] = $this->taxaArr[$tArr['parenttid']]['sciname'] . ' ' . trim(substr($sn, $pos));
						}
					}
				}
			}
		}
		return $hierarchyArr;
	}

	private function echoTaxonArray($node): void
	{
		if($node){
			uksort($node, array($this, 'cmp'));
			foreach($node as $key => $value){
				$taxonRankId = 0;
				if(array_key_exists($key,$this->taxaArr)){
					$sciName = $this->taxaArr[$key]['sciname'];
					$sciName = str_replace($this->targetStr, '<b>' .$this->targetStr. '</b>',$sciName);
					$taxonRankId = $this->taxaArr[$key]['rankid'];
					if((int)$this->taxaArr[$key]['rankid'] >= 180){
						$sciName = ' <i>' .$sciName. '</i> ';
					}
					if($this->displayAuthor) {
						$sciName .= ' ' . $this->taxaArr[$key]['author'];
					}
				}
				elseif($key) {
					$sciName = '<br/>Problematic Rooting (' .$key. ')';
				}
				else {
					$sciName = '&nbsp;';
				}
				$indent = $taxonRankId;
				if($indent > 230) {
					$indent -= 10;
				}
				echo '<div>' .str_repeat('&nbsp;',$indent/5);
                echo '<a href="taxonomyeditor.php?tid='.$key.'">'.$sciName.'</a>';
				if($this->taxonRank < 140 && !$this->displayFullTree && (int)$taxonRankId === 140){
					echo '<a href="index.php?target='.$sciName.'&tabindex=1">';
					echo '<i style="height:15px;width:15px;" class="fas fa-level-down-alt"></i>';
					echo '</a>';
				}
				echo '</div>';
				if(array_key_exists($key,$this->taxaArr) && array_key_exists('synonyms',$this->taxaArr[$key])){
					$synNameArr = $this->taxaArr[$key]['synonyms'];
					asort($synNameArr);
					foreach($synNameArr as $synTid => $synName){
						$synName = str_replace($this->targetStr, '<b>' .$this->targetStr. '</b>',$synName);
						echo '<div>'.str_repeat('&nbsp;',$indent/5).str_repeat('&nbsp;',7);
                        echo '[<a href="taxonomyeditor.php?tid='.$synTid.'">'.$synName.'</a>]';
						echo '</div>';
					}
				}
				if(is_array($value)){
					$this->echoTaxonArray($value);
				}
			}
		}
		else{
			echo "<div style='margin:20px;'>No taxa found matching your search</div>";
		}
	}
	
	public function setTargetStr($target): void
	{
		$this->targetStr = $this->conn->real_escape_string(ucfirst(trim($target)));
	}
	
	public function setDisplayAuthor($display): void
	{
		if($display) {
			$this->displayAuthor = true;
		}
	}

	public function setDisplayFullTree($displayTree): void
	{
		if($displayTree) {
			$this->displayFullTree = true;
		}
	}

	public function setDisplaySubGenera($displaySubg): void
	{
		if($displaySubg) {
			$this->displaySubGenera = true;
		}
	}

	private function cmp($a, $b): int
    {
		$sciNameA = (array_key_exists($a,$this->taxaArr)?$this->taxaArr[$a]['sciname']: 'unknown (' .$a. ')');
		$sciNameB = (array_key_exists($b,$this->taxaArr)?$this->taxaArr[$b]['sciname']: 'unknown (' .$b. ')');
		return strcmp($sciNameA, $sciNameB);
	}
	
	public function getTargetStr(): string
	{
		return $this->targetStr;
	}
}
