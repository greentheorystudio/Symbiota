<?php
include_once(__DIR__ . '/../services/DbConnectionService.php');

class OccurrenceSupport {

	private $conn;
	private $errorMessage;

	public function __construct(){
        $connection = new DbConnectionService();
	    $this->conn = $connection->getConnection();
	}

	public function __destruct(){
		if($this->conn) {
			$this->conn->close();
		}
	}

	public function getOccurrenceList($collid, $catalogNumber, $otherCatalogNumbers, $recordedBy, $recordNumber): array
	{
		$retArr = array();
		if(!$catalogNumber && !$otherCatalogNumbers && !$recordedBy && !$recordNumber) {
			return $retArr;
		}
		$sqlWhere = '';
		if($collid){
			$sqlWhere .= 'AND (o.collid = ' .$collid. ') ';
		}
		if($catalogNumber){
			$sqlWhere .= 'AND (o.catalognumber = "'.$catalogNumber.'") ';
		}
		if($otherCatalogNumbers){
			$sqlWhere .= 'AND (o.othercatalognumbers = "'.$otherCatalogNumbers.'") ';
		}
		if($recordedBy){
			if(strlen($recordedBy) < 4 || strtolower($recordedBy) === 'best'){
				$sqlWhere .= 'AND (o.recordedby LIKE "%'.$recordedBy.'%") ';
			}
			else{
				$sqlWhere .= 'AND (MATCH(f.recordedby) AGAINST("'.$recordedBy.'")) ';
			}
		}
		if($recordNumber){
			$sqlWhere .= 'AND (o.recordnumber = "'.$recordNumber.'") ';
		}
		$sql = 'SELECT o.occid, o.recordedby, o.recordnumber, o.eventdate, CONCAT_WS("; ",o.stateprovince, o.county, o.locality) AS locality '.
			'FROM omoccurrences o LEFT JOIN omoccurrencesfulltext f ON o.occid = f.occid '.
			'WHERE '.substr($sqlWhere,4);
		//echo $sql;
		$rs = $this->conn->query($sql);
		while($row = $rs->fetch_object()){
			$occId = $row->occid;
			$retArr[$occId]['recordedby'] = $row->recordedby;
			$retArr[$occId]['recordnumber'] = $row->recordnumber;
			$retArr[$occId]['eventdate'] = $row->eventdate;
			$retArr[$occId]['locality'] = $row->locality;
		}
		$rs->free();
		return $retArr;
	}
	
	public function getCollectionArr($filter): array
	{
		$retArr = array();
		if(!$filter) {
			return $retArr;
		}
		$sql = 'SELECT collid, collectionname FROM omcollections ';
		if($filter !== 'all' && is_array($filter)) {
			$sql .= 'WHERE collid IN(' . implode(',', $filter) . ')';
		}
		$rs = $this->conn->query($sql);
		while($row = $rs->fetch_object()){
			$retArr[$row->collid] = $row->collectionname;
		}
		$rs->free();
		asort($retArr);
		return $retArr;
	}

	public function exportCsvFile(): void
	{
		$fieldArr = array('occid','occurrenceID','catalogNumber','otherCatalogNumbers','family','sciname','genus','specificEpithet','taxonRank',
		'infraspecificEpithet','scientificNameAuthorship','taxonRemarks','identifiedBy','dateIdentified','identificationReferences',
		'identificationRemarks','identificationQualifier','typeStatus','recordedBy','recordNumber','associatedCollectors','eventDate',
		'year','month','day','verbatimEventDate','habitat','substrate','fieldnumber','occurrenceRemarks','informationWithheld',
		'associatedOccurrences','associatedTaxa','dynamicProperties','verbatimAttributes','behavior','reproductiveCondition','cultivationStatus',
		'establishmentMeans','lifeStage','sex','individualCount','samplingProtocol','samplingEffort','preparations','country','stateProvince',
		'county','municipality','locality','decimalLatitude','decimalLongitude','geodeticDatum','coordinateUncertaintyInMeters','locationRemarks',
		'verbatimCoordinates','minimumElevationInMeters','maximumElevationInMeters','verbatimElevation','minimumDepthInMeters',
		'maximumDepthInMeters','verbatimDepth','dateEntered','dateLastModified');
		$fileName = 'specimenOutput_'.time().'.csv';
		header ('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header ('Content-Type: text/csv');
		header ('Content-Disposition: attachment; filename="'.$fileName.'"');
		$sql = 'SELECT '.implode(',',$fieldArr).' FROM omoccurrences WHERE occid IN() ';
		$rs = $this->conn->query($sql);
		if($rs->num_rows){
			$out = fopen('php://output', 'wb');
			echo implode(',',$fieldArr)."\n";
			while($r = $rs->fetch_assoc()){
				fputcsv($out, $r);
			}
			fclose($out);
		}
		else{
			echo "Recordset is empty.\n";
		}
		$rs->free();
	}
	
	public function getErrorStr(){
		return $this->errorMessage;
	}
}
