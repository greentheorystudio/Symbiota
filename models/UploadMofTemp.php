<?php
include_once(__DIR__ . '/../services/DbService.php');

class UploadMofTemp{

	private $conn;

    private $fields = array(
        "upmfid" => array("dataType" => "number", "length" => 50),
        "collid" => array("dataType" => "number", "length" => 10),
        "dbpk" => array("dataType" => "string", "length" => 150),
        "eventdbpk" => array("dataType" => "string", "length" => 150),
        "occid" => array("dataType" => "number", "length" => 10),
        "eventid" => array("dataType" => "number", "length" => 10),
        "field" => array("dataType" => "string", "length" => 250),
        "datavalue" => array("dataType" => "string", "length" => 1000),
        "enteredby" => array("dataType" => "string", "length" => 250),
        "initialtimestamp" => array("dataType" => "timestamp", "length" => 0)
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
        $sourceKeyArr = array();
        $valueArr = array();
        if($collid){
            $sourceDataKeys = array_keys($data[0]);
            $fieldNameArr[] = 'collid';
            $sourceDataFieldNameIndex = $fieldMapping ? array_search('field', $fieldMapping, true) : 'field';
            foreach($sourceDataKeys as $key){
                if($key || (string)$key === '0'){
                    if(($fieldMapping && array_key_exists($key, $fieldMapping) && $fieldMapping[$key] !== 'unmapped') || !$fieldMapping){
                        $field = $fieldMapping ? $fieldMapping[$key] : $key;
                        $fieldNameArr[] = $field;
                        $sourceKeyArr[] = $key;
                    }
                }
            }
            foreach($data as $dataArr){
                $fieldName = $dataArr[$sourceDataFieldNameIndex];
                if(!$fieldMapping || (($eventMofFields || $occurrenceMofFields) && (array_key_exists($fieldName, $eventMofFields) || array_key_exists($fieldName, $occurrenceMofFields)))){
                    $dataValueArr = array();
                    $dataValueArr[] = SanitizerService::getSqlValueString($this->conn, $collid, $this->fields['collid']);
                    foreach($sourceKeyArr as $key){
                        $targetField = $fieldMapping ? $fieldMapping[$key] : $key;
                        $dataValueArr[] = SanitizerService::getSqlValueString($this->conn, $dataArr[$key], $this->fields[$targetField]);
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

    public function linkUploadToExistingOccurrenceData($collid): int
    {
        $returnVal = 0;
        if($collid){
            $sql = 'UPDATE uploadmoftemp AS u LEFT JOIN omoccurrences AS o ON u.dbpk = o.dbpk AND u.collid = o.collid '.
                'SET u.occid = o.occid '.
                'WHERE u.collid  = ' . $collid . ' AND u.dbpk IS NOT NULL AND o.occid IS NOT NULL ';
            if($this->conn->query($sql)){
                $returnVal = 1;
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
