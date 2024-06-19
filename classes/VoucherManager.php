<?php
include_once(__DIR__ . '/../services/DbService.php');
include_once(__DIR__ . '/../services/SanitizerService.php');
 
class VoucherManager {

	private $conn;
	private $tid;
	private $taxonName;
	private $clid;
	private $clName;

	public function __construct() {
		$connection = new DbService();
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
			$checklistData['habitat'] = SanitizerService::cleanOutStr($row->Habitat);
			$checklistData['abundance'] = SanitizerService::cleanOutStr($row->Abundance);
			$checklistData['notes'] = SanitizerService::cleanOutStr($row->Notes);
			$checklistData['internalnotes'] = SanitizerService::cleanOutStr($row->internalnotes);
			$checklistData['source'] = SanitizerService::cleanOutStr($row->source);
			$checklistData['familyoverride'] = SanitizerService::cleanOutStr($row->familyoverride);
			$checklistData['cltype'] = $row->type;
			$checklistData['locality'] = $row->locality;
			if(!$this->clName) {
				$this->clName = SanitizerService::cleanOutStr($row->Name);
			}
			if(!$this->taxonName) {
				$this->taxonName = SanitizerService::cleanOutStr($row->SciName);
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
			$innerSql .= ',' .$k. '=' .($valStr?'"'.SanitizerService::cleanInStr($this->conn,$valStr).'" ':'NULL');
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
					$habitatTarget = SanitizerService::cleanInStr($this->conn,$row->Habitat);
					$abundTarget = SanitizerService::cleanInStr($this->conn,$row->Abundance);
					$notesTarget = SanitizerService::cleanInStr($this->conn,$row->Notes);
					$internalNotesTarget = SanitizerService::cleanInStr($this->conn,$row->internalnotes);
					$sourceTarget = SanitizerService::cleanInStr($this->conn,$row->source);
					$nativeTarget = SanitizerService::cleanInStr($this->conn,$row->Nativity);
				
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
						$habitatSource = SanitizerService::cleanInStr($this->conn,$row->Habitat);
						$abundSource = SanitizerService::cleanInStr($this->conn,$row->Abundance);
						$notesSource = SanitizerService::cleanInStr($this->conn,$row->Notes);
						$internalNotesSource = SanitizerService::cleanInStr($this->conn,$row->internalnotes);
						$sourceSource = SanitizerService::cleanInStr($this->conn,$row->source);
						$nativeSource = SanitizerService::cleanInStr($this->conn,$row->Nativity);
					}
					$rsSourceCl->close();
					$habitatStr = $habitatTarget.(($habitatTarget && $habitatSource)? '; ' : '').$habitatSource;
					$abundStr = $abundTarget.(($abundTarget && $abundSource)? '; ' : '').$abundSource;
					$notesStr = $notesTarget.(($notesTarget && $notesSource)? '; ' : '').$notesSource;
					$internalNotesStr = $internalNotesTarget.(($internalNotesTarget && $internalNotesSource)? '; ' : '').$internalNotesSource;
					$sourceStr = $sourceTarget.(($sourceTarget && $sourceSource)? '; ' : '').$sourceSource;
					$nativeStr = $nativeTarget.(($nativeTarget && $nativeSource)? '; ' : '').$nativeSource;
					$sqlCl = 'UPDATE fmchklsttaxalink SET Habitat = "'.SanitizerService::cleanInStr($this->conn,$habitatStr).'", '.
						'Abundance = "'.SanitizerService::cleanInStr($this->conn,$abundStr).'", Notes = "'.SanitizerService::cleanInStr($this->conn,$notesStr).
						'", internalnotes = "'.SanitizerService::cleanInStr($this->conn,$internalNotesStr).'", source = "'.
						SanitizerService::cleanInStr($this->conn,$sourceStr).'", Nativity = "'.SanitizerService::cleanInStr($this->conn,$nativeStr).'" '.
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
		$sql = 'SELECT IFNULL(securitystatus,0) AS securitystatus FROM taxa WHERE tid = '.$this->tid;
		//echo $sql;
		$rs = $this->conn->query($sql);
		if(($r = $rs->fetch_object()) && $r->securitystatus === 0) {
			$sqlRare = 'UPDATE omoccurrences AS o INNER JOIN taxa AS t ON o.tid = t.tid '.
				'SET o.localitysecurity = 0 '.
				'WHERE o.localitysecurity = 1 AND ISNULL(o.localitySecurityReason) '.
				'AND o.stateprovince = "'.$rareLocality.'" AND t.tidaccepted = '.$this->tid;
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
				'notes = '.($notes?'"'.SanitizerService::cleanInStr($this->conn,$notes).'"':'NULL').
				',editornotes = '.($editorNotes?'"'.SanitizerService::cleanInStr($this->conn,$editorNotes).'"':'NULL').
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
	    $vNotes = SanitizerService::cleanInStr($this->conn,$vNotes);
		$vEditNotes = SanitizerService::cleanInStr($this->conn,$vEditNotes);
		if(is_numeric($vOccId) && $vOccId && $this->clid) {
            $status = $this->addVoucherRecord($vOccId, $vNotes, $vEditNotes);
            if($status){
                $sqlInsertCl = 'INSERT INTO fmchklsttaxalink ( clid, TID ) '.
                    'SELECT '.$this->clid.' AS clid, o.tid '.
                    'FROM omoccurrences AS o WHERE o.occid = '.$vOccId.' ';
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
			'FROM omoccurrences AS o INNER JOIN taxa AS t ON o.tid = t.tid '.
			'INNER JOIN fmchklsttaxalink AS ctl ON t.tidaccepted = ctl.tid '.
			'WHERE ctl.clid = '.$this->clid.' AND o.occid = '. $vOccId.' '.
			'LIMIT 1';
		//echo "addVoucherSql: ".$sql."<br/>";
		$rs = $this->conn->query($sql);
		if($row = $rs->fetch_object()){
			$occId = $row->occid;
			$notes = SanitizerService::cleanInStr($this->conn,$row->Notes);
			$editNotes = SanitizerService::cleanInStr($this->conn,$row->editnotes);
			
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
