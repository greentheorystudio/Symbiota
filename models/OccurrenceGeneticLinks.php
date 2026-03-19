<?php
include_once(__DIR__ . '/Taxa.php');
include_once(__DIR__ . '/../services/DbService.php');
include_once(__DIR__ . '/../services/SanitizerService.php');

class OccurrenceGeneticLinks{

	private $conn;

    private $fields = array(
        'idoccurgenetic' => array('dataType' => 'number', 'length' => 11),
        'occid' => array('dataType' => 'number', 'length' => 10),
        'sourceidentifier' => array('dataType' => 'string', 'length' => 150),
        'sourcename' => array('dataType' => 'string', 'length' => 150),
        'description' => array('dataType' => 'string', 'length' => 500),
        'targetgene' => array('dataType' => 'string', 'length' => 500),
        'targetsubfragment' => array('dataType' => 'string', 'length' => 500),
        'dnasequence' => array('dataType' => 'text', 'length' => 0),
        'url' => array('dataType' => 'text', 'length' => 0),
        'notes' => array('dataType' => 'string', 'length' => 1000),
        'authors' => array('dataType' => 'string', 'length' => 500),
        'authorinstitution' => array('dataType' => 'string', 'length' => 500),
        'reference' => array('dataType' => 'string', 'length' => 750),
        'initialtimestamp' => array('dataType' => 'timestamp', 'length' => 0)
    );

    public function __construct(){
        $connection = new DbService();
	    $this->conn = $connection->getConnection();
	}

 	public function __destruct(){
        $this->conn->close();
	}

    public function clearOccurrenceGeneticLinkageRecordsByArr($glIdArr): int
    {
        $retVal = 0;
        if(count($glIdArr) > 0){
            $sql = 'DELETE FROM omoccurgenetic WHERE idoccurgenetic IN(' . implode(',', $glIdArr) . ') ';
            if($this->conn->query($sql)){
                $retVal = $this->conn->affected_rows;
            }
        }
        return $retVal;
    }

    public function createOccurrenceGeneticLinkageRecord($data): int
    {
        $newID = 0;
        $fieldNameArr = array();
        $fieldValueArr = array();
        $occId = array_key_exists('occid', $data) ? (int)$data['occid'] : 0;
        if($occId){
            foreach($this->fields as $field => $fieldArr){
                if($field !== 'idoccurgenetic' && array_key_exists($field, $data)){
                    $fieldNameArr[] = $field;
                    $fieldValueArr[] = SanitizerService::getSqlValueString($this->conn, $data[$field], $fieldArr['dataType']);
                }
            }
            $fieldNameArr[] = 'initialtimestamp';
            $fieldValueArr[] = '"' . date('Y-m-d H:i:s') . '"';
            $sql = 'INSERT INTO omoccurgenetic(' . implode(',', $fieldNameArr) . ') '.
                'VALUES (' . implode(',', $fieldValueArr) . ') ';
            if($this->conn->query($sql)){
                $newID = $this->conn->insert_id;
                (new Taxa)->updateGeneticDataIdentifiers();
            }
        }
        return $newID;
    }

    public function createOccurrenceGeneticLinkageRecordsFromUploadData($collId): int
    {
        $skipFields = array('idoccurgenetic', 'initialtimestamp');
        $retVal = 0;
        $fieldNameArr = array();
        if($collId){
            foreach($this->fields as $field => $fieldArr){
                if(!in_array($field, $skipFields)){
                    $fieldNameArr[] = $field;
                }
            }
            if(count($fieldNameArr) > 0){
                $idArr = array();
                $sql = 'SELECT DISTINCT u.upgid FROM uploadgenetictemp AS u LEFT JOIN omoccurgenetic AS g ON u.occid = g.occid '.
                    'WHERE u.collid  = ' . (int)$collId . ' AND u.occid IS NOT NULL AND (ISNULL(g.occid) OR (u.sourceidentifier <> g.sourceidentifier)) LIMIT 50000 ';
                if($result = $this->conn->query($sql)){
                    while($row = $result->fetch_assoc()){
                        $idArr[] = $row['upgid'];
                    }
                    $result->free();
                    if(count($idArr) > 0){
                        $sql = 'INSERT IGNORE INTO omoccurgenetic(' . implode(',', $fieldNameArr) . ') '.
                            'SELECT ' . implode(',', $fieldNameArr) . ' FROM uploadgenetictemp '.
                            'WHERE upgid IN(' . implode(',', $idArr) . ') ';
                        if($this->conn->query($sql)){
                            $retVal = $this->conn->affected_rows;
                            (new Taxa)->updateGeneticDataIdentifiers();
                        }
                    }
                }
            }
        }
        return $retVal;
    }

    public function deleteGeneticLinkageRecord($linkId): int
    {
        $retVal = 1;
        $sql = 'DELETE FROM omoccurgenetic WHERE idoccurgenetic = ' . (int)$linkId . ' ';
        if(!$this->conn->query($sql)){
            $retVal = 0;
        }
        (new Taxa)->updateGeneticDataIdentifiers();
        return $retVal;
    }

    public function deleteOccurrenceGeneticLinkageRecords($idType, $id): int
    {
        $retVal = 0;
        $whereStr = '';
        if($idType === 'occid'){
            $whereStr = 'occid = ' . (int)$id . ' ';
        }
        elseif($idType === 'occidArr'){
            $whereStr = 'occid IN(' . implode(',', $id) . ') ';
        }
        elseif($idType === 'collid'){
            $whereStr = 'occid IN(SELECT occid FROM omoccurrences WHERE collid = ' . (int)$id . ') ';
        }
        if($whereStr){
            $glIdArr = array();
            $sql = 'SELECT idoccurgenetic FROM omoccurgenetic WHERE ' . $whereStr . ' ';
            if($idType === 'collid'){
                $sql .= 'LIMIT 50000 ';
            }
            if($result = $this->conn->query($sql)){
                while($row = $result->fetch_assoc()){
                    $glIdArr[] = $row['idoccurgenetic'];
                }
                $result->free();
                $retVal = $this->clearOccurrenceGeneticLinkageRecordsByArr($glIdArr);
            }
        }
        (new Taxa)->updateGeneticDataIdentifiers();
        return $retVal;
    }

    public function getOccurrenceGeneticLinkData($occid): array
    {
        $retArr = array();
        $fieldNameArr = (new DbService)->getSqlFieldNameArrFromFieldData($this->fields);
        $sql = 'SELECT ' . implode(',', $fieldNameArr) . ' '.
            'FROM omoccurgenetic '.
            'WHERE occid = ' . (int)$occid . ' ORDER BY sourcename ';
        if($result = $this->conn->query($sql)){
            $fields = mysqli_fetch_fields($result);
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $index => $row){
                $nodeArr = array();
                foreach($fields as $val){
                    $name = $val->name;
                    $nodeArr[$name] = $row[$name];
                }
                $retArr[] = $nodeArr;
                unset($rows[$index]);
            }
        }
        return $retArr;
    }

    public function updateGeneticLinkageRecord($linkId, $editData): int
    {
        $retVal = 0;
        $sqlPartArr = array();
        if($linkId && $editData){
            foreach($this->fields as $field => $fieldArr){
                if($field !== 'idoccurgenetic' && array_key_exists($field, $editData)){
                    $sqlPartArr[] = $field . ' = ' . SanitizerService::getSqlValueString($this->conn, $editData[$field], $fieldArr['dataType']);
                }
            }
            $sql = 'UPDATE omoccurgenetic SET ' . implode(', ', $sqlPartArr) . ' '.
                'WHERE idoccurgenetic = ' . (int)$linkId . ' ';
            if($this->conn->query($sql)){
                $retVal = 1;
            }
        }
        return $retVal;
    }
}
