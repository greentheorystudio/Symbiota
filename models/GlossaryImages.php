<?php
include_once(__DIR__ . '/../services/DbService.php');

class GlossaryImages{

	private $conn;

    private $fields = array(
        'glimgid' => array('dataType' => 'number', 'length' => 10),
        'glossid' => array('dataType' => 'number', 'length' => 10),
        'url' => array('dataType' => 'string', 'length' => 255),
        'structures' => array('dataType' => 'string', 'length' => 150),
        'notes' => array('dataType' => 'string', 'length' => 250),
        'createdby' => array('dataType' => 'string', 'length' => 250),
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

    public function createGlossaryImageRecord($data): int
    {
        $newID = 0;
        $fieldNameArr = array();
        $fieldValueArr = array();
        foreach($this->fields as $field => $fieldArr){
            if($field !== 'glimgid' && $field !== 'uid' && $field !== 'initialtimestamp' && array_key_exists($field, $data)){
                $fieldNameArr[] = $field;
                $fieldValueArr[] = SanitizerService::getSqlValueString($this->conn, $data[$field], $fieldArr['dataType']);
            }
        }
        $fieldNameArr[] = 'uid';
        $fieldValueArr[] = $GLOBALS['SYMB_UID'];
        $fieldNameArr[] = 'initialtimestamp';
        $fieldValueArr[] = '"' . date('Y-m-d H:i:s') . '"';
        $sql = 'INSERT INTO glossaryimages(' . implode(',', $fieldNameArr) . ') '.
            'VALUES (' . implode(',', $fieldValueArr) . ') ';
        if($this->conn->query($sql)){
            $newID = $this->conn->insert_id;
        }
        return $newID;
    }

    public function deleteGlossaryImageRecord($glimgid): int
    {
        $retVal = 1;
        $sql = 'DELETE FROM glossaryimages WHERE glimgid = ' . (int)$glimgid . ' ';
        if(!$this->conn->query($sql)){
            $retVal = 0;
        }
        return $retVal;
    }

    public function getGlossaryImageDataFromGlossidArr($glossidArr): array
    {
        $retArr = array();
        $sql = 'SELECT DISTINCT g.glossid, gi.url, gi.structures, gi.notes, gi.createdby '.
            'FROM glossary AS g LEFT JOIN glossarytermlink AS gt ON g.glossid = gt.glossid '.
            'LEFT JOIN glossarytermlink AS gt2 ON gt.glossgrpid = gt2.glossgrpid '.
            'LEFT JOIN glossaryimages AS gi ON gt2.glossid = gi.glossid '.
            'WHERE g.glossid IN(' . implode(',', $glossidArr) . ') AND gi.glimgid IS NOT NULL ';
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
                $retArr[$row['glossid']][] = $nodeArr;
                unset($rows[$index]);
            }
        }
        return $retArr;
    }

    public function updateGlossaryImageRecord($glimgid, $editData): int
    {
        $retVal = 0;
        $sqlPartArr = array();
        if($glimgid && $editData){
            foreach($this->fields as $field => $fieldArr){
                if($field !== 'glimgid' && $field !== 'uid' && $field !== 'initialtimestamp' && array_key_exists($field, $editData)){
                    $sqlPartArr[] = $field . ' = ' . SanitizerService::getSqlValueString($this->conn, $editData[$field], $fieldArr['dataType']);
                }
            }
            $sql = 'UPDATE glossaryimages SET ' . implode(', ', $sqlPartArr) . ' '.
                'WHERE glimgid = ' . (int)$glimgid . ' ';
            if($this->conn->query($sql)){
                $retVal = 1;
            }
        }
        return $retVal;
    }
}
