<?php
include_once(__DIR__ . '/../models/Occurrences.php');
include_once(__DIR__ . '/../models/Taxa.php');
include_once(__DIR__ . '/../services/DbConnectionService.php');
include_once(__DIR__ . '/../services/SanitizerService.php');
include_once(__DIR__ . '/../services/UuidService.php');

class OccurrenceDeterminations{

	private $conn;

    private $fields = array(
        "detid" => array("dataType" => "number", "length" => 10),
        "occid" => array("dataType" => "number", "length" => 10),
        "identifiedby" => array("dataType" => "string", "length" => 60),
        "dateidentified" => array("dataType" => "string", "length" => 45),
        "sciname" => array("dataType" => "string", "length" => 100),
        "verbatimscientificname" => array("dataType" => "string", "length" => 255),
        "tid" => array("dataType" => "number", "length" => 10),
        "scientificnameauthorship" => array("dataType" => "string", "length" => 100),
        "identificationqualifier" => array("dataType" => "string", "length" => 45),
        "iscurrent" => array("dataType" => "number", "length" => 11),
        "printqueue" => array("dataType" => "number", "length" => 11),
        "identificationreferences" => array("dataType" => "string", "length" => 255),
        "identificationremarks" => array("dataType" => "string", "length" => 500),
        "sortsequence" => array("dataType" => "number", "length" => 10),
        "initialtimestamp" => array("dataType" => "timestamp", "length" => 0)
    );

    public function __construct(){
        $connection = new DbConnectionService();
	    $this->conn = $connection->getConnection();
	}

 	public function __destruct(){
		if($this->conn) {
            $this->conn->close();
        }
	}

    public function createOccurrenceDeterminationRecord($data): int
    {
        $newID = 0;
        $fieldNameArr = array();
        $fieldValueArr = array();
        $occId = array_key_exists('occid', $data) ? (int)$data['occid'] : 0;
        $sciname = array_key_exists('sciname', $data) ? SanitizerService::cleanInStr($this->conn, $data['sciname']) : '';
        if($occId && $sciname){
            $isCurrent = (array_key_exists('iscurrent', $data) && (int)$data['iscurrent'] === 1);
            if($isCurrent){
                $sqlSetCur1 = 'UPDATE omoccurdeterminations SET iscurrent = 0 WHERE occid = ' . $occId;
                $this->conn->query($sqlSetCur1);
            }
            foreach($this->fields as $field => $fieldArr){
                if($field !== 'detid' && array_key_exists($field, $data)){
                    $fieldNameArr[] = $field;
                    $fieldValueArr[] = SanitizerService::getSqlValueString($this->conn, $data[$field], $fieldArr['dataType']);
                }
            }
            $fieldNameArr[] = 'initialtimestamp';
            $fieldValueArr[] = '"' . date('Y-m-d H:i:s') . '"';
            $sql = 'INSERT INTO omoccurdeterminations(' . implode(',', $fieldNameArr) . ') '.
                'VALUES (' . implode(',', $fieldValueArr) . ') ';
            //echo "<div>".$sql."</div>";
            if($this->conn->query($sql)){
                $newID = $this->conn->insert_id;
                $guid = UuidService::getUuidV4();
                $this->conn->query('INSERT INTO guidoccurdeterminations(guid, detid) VALUES("' . $guid . '",' . $newID . ')');
                if($isCurrent){
                    $this->createOccurrenceDeterminationRecordFromOccurrence($occId);
                    if(array_key_exists('tid', $data) && (int)$data['tid'] > 0){
                        $taxonData = (new Taxa)->getTaxonFromTid($data['tid']);
                        $data['family'] = $taxonData['family'];
                        $data['localitysecurity'] = $taxonData['securitystatus'];
                    }
                    (new Occurrences)->updateOccurrenceRecord($occId, $data, true);
                }
            }
        }
        return $newID;
    }

    public function createOccurrenceDeterminationRecordFromOccurrence($occid): void
    {
        $sql = 'INSERT IGNORE INTO omoccurdeterminations(occid, tid, identifiedby, dateidentified, sciname, verbatimscientificname, '.
            'scientificnameauthorship, identificationqualifier, identificationreferences, identificationremarks, sortsequence) '.
            'SELECT occid, tid, IFNULL(identifiedby, "unknown"), IFNULL(dateidentified, "unknown"), sciname, verbatimScientificName, '.
            'scientificnameauthorship, identificationqualifier, identificationreferences, identificationremarks, 10 '.
            'FROM omoccurrences WHERE occid = ' . (int)$occid . ' ';
        //echo "<div>".$sqlInsert."</div>";
        if($this->conn->query($sql)){
            $guid = UuidService::getUuidV4();
            $detId = $this->conn->insert_id;
            $this->conn->query('INSERT IGNORE INTO guidoccurdeterminations(guid, detid) VALUES("' . $guid . '" ,' . $detId . ')');
        }
    }

    public function getDeterminationFields(): array
    {
        return $this->fields;
    }

    public function getOccurrenceDeterminationData($occid): array
    {
        $retArr = array();
        $sql = 'SELECT d.detid, d.identifiedby, d.dateidentified, d.sciname, d.verbatimscientificname, d.tid, d.scientificnameauthorship, ' .
            'd.identificationqualifier, d.iscurrent, d.identificationreferences, d.identificationremarks, d.sortsequence, d.printqueue '.
            'FROM omoccurdeterminations AS d '.
            'WHERE d.occid = ' . (int)$occid . ' ORDER BY d.iscurrent DESC, d.sortsequence ';
        //echo '<div>'.$sql.'</div>';
        if($rs = $this->conn->query($sql)){
            $fields = mysqli_fetch_fields($rs);
            while($r = $rs->fetch_object()){
                $nodeArr = array();
                foreach($fields as $val){
                    $name = $val->name;
                    $nodeArr[$name] = $r->$name;
                }
                if($nodeArr['tid'] && (int)$nodeArr['tid'] > 0){
                    $nodeArr['taxonData'] = (new Taxa)->getTaxonFromTid($nodeArr['tid']);
                }
                $retArr[] = $nodeArr;
            }
            $rs->free();
        }
        return $retArr;
    }
}
