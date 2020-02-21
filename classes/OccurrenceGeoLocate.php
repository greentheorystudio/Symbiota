<?php
include_once($SERVER_ROOT.'/classes/DbConnection.php');

class OccurrenceGeoLocate {

	private $conn;
	private $collid;
	private $filterArr = array();
	private $errorStr;
	private $collArr;

	public function __construct() {
		$connection = new DbConnection();
		$this->conn = $connection->getConnection();
	}

	public function __destruct(){
 		if(!($this->conn === false)) {
			$this->conn->close();
		}
	}

	public function batchConvertTrs(): array
	{
		return $this->getTrsOccurrences();
	}

	private function getTrsOccurrences($limit = 100): array
	{
		$retArr = array();
		if($this->collid){
			$sql = 'SELECT occid, country, stateprovince, county, verbatimCoordinates, '.
				'IF(verbatimCoordinates LIKE "%TRS:%", TRIM(substr(verbatimCoordinates, INSTR(verbatimCoordinates, "TRS:")+4, LENGTH(verbatimCoordinates))), verbatimCoordinates) AS verbcoords '.
				'FROM omoccurrences '.$this->getTrsSqlWhere();
			if($limit) {
				$sql .= 'LIMIT ' . $limit;
			}
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$retArr[$r->occid]['country'] = $r->country;
				$retArr[$r->occid]['state'] = $r->stateprovince;
				$retArr[$r->occid]['county'] = $r->county;
				$retArr[$r->occid]['verbcoords'] = $r->verbcoords;
			}
			$rs->free();
		}
		return $retArr;
	}

	private function getTrsSqlWhere(): string
	{
		$sql = 'WHERE (collid = '.$this->collid.') AND (county IS NOT NULL) AND ISNULL(decimalLatitude) '.
			'AND (locality regexp "T\\.? ?[0-9]{1,3}?[NS]\\.?,? ?R\\.? ?[0-9]{1,3} ?[EW]\\.?,? ?.*" '.
			'OR verbatimCoordinates regexp "T\\.? ?[0-9]{1,3} ?[NS]\\.?,? ?R\\.? ?[0-9]{1,3} ?[EW]\\.?,? ?.*") ';
		if(isset($this->filterArr['country']) && $this->filterArr['country']){
			$sql .= 'AND (country = "'.$this->cleanInStr($this->filterArr['country']).'" ';
		}
		if(isset($this->filterArr['stateProvince']) && $this->filterArr['stateProvince']){
			$sql .= 'AND (stateProvince = "'.$this->cleanInStr($this->filterArr['stateProvince']).'" ';
		}
		if(isset($this->filterArr['county']) && $this->filterArr['county']){
			$countyTerm = $this->cleanInStr($this->filterArr['county']);
			$countyTerm = str_replace(array(' county',' parish'),'',$countyTerm);
			$sql .= 'AND (county LIKE "'.$countyTerm.'%" ';
		}
		if(isset($this->filterArr['locality']) && $this->filterArr['locality']){
			$sql .= 'AND (locality LIKE "%'.$this->cleanInStr($this->filterArr['locality']).'%" ';
		}
		return $sql;
	}

	public function loadOccurrences($postArr): void
	{
		$sql = 'UPDATE occurrences ';
		foreach($postArr as $fieldName => $fieldValue){
			$occid = '';
			$decLat = '';
			$decLng = '';
			$coordErr = '';
			if(is_numeric($occid) && is_numeric($decLat) && is_numeric($decLng) && is_numeric($coordErr)){
				$sql .= 'SET decimallatitude = '.$decLat.', decimallongitude = '.$decLng.', coordinateUncertaintyInMeters = '.$coordErr.
					', georeferenceSource = CONCAT("Batch georeferences using GeoLocate services (",curdate(),")") '.
					'WHERE (occid = '.$occid.') AND (decimallatitude IS NULL) AND (decimallongitude IS NULL) ';
				if(!$this->conn->query($sql)){
					$this->errorStr = 'ERROR loading georef data: '.$this->conn->query($sql);
				}
			}
		}
	}

	public function setCollId($cid): void
	{
		if(is_numeric($cid)){
			$this->collid = $cid;
			$sql = 'SELECT collectionname, managementtype '.
				'FROM omcollections WHERE collid = '.$cid;
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$this->collArr['name'] = $r->collectionname;
				$this->collArr['mtype'] = $r->managementtype;
			}
			$rs->free();
		}
	}

	public function addFilterTerm($term, $value): void
	{
		$this->filterArr[$term] = $value;
	}

	private function cleanInStr($str){
		$newStr = trim($str);
		$newStr = preg_replace('/\s\s+/', ' ',$newStr);
		$newStr = $this->conn->real_escape_string($newStr);
		return $newStr;
	}
}
