<?php
include_once('KeyManager.php');

class KeyMassUpdate extends KeyManager{
	
	private $clid;
	private $cid;
	private $taxaArr = array();
	private $stateArr = array();
	private $descrArr = array();
	private $headerStr;
	private $cnt = 0;

	public function getCharList($tidFilter): array
	{
		$headingArray = array();
		$sql = 'SELECT DISTINCT ch.headingname, c.CID, c.CharName ' .
			'FROM kmcharacters c INNER JOIN kmchartaxalink ctl ON c.CID = ctl.CID ' .
			'INNER JOIN kmcharheading ch ON c.hid = ch.hid ' .
			'LEFT JOIN kmchardependance cd ON c.CID = cd.CID ' .
			"WHERE ch.language = '".$this->language."' AND (ctl.Relation = 'include') ".
			"AND (c.chartype='UM' OR c.chartype='OM') AND (c.defaultlang='".$this->language."') ";
		if($tidFilter){
			$strFrag = implode(',',$this->getParentArr($tidFilter)).','.$tidFilter;
			$sql .= 'AND (ctl.TID In ('.$strFrag.')) ';
		}
		$sql .= 'ORDER BY c.hid, c.SortSequence, c.CharName';
		//echo $sql;
		$result = $this->conn->query($sql);
		while($row = $result->fetch_object()){
			$headingArray[$row->headingname][$row->CID] = $row->CharName;
		}
		$result->free();
		return $headingArray;
	}
	
	private function setStates(): void
	{
		$sql = 'SELECT charstatename, cs FROM kmcs WHERE (cid = '.$this->cid.') ';
		$rs = $this->conn->query($sql);
		while($row = $rs->fetch_object()){
			$this->stateArr[$row->cs] = $row->charstatename;
		}
		$rs->free();
		ksort($this->stateArr);
	}

	public function echoTaxaList($tidFilter, $generaOnly = false): void
	{
		$tidArr = array();
		
		$sqlBase = '';
		$sqlWhere = '';
		if($tidFilter){
			$sqlBase .= 'INNER JOIN taxaenumtree e ON ts.tid = e.tid ';
			$sqlWhere .= 'AND (e.taxauthid = '.$this->taxAuthId.') AND (e.parenttid = '.$tidFilter.') ';
		}
		if(!$generaOnly){
			$sql = 'SELECT DISTINCT t.tid, t.sciname, ts2.parenttid '.
				'FROM taxa t INNER JOIN taxstatus ts ON t.tid = ts.tidaccepted '.
				'INNER JOIN taxstatus ts2 ON t.tid = ts2.tid '.
				'INNER JOIN fmchklsttaxalink c ON ts.tid = c.tid '.$sqlBase.
				'WHERE (ts.taxauthid = '.$this->taxAuthId.') AND (ts2.taxauthid = '.$this->taxAuthId.') AND (t.rankid = 220) AND (c.clid = '.$this->clid.') '.$sqlWhere;
			//echo $sql; exit;
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$this->taxaArr[$r->parenttid][$r->tid] = $r->sciname;
				$tidArr[] = $r->tid;
			}
			$rs->free();
		}
		
		$famArr = array();
		$sql2 = 'SELECT DISTINCT t.tid, t.sciname, ts2.parenttid, t.rankid '.
			'FROM taxa t INNER JOIN taxaenumtree e2 ON t.tid = e2.parenttid '.
			'INNER JOIN taxstatus ts2 ON t.tid = ts2.tid '.
			'INNER JOIN taxstatus ts ON e2.tid = ts.tidaccepted '.
			'INNER JOIN fmchklsttaxalink c ON ts.tid = c.tid '.$sqlBase.
			'WHERE (ts.taxauthid = '.$this->taxAuthId.') AND (ts2.taxauthid = '.$this->taxAuthId.') AND (e2.taxauthid = '.$this->taxAuthId.') '.
			'AND (t.rankid <= 220) AND (t.rankid >= 140) AND (c.clid = '.$this->clid.') '.$sqlWhere;
		//echo $sql2; exit;
		$rs2 = $this->conn->query($sql2);
		while($r2 = $rs2->fetch_object()){
			if($r2->rankid == 140){
				$famArr[$r2->tid] = $r2->sciname;
			}
			elseif(!$generaOnly || $r2->rankid < 220){
				$this->taxaArr[$r2->parenttid][$r2->tid] = $r2->sciname;
			}
			$tidArr[] = $r2->tid;
		}
		$rs2->free();

		if($tidArr){
			$sql3 = 'SELECT tid, cid, cs, inherited FROM kmdescr '.
				'WHERE (cid='.$this->cid.') AND (tid IN('.implode(',',$tidArr).'))';
			$rs3 = $this->conn->query($sql3);
			while($r3 = $rs3->fetch_object()){
				$this->descrArr[$r3->tid][$r3->cs] = ($r3->inherited?1:0);
			}
			$rs3->free();
		}
		
		$this->setStates();
		$this->headerStr = '<tr>';
 		foreach($this->stateArr as $cs => $csName){
 			$this->headerStr .= '<th>'.str_replace(' ', '<br/>',$csName).'</th>';
 		}
 		$this->headerStr .= '</tr>'."\n";
 		$this->headerStr .= '<tr><td style="text-align:right;" colspan="'.(count($this->stateArr)+1).'"><input type="submit" name="action" value="Save Changes" onclick="submitAttrs()" /></td></tr>';
 		echo $this->headerStr;
		
		foreach($famArr as $famTid => $family){
			$this->echoTaxaRow($famTid,$family);
			$this->processTaxa($famTid);
		}
		echo $this->headerStr;
	}
	
	private function processTaxa($tid,$indent=0): void
	{
		if(isset($this->taxaArr[$tid])){
			$indent++;
			$childArr = $this->taxaArr[$tid];
			asort($childArr);
			foreach($childArr as $childTid => $childSciname){
				$this->echoTaxaRow($childTid,$childSciname,$indent);
				$this->processTaxa($childTid,$indent);
			}
		}
	}

	private function echoTaxaRow($tid,$sciname,$indent = 0): void
	{
		echo '<tr><td>';
		echo '<span style="margin-left:'.($indent*10).'px"><b>'.($indent?'<i>':'').$sciname.($indent?'</i>':'').'</b></span>';
		echo '<a href="editor.php?tid='.$tid.'" target="_blank"><img src="../../images/edit.png" /></a>';
		echo '</td>';
		foreach($this->stateArr as $cs => $csName){
			$isSelected = false;
			$isInherited = false;
			if(isset($this->descrArr[$tid][$cs])){
				$isSelected = true;
				if($this->descrArr[$tid][$cs]) {
					$isInherited = true;
				}
			}
			if($isSelected && !$isInherited){
				$jsStr = "removeAttr('".$tid. '-' .$cs."');";
			}
			else{
				$jsStr = "addAttr('".$tid. '-' .$cs."');";
			}
			echo '<td style="text-align:center;width:15px;white-space:nowrap;">';
			echo '<input type="checkbox" name="csDisplay" onclick="'.$jsStr.'" '.($isSelected && !$isInherited?'CHECKED':'').' title="'.$csName.'"/>'.($isInherited?'(I)':'');
			echo "</td>\n";
		}
		echo '</tr>';
		$this->cnt++;
		if($this->cnt%40 == 0) {
			echo $this->headerStr;
		}
	}
	
	public function processAttributes($rAttrs,$aAttrs): void
	{
		$removeArr = $this->processAttrArr($rAttrs);
		$addArr = $this->processAttrArr($aAttrs);
		$tidUsedStr = implode(',',array_unique(array_merge(array_keys($removeArr),array_keys($addArr))));
		if($tidUsedStr){
			$this->deleteInheritance($tidUsedStr,$this->cid);
			if($removeArr) {
				$this->processRemoveAttributes($removeArr);
			}
			if($addArr) {
				$this->processAddAttributes($addArr);
			}
			$this->resetInheritance($tidUsedStr,$this->cid);
		}
	}
	
	private function processAttrArr($inputArr): array
	{
		$retArr = array();
		if($inputArr){
			foreach($inputArr as $v){
	 			if($v){
					$t = explode('-',$v);
					$retArr[$t[0]][] = $t[1];
	 			}
	 		}
		}
 		return $retArr;
	}

	private function processRemoveAttributes($inputArr): void
	{
 		foreach($inputArr as $tid => $csArr){
 			foreach($csArr as $cs){
				$this->deleteDescr($tid, $this->cid, $cs);
 			}
 		}
	}

	private function processAddAttributes($addArr): void
	{
 		foreach($addArr as $tid => $csArr){
 			foreach($csArr as $cs){
				$this->insertDescr($tid, $this->cid, $cs);
 			}
 		}
	}

	public function getTaxaQueryList(): array
	{
		$retArr = array();
		$sql = 'SELECT DISTINCT t.tid, t.sciname '. 
			'FROM fmchklsttaxalink c INNER JOIN taxaenumtree e ON c.tid = e.tid '.
			'INNER JOIN taxa t ON e.parenttid = t.tid '.
			'WHERE (c.clid = '.$this->clid.') AND (t.rankid >= 140) AND (t.rankid < 180) AND (e.taxauthid = 1) '.
			'ORDER BY t.sciname ';
		//echo $sql;
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[$r->tid] = $r->sciname;
		}
		$rs->free();
		return $retArr;
	}

	public function setCid($cid): void
	{
		if(is_numeric($cid)) {
			$this->cid = $cid;
		}
	}

	public function setClid($clid): void
	{
		if(is_numeric($clid)) {
			$this->clid = $clid;
		}
	}
}
