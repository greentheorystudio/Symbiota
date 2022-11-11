<?php
include_once(__DIR__ . '/DbConnection.php');
include_once(__DIR__ . '/SOLRManager.php');
include_once(__DIR__ . '/Sanitizer.php');

class OccurrenceMaintenance {

	protected $conn;
	private $destructConn = true;
	private $verbose = false;	// 0 = silent, 1 = echo as list item
	private $errorArr = array();

	public function __construct($con = null){
		if($con){
			$this->conn = $con;
			$this->destructConn = false;
		}
		else{
            $connection = new DbConnection();
		    $this->conn = $connection->getConnection();
		}
	}

	public function __destruct(){
		if($this->destructConn && $this->conn){
			$this->conn->close();
			$this->conn = null;
		}
 	}

	public function generalOccurrenceCleaning($collId): bool
	{
		set_time_limit(600);
		$status = true;

		if($this->verbose) {
			$this->outputMsg('Updating null scientific names of family rank identifications... ', 1);
		}
		$sql1 = 'SELECT occid FROM omoccurrences WHERE collid = '.$collId.' AND family IS NOT NULL AND ISNULL(sciname) ';
		$rs1 = $this->conn->query($sql1);
		$occidArr2 = array();
		while($r1 = $rs1->fetch_object()){
			$occidArr2[] = $r1->occid;
		}
		$rs1->free();
		if($occidArr2){
			$sql = 'UPDATE omoccurrences SET sciname = family WHERE occid IN('.implode(',',$occidArr2).') ';
			if(!$this->conn->query($sql)){
				$errStr = 'WARNING: unable to update sciname using family.';
				$this->errorArr[] = $errStr;
				if($this->verbose) {
					$this->outputMsg($errStr, 2);
				}
				$status = false;
			}
		}
		unset($occidArr2);
		
		if($this->verbose) {
			$this->outputMsg('Indexing valid scientific names (e.g. populating tid)... ', 1);
		}
		$sql1 = 'SELECT o.occid FROM omoccurrences AS o INNER JOIN taxa AS t ON o.sciname = t.sciname '.
			'WHERE o.collid IN('.$collId.') AND ISNULL(o.tid) ';
		$rs1 = $this->conn->query($sql1);
		$occidArr3 = array();
		while($r1 = $rs1->fetch_object()){
			$occidArr3[] = $r1->occid;
		}
		$rs1->free();
		if($occidArr3){
			$sql = 'UPDATE omoccurrences AS o INNER JOIN taxa AS t ON o.sciname = t.sciname '.
				'SET o.tid = t.tid '.
				'WHERE o.occid IN('.implode(',',$occidArr3).') ';
			if(!$this->conn->query($sql)){
				$errStr = 'WARNING: unable to update tid.';
				$this->errorArr[] = $errStr;
				if($this->verbose) {
					$this->outputMsg($errStr, 2);
				}
				$status = false;
			}
		}
		unset($occidArr3);
		
		if($this->verbose) {
			$this->outputMsg('Updating and indexing occurrence images... ', 1);
		}
		$sql1 = 'SELECT o.occid FROM omoccurrences AS o INNER JOIN images AS i ON o.occid = i.occid '.
			'WHERE o.collid IN('.$collId.') AND ISNULL(i.tid) AND o.tid IS NOT NULL ';
		$rs1 = $this->conn->query($sql1);
		$occidArr4 = array();
		while($r1 = $rs1->fetch_object()){
			$occidArr4[] = $r1->occid;
		}
		$rs1->free();
		if($occidArr4){
			$sql = 'UPDATE omoccurrences AS o INNER JOIN images AS i ON o.occid = i.occid '.
				'SET i.tid = o.tid '.
				'WHERE o.occid IN('.implode(',',$occidArr4).')';
			if(!$this->conn->query($sql)){
				$errStr = 'WARNING: unable to update image tid field.';
				$this->errorArr[] = $errStr;
				if($this->verbose) {
					$this->outputMsg($errStr, 2);
				}
				$status = false;
			}
		}
		unset($occidArr4);
		
		return $status;
	}
	
	public function protectRareSpecies($collid = null): void
	{
		$this->protectGloballyRareSpecies();
		$this->protectStateRareSpecies($collid);
	}
	
	public function protectGloballyRareSpecies(): bool
	{
		$status = true;
		if($this->verbose) {
			$this->outputMsg('Protecting globally rare species... ', 1);
		}
		$sensitiveArr = array();
		$sql = 'SELECT DISTINCT tid FROM taxa WHERE (SecurityStatus > 0)';
		$rs = $this->conn->query($sql); 
		while($r = $rs->fetch_object()){
			$sensitiveArr[] = $r->tid; 
		}
		$rs->free();
		$sql2 = 'SELECT DISTINCT ts.tid '.
			'FROM taxa AS t INNER JOIN taxstatus AS ts ON t.tid = ts.tidaccepted '.
			'WHERE t.SecurityStatus > 0 AND t.tid != ts.tid ';
		$rs2 = $this->conn->query($sql2);
		while($r2 = $rs2->fetch_object()){
			$sensitiveArr[] = $r2->tid;
		}
		$rs2->free();
		
		if($sensitiveArr){
			$sql2 = 'UPDATE omoccurrences '.
				'SET localitySecurity = 1 '.
				'WHERE (ISNULL(localitySecurity) OR localitySecurity = 0) AND ISNULL(localitySecurityReason) AND tid IN('.implode(',',$sensitiveArr).') ';
			if(!$this->conn->query($sql2)){
				$errStr = 'WARNING: unable to protect globally rare species.';
				$this->errorArr[] = $errStr;
				if($this->verbose) {
					$this->outputMsg($errStr, 2);
				}
				$status = false;
			}
		}
		return $status;
	}

	public function protectStateRareSpecies($collid = null): bool
	{
		$status = true;
		if($this->verbose) {
			$this->outputMsg('Protecting state level rare species... ', 1);
		}
		$sql = 'SELECT o.occid FROM omoccurrences AS o INNER JOIN taxa AS t ON o.tid = t.tid '.
			'INNER JOIN fmchecklists AS c ON o.stateprovince = c.locality '.
			'INNER JOIN fmchklsttaxalink AS cl ON c.clid = cl.clid AND t.tidaccepted = cl.tid '.
			'WHERE (ISNULL(o.localitysecurity) OR o.localitysecurity = 0) AND ISNULL(o.localitySecurityReason) AND c.type = "rarespp" ';
		if($collid) {
			$sql .= ' AND o.collid IN(' . $collid . ') ';
		}
		$rs = $this->conn->query($sql);
		$occArr = array();
		while($r = $rs->fetch_object()){
			$occArr[] = $r->occid;
		}
		$rs->free();
		
		if($occArr){
			$sql2 = 'UPDATE omoccurrences '.
				'SET localitysecurity = 1 '.
				'WHERE occid IN('.implode(',',$occArr).')';
			if(!$this->conn->query($sql2)){
				$errStr = 'WARNING: unable to protect state level rare species.';
				$this->errorArr[] = $errStr;
				if($this->verbose) {
					$this->outputMsg($errStr, 2);
				}
				$status = false;
			}
		}
		return $status;
	}

    public function protectGlobalSpecies($collid = null): int
    {
        $status = 0;
        if($this->verbose) {
            $this->outputMsg('Protecting globally rare species... ', 1);
        }
        $sensitiveArr = $this->getSensitiveTaxa();

        if($sensitiveArr){
            $sql = 'UPDATE omoccurrences '.
                'SET localitySecurity = 1 '.
                'WHERE (ISNULL(localitySecurity) OR localitySecurity = 0) AND ISNULL(localitySecurityReason) AND tid IN('.implode(',',$sensitiveArr).') ';
            if($collid) {
                $sql .= 'AND collid = ' . $collid . ' ';
            }
            if($this->conn->query($sql)){
                $status += $this->conn->affected_rows;
            }
            else{
                $errStr = 'WARNING: unable to protect globally rare species; '.$this->conn->error;
                $this->errorArr[] = $errStr;
                if($this->verbose) {
                    $this->outputMsg($errStr, 2);
                }
            }
        }
        $sql2 = 'UPDATE omoccurrences '.
            'SET localitySecurity = 0 '.
            'WHERE localitySecurity = 1 AND ISNULL(localitySecurityReason) AND tid NOT IN('.implode(',',$sensitiveArr).') ';
        if($collid) {
            $sql2 .= 'AND collid = ' . $collid . ' ';
        }
        if($this->conn->query($sql2)){
            $status += $this->conn->affected_rows;
        }
        return $status;
    }

    private function getSensitiveTaxa(): array
    {
        $sensitiveArr = array();
        $sql = 'SELECT DISTINCT tid FROM taxa WHERE (SecurityStatus > 0)';
        $rs = $this->conn->query($sql);
        while($r = $rs->fetch_object()){
            $sensitiveArr[] = $r->tid;
        }
        $rs->free();
        $sql2 = 'SELECT DISTINCT ts.tid '.
            'FROM taxa AS t INNER JOIN taxstatus AS ts ON t.tid = ts.tidaccepted '.
            'WHERE t.SecurityStatus > 0 AND t.tid != ts.tid ';
        $rs2 = $this->conn->query($sql2);
        while($r2 = $rs2->fetch_object()){
            $sensitiveArr[] = $r2->tid;
        }
        $rs2->free();
        return $sensitiveArr;
    }

	public function updateCollectionStats($collid, $full = null): bool
	{
        set_time_limit(600);
		$recordCnt = 0;
		$georefCnt = 0;
		$familyCnt = 0;
		$genusCnt = 0;
		$speciesCnt = 0;
		if($full){
			$statsArr = array();
			if($this->verbose) {
				$this->outputMsg('Calculating occurrence, georeference, family, genera, and species counts... ', 1);
			}
			$sql = 'SELECT COUNT(o.occid) AS SpecimenCount, COUNT(o.decimalLatitude) AS GeorefCount, '.
				'COUNT(DISTINCT o.family) AS FamilyCount, COUNT(o.typeStatus) AS TypeCount, '.
				'COUNT(DISTINCT CASE WHEN t.RankId >= 180 THEN t.UnitName1 ELSE NULL END) AS GeneraCount, '.
				'COUNT(CASE WHEN t.RankId >= 220 THEN o.occid ELSE NULL END) AS SpecimensCountID, '.
				'COUNT(DISTINCT CASE WHEN t.RankId = 220 THEN t.SciName ELSE NULL END) AS SpeciesCount, '.
				'COUNT(DISTINCT CASE WHEN t.RankId >= 220 THEN t.SciName ELSE NULL END) AS TotalTaxaCount '.
				'FROM omoccurrences AS o LEFT JOIN taxa AS t ON o.tid = t.TID '.
				'WHERE o.collid IN('.$collid.') ';
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$recordCnt = $r->SpecimenCount;
				$georefCnt = $r->GeorefCount;
				$familyCnt = $r->FamilyCount;
				$genusCnt = $r->GeneraCount;
				$speciesCnt = $r->SpeciesCount;
				$statsArr['SpecimensCountID'] = $r->SpecimensCountID;
				$statsArr['TotalTaxaCount'] = $r->TotalTaxaCount;
				$statsArr['TypeCount'] = $r->TypeCount;
			}
			$rs->free();

			if($this->verbose) {
				$this->outputMsg('Calculating number of occurrences imaged... ', 1);
			}
			$sql = 'SELECT count(DISTINCT o.occid) as imgcnt '.
				'FROM omoccurrences AS o INNER JOIN images AS i ON o.occid = i.occid '.
				'WHERE o.collid IN('.$collid.') ';
			$rs = $this->conn->query($sql);
			if($r = $rs->fetch_object()){
				$statsArr['imgcnt'] = $r->imgcnt;
			}
			$rs->free();

			if($this->verbose) {
				$this->outputMsg('Calculating genetic resources counts... ', 1);
			}
			$sql = 'SELECT COUNT(CASE WHEN g.resourceurl LIKE "http://www.boldsystems%" THEN o.occid ELSE NULL END) AS boldcnt, '.
				'COUNT(CASE WHEN g.resourceurl LIKE "http://www.ncbi%" THEN o.occid ELSE NULL END) AS gencnt '.
				'FROM omoccurrences AS o INNER JOIN omoccurgenetic AS g ON o.occid = g.occid '.
				'WHERE o.collid IN('.$collid.') ';
			$rs = $this->conn->query($sql);
			if($r = $rs->fetch_object()){
				$statsArr['boldcnt'] = $r->boldcnt;
				$statsArr['gencnt'] = $r->gencnt;
			}
			$rs->free();

			if($this->verbose) {
				$this->outputMsg('Calculating reference counts... ', 1);
			}
			$sql = 'SELECT count(r.occid) AS refcnt '.
				'FROM omoccurrences AS o INNER JOIN referenceoccurlink AS r ON o.occid = r.occid '.
				'WHERE o.collid IN('.$collid.') ';
			$rs = $this->conn->query($sql);
			if($r = $rs->fetch_object()){
				$statsArr['refcnt'] = $r->refcnt;
			}
			$rs->free();

			if($this->verbose) {
				$this->outputMsg('Calculating counts per family... ', 1);
			}
			$sql = 'SELECT o.family, COUNT(o.occid) AS SpecimensPerFamily, COUNT(o.decimalLatitude) AS GeorefSpecimensPerFamily, '.
				'COUNT(CASE WHEN t.RankId >= 220 THEN o.occid ELSE NULL END) AS IDSpecimensPerFamily, '.
				'COUNT(CASE WHEN t.RankId >= 220 AND o.decimalLatitude IS NOT NULL THEN o.occid ELSE NULL END) AS IDGeorefSpecimensPerFamily '.
				'FROM omoccurrences AS o LEFT JOIN taxa AS t ON o.tid = t.TID '.
				'WHERE o.collid IN('.$collid.') '.
				'GROUP BY o.family ';
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$family = str_replace(array('"',"'"), '',$r->family);
				if($family){
					$statsArr['families'][$family]['SpecimensPerFamily'] = $r->SpecimensPerFamily;
					$statsArr['families'][$family]['GeorefSpecimensPerFamily'] = $r->GeorefSpecimensPerFamily;
					$statsArr['families'][$family]['IDSpecimensPerFamily'] = $r->IDSpecimensPerFamily;
					$statsArr['families'][$family]['IDGeorefSpecimensPerFamily'] = $r->IDGeorefSpecimensPerFamily;
				}
			}
			$rs->free();
			
			if($this->verbose) {
				$this->outputMsg('Calculating counts per country... ', 1);
			}
			$sql = 'SELECT o.country, COUNT(o.occid) AS CountryCount, COUNT(o.decimalLatitude) AS GeorefSpecimensPerCountry, '.
				'COUNT(CASE WHEN t.RankId >= 220 THEN o.occid ELSE NULL END) AS IDSpecimensPerCountry, '.
				'COUNT(CASE WHEN t.RankId >= 220 AND o.decimalLatitude IS NOT NULL THEN o.occid ELSE NULL END) AS IDGeorefSpecimensPerCountry '.
				'FROM omoccurrences AS o LEFT JOIN taxa AS t ON o.tid = t.TID '.
				'WHERE o.collid IN('.$collid.') '.
				'GROUP BY o.country ';
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$country = str_replace(array('"',"'"), '',$r->country);
				if($country){
					$statsArr['countries'][$country]['CountryCount'] = $r->CountryCount;
					$statsArr['countries'][$country]['GeorefSpecimensPerCountry'] = $r->GeorefSpecimensPerCountry;
					$statsArr['countries'][$country]['IDSpecimensPerCountry'] = $r->IDSpecimensPerCountry;
					$statsArr['countries'][$country]['IDGeorefSpecimensPerCountry'] = $r->IDGeorefSpecimensPerCountry;
				}
			}
			$rs->free();

			$returnArrJson = json_encode($statsArr);
			$sql = 'UPDATE omcollectionstats '.
				"SET dynamicProperties = '".Sanitizer::cleanInStr($this->conn,$returnArrJson)."' ".
				'WHERE collid IN('.$collid.') ';
			if(!$this->conn->query($sql)){
				$errStr = 'WARNING: unable to update collection stats table [1].';
				$this->errorArr[] = $errStr;
				if($this->verbose) {
					$this->outputMsg($errStr, 2);
				}
			}
		}
		else{
			if($this->verbose) {
				$this->outputMsg('Calculating occurrence, georeference, family, genera, and species counts... ', 1);
			}
			$sql = 'SELECT COUNT(o.occid) AS SpecimenCount, COUNT(o.decimalLatitude) AS GeorefCount, COUNT(DISTINCT o.family) AS FamilyCount, '.
				'COUNT(DISTINCT CASE WHEN t.RankId >= 180 THEN t.UnitName1 ELSE NULL END) AS GeneraCount, '.
				'COUNT(DISTINCT CASE WHEN t.RankId = 220 THEN t.SciName ELSE NULL END) AS SpeciesCount '.
				'FROM omoccurrences AS o LEFT JOIN taxa AS t ON o.tid = t.TID '.
				'WHERE o.collid IN('.$collid.') ';
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$recordCnt = $r->SpecimenCount;
				$georefCnt = $r->GeorefCount;
				$familyCnt = $r->FamilyCount;
				$genusCnt = $r->GeneraCount;
				$speciesCnt = $r->SpeciesCount;
			}
		}
		
		$sql = 'UPDATE omcollectionstats AS cs '.
			'SET cs.recordcnt = '.$recordCnt.',cs.georefcnt = '.$georefCnt.',cs.familycnt = '.$familyCnt.',cs.genuscnt = '.$genusCnt.
			',cs.speciescnt = '.$speciesCnt.', cs.datelastmodified = CURDATE() '.
			'WHERE cs.collid IN('.$collid.')';
		if(!$this->conn->query($sql)){
			$errStr = 'WARNING: unable to update collection stats table [2].';
			$this->errorArr[] = $errStr;
			if($this->verbose) {
				$this->outputMsg($errStr, 2);
			}
		}
		if($GLOBALS['SOLR_MODE']){
            $solrManager = new SOLRManager();
            $solrManager->updateSOLR();
        }
		return true;
	}
	
	public function getCollectionMetadata($collid): array
	{
		$retArr = array();
		if(is_numeric($collid)){
			$sql = 'SELECT institutioncode, collectioncode, collectionname, colltype, managementtype '.
				'FROM omcollections '.
				'WHERE collid = '.$collid;
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$retArr['instcode'] = $r->institutioncode;
				$retArr['collcode'] = $r->collectioncode;
				$retArr['collname'] = $r->collectionname;
				$retArr['colltype'] = $r->colltype;
				$retArr['mantype'] = $r->managementtype;
			}
			$rs->free();
		}
		return $retArr;
	}
	
	public function setVerbose($v): void
	{
		if($v){
			$this->verbose = true;
		}
		else{
			$this->verbose = false;
		}
	}

	public function getErrorArr(): array
	{
		return $this->errorArr;
	}

	private function outputMsg($str, $indent = null): void
	{
		if($this->verbose){
			echo '<li style="margin-left:'.($indent?$indent*10:'0').'px;">'.$str.'</li>';
		}
		flush();
	}
}
