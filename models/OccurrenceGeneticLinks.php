<?php
include_once(__DIR__ . '/../services/DbService.php');
include_once(__DIR__ . '/../services/SanitizerService.php');

class OccurrenceGeneticLinks{

	private $conn;

    private $fields = array(
        'idoccurgenetic' => array('dataType' => 'number', 'length' => 11),
        'occid' => array('dataType' => 'number', 'length' => 10),
        'identifier' => array('dataType' => 'string', 'length' => 150),
        'resourcename' => array('dataType' => 'string', 'length' => 150),
        'title' => array('dataType' => 'string', 'length' => 150),
        'locus' => array('dataType' => 'string', 'length' => 500),
        'resourceurl' => array('dataType' => 'string', 'length' => 500),
        'notes' => array('dataType' => 'string', 'length' => 250),
        'initialtimestamp' => array('dataType' => 'timestamp', 'length' => 0)
    );

    public function __construct(){
        $connection = new DbService();
	    $this->conn = $connection->getConnection();
	}

 	public function __destruct(){
        $this->conn->close();
	}

    public function createOccurrenceGeneticLinkageRecord($data): int
    {
        $newID = 0;
        $fieldNameArr = array();
        $fieldValueArr = array();
        $occId = array_key_exists('occid', $data) ? (int)$data['occid'] : 0;
        $name = array_key_exists('resourcename', $data) ? SanitizerService::cleanInStr($this->conn, $data['resourcename']) : '';
        if($occId && $name){
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
            //echo "<div>".$sql."</div>";
            if($this->conn->query($sql)){
                $newID = $this->conn->insert_id;
            }
        }
        return $newID;
    }

    public function deleteGeneticLinkageRecord($linkId): int
    {
        $retVal = 1;
        $sql = 'DELETE FROM omoccurgenetic WHERE idoccurgenetic = ' . (int)$linkId . ' ';
        if(!$this->conn->query($sql)){
            $retVal = 0;
        }
        return $retVal;
    }

    public function deleteOccurrenceGeneticLinkageRecords($idType, $id): int
    {
        $retVal = 0;
        $whereStr = '';
        if($idType === 'occid'){
            $whereStr = 'occid = ' . (int)$id;
        }
        elseif($idType === 'occidArr'){
            $whereStr = 'occid IN(' . implode(',', $id) . ')';
        }
        elseif($idType === 'collid'){
            $whereStr = 'occid IN(SELECT occid FROM omoccurrences WHERE collid = ' . (int)$id . ')';
        }
        if($whereStr){
            $sql = 'DELETE FROM omoccurgenetic WHERE ' . $whereStr . ' ';
            if($this->conn->query($sql)){
                $retVal = 1;
            }
        }
        return $retVal;
    }

    public function getOccurrenceGeneticLinkData($occid): array
    {
        $retArr = array();
        $fieldNameArr = (new DbService)->getSqlFieldNameArrFromFieldData($this->fields);
        $sql = 'SELECT ' . implode(',', $fieldNameArr) . ' '.
            'FROM omoccurgenetic '.
            'WHERE occid = ' . (int)$occid . ' ORDER BY resourcename ';
        //echo '<div>'.$sql.'</div>';
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
            //echo "<div>".$sql."</div>";
            if($this->conn->query($sql)){
                $retVal = 1;
            }
        }
        return $retVal;
    }
}
