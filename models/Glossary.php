<?php
include_once(__DIR__ . '/TaxonHierarchy.php');
include_once(__DIR__ . '/../services/DbService.php');

class Glossary{

	private $conn;

    private $fields = array(
        'glossid' => array('dataType' => 'number', 'length' => 10),
        'term' => array('dataType' => 'string', 'length' => 150),
        'definition' => array('dataType' => 'string', 'length' => 2000),
        'language' => array('dataType' => 'string', 'length' => 45),
        'source' => array('dataType' => 'string', 'length' => 1000),
        'translator' => array('dataType' => 'string', 'length' => 250),
        'author' => array('dataType' => 'string', 'length' => 250),
        'notes' => array('dataType' => 'string', 'length' => 250),
        'resourceurl' => array('dataType' => 'string', 'length' => 600),
        'uid' => array('dataType' => 'number', 'length' => 10),
        'initialtimestamp' => array('dataType' => 'timestamp', 'length' => 0)
    );

    public function __construct(){
        $connection = new DbService();
	    $this->conn = $connection->getConnection();
	}

 	public function __destruct(){
        $this->conn->close();
	}

    public function createGlossaryRecord($data): int
    {
        $newID = 0;
        $fieldNameArr = array();
        $fieldValueArr = array();
        foreach($this->fields as $field => $fieldArr){
            if($field !== 'glossid' && $field !== 'uid' && $field !== 'initialtimestamp' && array_key_exists($field, $data)){
                $fieldNameArr[] = $field;
                $fieldValueArr[] = SanitizerService::getSqlValueString($this->conn, $data[$field], $fieldArr['dataType']);
            }
        }
        $fieldNameArr[] = 'uid';
        $fieldValueArr[] = $GLOBALS['SYMB_UID'];
        $fieldNameArr[] = 'initialtimestamp';
        $fieldValueArr[] = '"' . date('Y-m-d H:i:s') . '"';
        $sql = 'INSERT INTO glossary(' . implode(',', $fieldNameArr) . ') '.
            'VALUES (' . implode(',', $fieldValueArr) . ') ';
        //echo "<div>".$sql."</div>";
        if($this->conn->query($sql)){
            $newID = $this->conn->insert_id;
        }
        return $newID;
    }

    public function deleteGlossaryRecord($glossid): int
    {
        $retVal = 1;
        $sql = 'DELETE FROM glossaryimages WHERE glossid = ' . (int)$glossid . ' ';
        if(!$this->conn->query($sql)){
            $retVal = 0;
        }
        $sql = 'DELETE FROM glossarytaxalink WHERE glossid = ' . (int)$glossid . ' ';
        if(!$this->conn->query($sql)){
            $retVal = 0;
        }
        $sql = 'DELETE FROM glossarytermlink WHERE glossid = ' . (int)$glossid . ' ';
        if(!$this->conn->query($sql)){
            $retVal = 0;
        }
        $sql = 'DELETE FROM glossary WHERE glossid = ' . (int)$glossid . ' ';
        if(!$this->conn->query($sql)){
            $retVal = 0;
        }
        return $retVal;
    }

    public function getGlossaryData($glossid): array
    {
        $retArr = array();
        $fieldNameArr = (new DbService)->getSqlFieldNameArrFromFieldData($this->fields);
        $sql = 'SELECT ' . implode(',', $fieldNameArr) . ' '.
            'FROM glossary WHERE glossid = ' . (int)$glossid . ' ';
        //echo '<div>'.$sql.'</div>';
        if($result = $this->conn->query($sql)){
            $fields = mysqli_fetch_fields($result);
            $row = $result->fetch_array(MYSQLI_ASSOC);
            $result->free();
            if($row){
                foreach($fields as $val){
                    $name = $val->name;
                    $retArr[$name] = $row[$name];
                }
            }
        }
        return $retArr;
    }

    public function getTaxonGlossary($tid): array
    {
        $retArr = array();
        if($tid){
            $fieldNameArr = (new DbService)->getSqlFieldNameArrFromFieldData($this->fields, 'g');
            $tidArr = (new TaxonHierarchy)->getParentTidDataFromTidArr(array($tid));
            $tidArr[$tid][] = $tid;
            $sql = 'SELECT ' . implode(',', $fieldNameArr) . ' '.
                'FROM glossary AS g LEFT JOIN glossarytaxalink AS gt ON g.glossid = gt.glossid '.
                'WHERE gt.tid IN('.implode(',', $tidArr[$tid]).') '.
                'ORDER BY g.term ';
            //echo $sql; exit;
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
        }
        return $retArr;
    }

    public function updateGlossaryRecord($glossid, $editData): int
    {
        $retVal = 0;
        $sqlPartArr = array();
        if($glossid && $editData){
            foreach($this->fields as $field => $fieldArr){
                if($field !== 'glossid' && $field !== 'uid' && $field !== 'initialtimestamp' && array_key_exists($field, $editData)){
                    $sqlPartArr[] = $field . ' = ' . SanitizerService::getSqlValueString($this->conn, $editData[$field], $fieldArr['dataType']);
                }
            }
            $sql = 'UPDATE glossary SET ' . implode(', ', $sqlPartArr) . ' '.
                'WHERE glossid = ' . (int)$glossid . ' ';
            //echo "<div>".$sql."</div>";
            if($this->conn->query($sql)){
                $retVal = 1;
            }
        }
        return $retVal;
    }
}
