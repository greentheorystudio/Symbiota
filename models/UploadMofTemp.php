<?php
include_once(__DIR__ . '/../services/DbService.php');

class UploadMofTemp{

	private $conn;

    private $fields = array(
        'upmfid' => array('dataType' => 'number', 'length' => 50),
        'collid' => array('dataType' => 'number', 'length' => 10),
        'dbpk' => array('dataType' => 'string', 'length' => 150),
        'eventdbpk' => array('dataType' => 'string', 'length' => 150),
        'occid' => array('dataType' => 'number', 'length' => 10),
        'eventid' => array('dataType' => 'number', 'length' => 10),
        'field' => array('dataType' => 'string', 'length' => 250),
        'datavalue' => array('dataType' => 'string', 'length' => 1000),
        'enteredby' => array('dataType' => 'string', 'length' => 250),
        'initialtimestamp' => array('dataType' => 'timestamp', 'length' => 0)
    );

    public function __construct(){
        $connection = new DbService();
	    $this->conn = $connection->getConnection();
	}

 	public function __destruct(){
        $this->conn->close();
	}

    public function batchCreateRecords($collid, $data, $fieldMapping =  null, $eventMofFields = null, $occurrenceMofFields = null): int
    {
        $recordsCreated = 0;
        $fieldNameArr = array();
        $valueArr = array();
        $skipFields = array('upmfid', 'collid', 'occid', 'eventid', 'enteredby', 'initialtimestamp');
        $mappedFields = array();
        if($collid){
            $fieldNameArr[] = 'collid';
            $fieldNameArr[] = 'enteredby';
            foreach($this->fields as $field => $fieldArr){
                if(!in_array($field, $skipFields)){
                    $fieldNameArr[] = $field;
                    if($fieldMapping){
                        $mappedFieldVal = null;
                        $mappedKey = $fieldMapping[$field] ?? null;
                        if(($mappedKey && (string)$mappedKey !== 'unmapped') || (string)$mappedKey === '0'){
                            $mappedFieldVal = (string)$mappedKey;
                        }
                        $mappedFields[$field] = $mappedFieldVal;
                    }
                    elseif(array_key_exists($field, $data[0])){
                        $mappedFields[$field] = $field;
                    }
                }
            }
            foreach($data as $dataArr){
                $dataValueArr = array();
                $mofData = array();
                $dataValueArr[] = SanitizerService::getSqlValueString($this->conn, $collid, $this->fields['collid']);
                $dataValueArr[] = SanitizerService::getSqlValueString($this->conn, $GLOBALS['USERNAME'], $this->fields['enteredby']);
                foreach($mappedFields as $field => $key){
                    $mofData[$field] = ($key || (string)$key === '0') ? $dataArr[$key] : null;
                }
                if(array_key_exists('field', $mofData) && array_key_exists('datavalue', $mofData) && (!$fieldMapping || (($eventMofFields || $occurrenceMofFields) && (array_key_exists($mofData['field'], $eventMofFields) || array_key_exists($mofData['field'], $occurrenceMofFields))))) {
                    foreach($this->fields as $field => $fieldArr){
                        if(!in_array($field, $skipFields, true)){
                            $dataValue = $mofData[$field] ?? null;
                            $dataValueArr[] = SanitizerService::getSqlValueString($this->conn, SanitizerService::cleanInStr($this->conn, $dataValue), $fieldArr);
                        }
                    }
                    $valueArr[] = '(' . implode(',', $dataValueArr) . ')';
                }
            }
            if(count($valueArr) > 0){
                $sql = 'INSERT INTO uploadmoftemp(' . implode(',', $fieldNameArr) . ') '.
                    'VALUES ' . implode(',', $valueArr) . ' ';
                //echo "<div>".$sql."</div>";
                if($this->conn->query($sql)){
                    $recordsCreated = $this->conn->affected_rows;
                }
            }
        }
        return $recordsCreated;
    }

    public function clearCollectionData($collid): bool
    {
        if($collid){
            $sql = 'DELETE FROM uploadmoftemp WHERE collid = ' . (int)$collid . ' ';
            if($this->conn->query($sql)){
                return true;
            }
        }
        return false;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function getUploadCount($collid): int
    {
        $returnVal = 0;
        if($collid){
            $sql = 'SELECT COUNT(upmfid) AS cnt FROM uploadmoftemp WHERE collid  = ' . (int)$collid . ' ';
            if($result = $this->conn->query($sql)){
                $row = $result->fetch_array(MYSQLI_ASSOC);
                $result->free();
                if($row){
                    $returnVal = (int)$row['cnt'];
                }
            }
        }
        return $returnVal;
    }

    public function getUploadedMofDataFields($collid): array
    {
        $retArr = array();
        if($collid){
            $sql = 'SELECT DISTINCT field FROM uploadmoftemp WHERE collid = ' . (int)$collid . ' ';
            //echo '<div>'.$sql.'</div>';
            if($result = $this->conn->query($sql)){
                $rows = $result->fetch_all(MYSQLI_ASSOC);
                $result->free();
                foreach($rows as $index => $row){
                    $retArr[] = $row['field'];
                    unset($rows[$index]);
                }
            }
        }
        return $retArr;
    }

    public function populateMofIdentifiers($collid, $eventMofDataFields, $occurrenceMofDataFields): int
    {
        $returnVal = 1;
        if($collid){
            if(count($eventMofDataFields) > 0){
                $sql = 'UPDATE uploadmoftemp AS u LEFT JOIN uploadspectemp AS o ON u.eventdbpk = o.eventdbpk AND u.collid = o.collid '.
                    'SET u.eventid = o.eventid WHERE u.collid  = ' . (int)$collid . ' '.
                    'AND o.eventid IS NOT NULL AND u.field IN("' . implode('","', $eventMofDataFields) . '") ';
                if(!$this->conn->query($sql)){
                    $returnVal = 0;
                }
            }

            if($returnVal === 1 && count($occurrenceMofDataFields) > 0){
                $sql = 'UPDATE uploadmoftemp AS u LEFT JOIN uploadspectemp AS o ON u.dbpk = o.dbpk AND u.collid = o.collid '.
                    'SET u.occid = o.occid WHERE u.collid  = ' . (int)$collid . ' '.
                    'AND o.occid IS NOT NULL AND u.field IN("' . implode('","', $occurrenceMofDataFields) . '") ';
                if(!$this->conn->query($sql)){
                    $returnVal = 0;
                }
            }
        }
        return $returnVal;
    }

    public function removeExistingMofDataFromUpload($collid): int
    {
        $returnVal = 0;
        if($collid){
            $sql = 'DELETE u.* FROM uploadmoftemp AS u LEFT JOIN ommofextension AS m ON u.eventid = m.eventid '.
                'WHERE u.collid  = ' . $collid . ' AND m.eventid IS NOT NULL AND u.field = m.field ';
            if($this->conn->query($sql)){
                $returnVal = 1;
            }

            if($returnVal === 1){
                $sql = 'DELETE u.* FROM uploadmoftemp AS u LEFT JOIN ommofextension AS m ON u.occid = m.occid '.
                    'WHERE u.collid  = ' . $collid . ' AND m.occid IS NOT NULL AND u.field = m.field ';
                if(!$this->conn->query($sql)){
                    $returnVal = 0;
                }
            }
        }
        return $returnVal;
    }

    public function removeExistingOccurrenceDataFromUpload($collid): int
    {
        $returnVal = 0;
        if($collid){
            $sql = 'DELETE u.* FROM uploadmoftemp AS u LEFT JOIN omoccurrences AS o ON u.dbpk = o.dbpk AND u.collid = o.collid '.
                'WHERE u.collid  = ' . $collid . ' AND u.dbpk IS NOT NULL AND o.occid IS NOT NULL ';
            if($this->conn->query($sql)){
                $returnVal = 1;
            }
        }
        return $returnVal;
    }
}
