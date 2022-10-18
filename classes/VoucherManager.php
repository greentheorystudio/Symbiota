<?php
include_once(__DIR__ . '/DbConnection.php');
include_once(__DIR__ . '/Sanitizer.php');
 
class VoucherManager {

	private $conn;
	private $tid;
	private $taxonName;
	private $clid;
	private $clName;

	public function __construct() {
		$connection = new DbConnection();
		$this->conn = $connection->getConnection();
 	}
	
 	public function __destruct(){
 		if($this->conn) {
			$this->conn->close();
		}
	}

 	public function setTid($t): void
	{
        $this->tid = $t;
 	}
	
	public function getTid(){
		return $this->tid;
	}
	
	public function getTaxonName(){
		return $this->taxonName;
	}
	
	public function setClid($id): void
	{
		if(is_numeric($id)){
			$this->clid = $this->conn->real_escape_string($id);
		}
	}
	
	public function getClid(){
		return $this->clid;
	}
	
	public function getClName(){
		return $this->clName;
	}
	
	public function getChecklistData(): array
	{
 		$checklistData = array();
 		if(!$this->tid || !$this->clid) {
			return $checklistData;
		}
		$sql = 'SELECT t.SciName, cllink.Habitat, cllink.Abundance, cllink.Notes, cllink.internalnotes, cllink.source, cllink.familyoverride, ' .
			'cl.Name, cl.type, cl.locality ' .
			'FROM (fmchecklists cl INNER JOIN fmchklsttaxalink cllink ON cl.CLID = cllink.CLID) ' .
			'INNER JOIN taxa t ON cllink.TID = t.TID ' .
			'WHERE ((cllink.TID = ' .$this->tid. ') AND (cllink.CLID = ' .$this->clid. '))';
 		$result = $this->conn->query($sql);
		if($row = $result->fetch_object()){
			$checklistData['habitat'] = Sanitizer::cleanOutStr($row->Habitat);
			$checklistData['abundance'] = Sanitizer::cleanOutStr($row->Abundance);
			$checklistData['notes'] = Sanitizer::cleanOutStr($row->Notes);
			$checklistData['internalnotes'] = Sanitizer::cleanOutStr($row->internalnotes);
			$checklistData['source'] = Sanitizer::cleanOutStr($row->source);
			$checklistData['familyoverride'] = Sanitizer::cleanOutStr($row->familyoverride);
			$checklistData['cltype'] = $row->type;
			$checklistData['locality'] = $row->locality;
			if(!$this->clName) {
				$this->clName = Sanitizer::cleanOutStr($row->Name);
			}
			if(!$this->taxonName) {
				$this->taxonName = Sanitizer::cleanOutStr($row->SciName);
			}
		}
		$result->close();
		return $checklistData;
	}

	public function editClData($eArr): string
	{
		$retStr = '';
		$innerSql = '';
		foreach($eArr as $k => $v){
			$valStr = trim($v);
			$innerSql .= ',' .$k. '=' .($valStr?'"'.Sanitizer::cleanInStr($this->conn,$valStr).'" ':'NULL');
		}
		$sqlClUpdate = 'UPDATE fmchklsttaxalink SET '.substr($innerSql,1).
			' WHERE (tid = '.$this->tid.') AND (clid = '.$this->clid.')';
		if(!$this->conn->query($sqlClUpdate)){
			$retStr = 'ERROR editing details.';
		}
		return $retStr;
	}

	public function renameTaxon($newTaxon, $rareLocality = null): string
	{
		$statusStr = '';
        $habitatSource = '';
        $abundSource = '';
        $notesSource = '';
        $internalNotesSource = '';
        $sourceSource = '';
        $nativeSource = '';
		$nTaxon = $this->conn->real_escape_string($newTaxon);
		if(is_numeric($nTaxon)){
			$sql = 'UPDATE fmchklsttaxalink SET TID = '.$nTaxon.' '.
				'WHERE (TID = '.$this->tid.') AND (CLID = '.$this->clid.')';
			if($this->conn->query($sql)){
				$this->tid = $nTaxon;
				$this->taxonName = '';
			}
			else{
				$sqlTarget = 'SELECT cllink.Habitat, cllink.Abundance, cllink.Notes, cllink.internalnotes, cllink.source, cllink.Nativity ' .
					'FROM fmchklsttaxalink cllink WHERE (TID = ' .$nTaxon. ') AND (CLID = ' .$this->clid.')';
				$rsTarget = $this->conn->query($sqlTarget);
				if($row = $rsTarget->fetch_object()){
					$habitatTarget = Sanitizer::cleanInStr($this->conn,$row->Habitat);
					$abundTarget = Sanitizer::cleanInStr($this->conn,$row->Abundance);
					$notesTarget = Sanitizer::cleanInStr($this->conn,$row->Notes);
					$internalNotesTarget = Sanitizer::cleanInStr($this->conn,$row->internalnotes);
					$sourceTarget = Sanitizer::cleanInStr($this->conn,$row->source);
					$nativeTarget = Sanitizer::cleanInStr($this->conn,$row->Nativity);
				
					$sqlVouch = 'UPDATE IGNORE fmvouchers SET TID = '.$nTaxon.' '.
						'WHERE (TID = '.$this->tid.') AND (CLID = '.$this->clid.')';
					if(!$this->conn->query($sqlVouch)){
						$statusStr = 'ERROR transferring vouchers during taxon transfer.';
					}
					$sqlVouchDel = 'DELETE FROM fmvouchers v '.
						'WHERE (v.CLID = '.$this->clid.') AND (v.TID = '.$this->tid.')';
					if(!$this->conn->query($sqlVouchDel)){
						$statusStr = 'ERROR removing vouchers during taxon transfer.';
					}
					
					$sqlSourceCl = 'SELECT ctl.Habitat, ctl.Abundance, ctl.Notes, ctl.internalnotes, ctl.source, ctl.Nativity ' .
						'FROM fmchklsttaxalink ctl WHERE (ctl.TID = ' .$this->tid. ') AND (ctl.CLID = ' .$this->clid.')';
					$rsSourceCl =  $this->conn->query($sqlSourceCl);
					if($row = $rsSourceCl->fetch_object()){
						$habitatSource = Sanitizer::cleanInStr($this->conn,$row->Habitat);
						$abundSource = Sanitizer::cleanInStr($this->conn,$row->Abundance);
						$notesSource = Sanitizer::cleanInStr($this->conn,$row->Notes);
						$internalNotesSource = Sanitizer::cleanInStr($this->conn,$row->internalnotes);
						$sourceSource = Sanitizer::cleanInStr($this->conn,$row->source);
						$nativeSource = Sanitizer::cleanInStr($this->conn,$row->Nativity);
					}
					$rsSourceCl->close();
					$habitatStr = $habitatTarget.(($habitatTarget && $habitatSource)? '; ' : '').$habitatSource;
					$abundStr = $abundTarget.(($abundTarget && $abundSource)? '; ' : '').$abundSource;
					$notesStr = $notesTarget.(($notesTarget && $notesSource)? '; ' : '').$notesSource;
					$internalNotesStr = $internalNotesTarget.(($internalNotesTarget && $internalNotesSource)? '; ' : '').$internalNotesSource;
					$sourceStr = $sourceTarget.(($sourceTarget && $sourceSource)? '; ' : '').$sourceSource;
					$nativeStr = $nativeTarget.(($nativeTarget && $nativeSource)? '; ' : '').$nativeSource;
					$sqlCl = 'UPDATE fmchklsttaxalink SET Habitat = "'.Sanitizer::cleanInStr($this->conn,$habitatStr).'", '. 
						'Abundance = "'.Sanitizer::cleanInStr($this->conn,$abundStr).'", Notes = "'.Sanitizer::cleanInStr($this->conn,$notesStr).
						'", internalnotes = "'.Sanitizer::cleanInStr($this->conn,$internalNotesStr).'", source = "'.
						Sanitizer::cleanInStr($this->conn,$sourceStr).'", Nativity = "'.Sanitizer::cleanInStr($this->conn,$nativeStr).'" '.
						'WHERE (TID = '.$nTaxon.') AND (CLID = '.$this->clid.')';
					if($this->conn->query($sqlCl)){
						$sqlDel = 'DELETE FROM fmchklsttaxalink WHERE (CLID = '.$this->clid.') AND (TID = '.$this->tid.')';
						if($this->conn->query($sqlDel)){
							$this->tid = $nTaxon;
							$this->taxonName = '';
						}
						else{
							$statusStr = 'ERROR removing taxon during taxon transfer.';
						}
					}
					else{
						$statusStr = 'ERROR updating new taxon during taxon transfer.';
					}
				}
				$rsTarget->close();
			}
			if($rareLocality){
				$this->setStateRare($rareLocality);
			}
		}
		return $statusStr;
	}
	
	public function deleteTaxon($rareLocality = null): string
	{
		$statusStr = '';
		$vSql = 'DELETE v.* FROM fmvouchers v WHERE (v.tid = '.$this->tid.') AND (v.clid = '.$this->clid.')';
		$this->conn->query($vSql);
		$sql = 'DELETE ctl.* FROM fmchklsttaxalink ctl WHERE (ctl.tid = '.$this->tid.') AND (ctl.clid = '.$this->clid.')';
		if($this->conn->query($sql)){
			if($rareLocality){
				$this->setStateRare($rareLocality);
			}
		}
		else{
			$statusStr = 'ERROR deleting taxon from checklist.';
		}
		return $statusStr;
	}
	
	private function setStateRare($rareLocality): void
	{
		$sql = 'SELECT IFNULL(securitystatus,0) as securitystatus FROM taxa WHERE tid = '.$this->tid;
		//echo $sql;
		$rs = $this->conn->query($sql);
		if(($r = $rs->fetch_object()) && $r->securitystatus === 0) {
			$sqlRare = 'UPDATE omoccurrences o INNER JOIN taxstatus ts1 ON o.tidinterpreted = ts1.tid '.
				'INNER JOIN taxstatus ts2 ON ts1.tidaccepted = ts2.tidaccepted '.
				'SET o.localitysecurity = NULL '.
				'WHERE (o.localitysecurity = 1) AND (o.localitySecurityReason IS NULL) '.
				'AND o.stateprovince = "'.$rareLocality.'" AND ts2.tid = '.$this->tid;
			//echo $sqlRare; exit;
			if(!$this->conn->query($sqlRare)){
				$statusStr = 'ERROR resetting locality security during taxon delete.';
			}
		}
		$rs->free();
	}

	public function getVoucherData(): array
	{
		$voucherData = array();
 		if(!$this->tid || !$this->clid) {
			return $voucherData;
		}
		$sql = 'SELECT v.occid, CONCAT_WS(" ",o.recordedby,o.recordnumber) AS collector, o.catalognumber, '.
			'o.sciname, o.eventdate, v.notes, v.editornotes '.
			'FROM fmvouchers v INNER JOIN omoccurrences o ON v.occid = o.occid '.
			'WHERE (v.TID = '.$this->tid.') AND (v.CLID = '.$this->clid.')';
		$result = $this->conn->query($sql);
		while($row = $result->fetch_object()){
			$occId = $row->occid;
			$voucherData[$occId]['collector'] = $row->collector;
			$voucherData[$occId]['catalognumber'] = $row->catalognumber;
			$voucherData[$occId]['sciname'] = $row->sciname;
			$voucherData[$occId]['eventdate'] = $row->eventdate;
			$voucherData[$occId]['notes'] = $row->notes;
			$voucherData[$occId]['editornotes'] = $row->editornotes;
		}
		$result->close();
		return $voucherData;
	}
	
	public function editVoucher($occid, $notes, $editorNotes): string
	{
		$statusStr = '';
		if($this->tid && $this->clid && is_numeric($occid)){
			$sql = 'UPDATE fmvouchers SET '.
				'notes = '.($notes?'"'.Sanitizer::cleanInStr($this->conn,$notes).'"':'NULL').
				',editornotes = '.($editorNotes?'"'.Sanitizer::cleanInStr($this->conn,$editorNotes).'"':'NULL').
				' WHERE (occid = '.$occid.') AND (tid = '.$this->tid.') AND (clid = '.$this->clid.')';
			//echo $sql;
			if(!$this->conn->query($sql)){
				$statusStr = 'ERROR editing voucher.';
			}
		}
		return $statusStr;
	}
	
	public function addVoucher($vOccId, $vNotes, $vEditNotes): ?string
	{
		$returnStr = '';
	    $vNotes = Sanitizer::cleanInStr($this->conn,$vNotes);
		$vEditNotes = Sanitizer::cleanInStr($this->conn,$vEditNotes);
		if(is_numeric($vOccId) && $vOccId && $this->clid) {
            $status = $this->addVoucherRecord($vOccId, $vNotes, $vEditNotes);
            if($status){
                $sqlInsertCl = 'INSERT INTO fmchklsttaxalink ( clid, TID ) '.
                    'SELECT '.$this->clid.' AS clid, o.TidInterpreted '.
                    'FROM omoccurrences o WHERE (o.occid = '.$vOccId.')';
                //echo "<div>sqlInsertCl: ".$sqlInsertCl."</div>";
                if($this->conn->query($sqlInsertCl)){
                    $returnStr = $this->addVoucherRecord($vOccId, $vNotes, $vEditNotes);
                }
            }
        }
		return $returnStr;
	}

	private function addVoucherRecord($vOccId, $vNotes, $vEditNotes): string
	{
		$returnStr = 'ERROR: Neither the target taxon nor a sysnonym is present in this checklists. Taxon needs to be added.';
	    $sql = 'SELECT DISTINCT o.occid, ctl.tid, ctl.clid, o.recordedby, o.recordnumber, '.
			'"'.$vNotes.'" AS Notes, "'.$vEditNotes.'" AS editnotes '.
			'FROM ((omoccurrences o INNER JOIN taxstatus ts1 ON o.TidInterpreted = ts1.tid) '.
			'INNER JOIN taxstatus ts2 ON ts1.tidaccepted = ts2.tidaccepted) '.
			'INNER JOIN fmchklsttaxalink ctl ON ts2.tid = ctl.tid '.
			'WHERE (ctl.clid = '.$this->clid.') AND (o.occid = '.
			$vOccId.') '.
			'LIMIT 1';
		//echo "addVoucherSql: ".$sql."<br/>";
		$rs = $this->conn->query($sql);
		if($row = $rs->fetch_object()){
			$occId = $row->occid;
			$notes = Sanitizer::cleanInStr($this->conn,$row->Notes);
			$editNotes = Sanitizer::cleanInStr($this->conn,$row->editnotes);
			
			$sqlInsert = 'INSERT INTO fmvouchers ( occid, TID, CLID, Notes, editornotes ) '.
				'VALUES ('.$occId.','.$row->tid.','.$row->clid.',"'.
				$notes.'","'.$editNotes.'") ';
			//echo "<div>".$sqlInsert."</div>";
			if(!$this->conn->query($sqlInsert)){
				$rs->close();
                $returnStr = 'ERROR - Voucher insert failed.';
			}

			$this->tid = $row->tid;
			$rs->close();
            $returnStr = '';
		}
		return $returnStr;
	}

	public function removeVoucher($delOid): string
	{
		$statusStr = '';
		if(is_numeric($delOid)){
			$sqlDel = 'DELETE FROM fmvouchers WHERE occid = '.$delOid.' AND (TID = '.$this->tid.') AND (CLID = '.$this->clid.')';
			if(!$this->conn->query($sqlDel)){
				$statusStr = 'ERROR deleting voucher.';
			}
		}
		return $statusStr;
	}
}
