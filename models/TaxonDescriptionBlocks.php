<?php
include_once(__DIR__ . '/../services/DbService.php');
include_once(__DIR__ . '/../services/SanitizerService.php');

class TaxonDescriptionBlocks{

	private $conn;

    private $fields = array(
        "tdbid" => array("dataType" => "number", "length" => 10),
        "tid" => array("dataType" => "number", "length" => 10),
        "caption" => array("dataType" => "string", "length" => 40),
        "source" => array("dataType" => "string", "length" => 250),
        "sourceurl" => array("dataType" => "string", "length" => 250),
        "language" => array("dataType" => "string", "length" => 45),
        "langid" => array("dataType" => "number", "length" => 11),
        "displaylevel" => array("dataType" => "number", "length" => 10),
        "uid" => array("dataType" => "number", "length" => 10),
        "notes" => array("dataType" => "string", "length" => 250),
        "initialtimestamp" => array("dataType" => "timestamp", "length" => 0)
    );

    public function __construct(){
        $connection = new DbService();
	    $this->conn = $connection->getConnection();
	}

 	public function __destruct(){
        $this->conn->close();
	}

    public function createTaxonDescriptionBlockRecord($data): int
    {
        $newID = 0;
        $fieldNameArr = array();
        $fieldValueArr = array();
        foreach($this->fields as $field => $fieldArr){
            if($field !== 'tdbid' && $field !== 'uid' && $field !== 'initialtimestamp' && array_key_exists($field, $data)){
                if($field === 'source' || $field === 'language'){
                    $fieldNameArr[] = '`' . $field . '`';
                }
                else{
                    $fieldNameArr[] = $field;
                }
                $fieldValueArr[] = SanitizerService::getSqlValueString($this->conn, $data[$field], $fieldArr['dataType']);
            }
        }
        $fieldNameArr[] = 'uid';
        $fieldValueArr[] = $GLOBALS['SYMB_UID'];
        $fieldNameArr[] = 'initialtimestamp';
        $fieldValueArr[] = '"' . date('Y-m-d H:i:s') . '"';
        $sql = 'INSERT INTO taxadescrblock(' . implode(',', $fieldNameArr) . ') '.
            'VALUES (' . implode(',', $fieldValueArr) . ') ';
        //echo "<div>".$sql."</div>";
        if($this->conn->query($sql)){
            $newID = $this->conn->insert_id;
        }
        return $newID;
    }

    public function deleteTaxonDescriptionBlockRecord($tdbid): int
    {
        $retVal = 1;
        $sql = 'DELETE FROM taxadescrstmts WHERE tdbid = ' . (int)$tdbid . ' ';
        if(!$this->conn->query($sql)){
            $retVal = 0;
        }
        $sql = 'DELETE FROM taxadescrblock WHERE tdbid = ' . (int)$tdbid . ' ';
        if(!$this->conn->query($sql)){
            $retVal = 0;
        }
        return $retVal;
    }

    public function getTaxonDescriptionBlockData($tdbid): array
    {
        $retArr = array();
        $fieldNameArr = (new DbService)->getSqlFieldNameArrFromFieldData($this->fields);
        $sql = 'SELECT ' . implode(',', $fieldNameArr) . ' '.
            'FROM taxadescrblock WHERE tdbid = ' . (int)$tdbid . ' ';
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

    public function getTaxonDescriptions($tid): array
    {
        $retArr = array();
        $fieldNameArr = (new DbService)->getSqlFieldNameArrFromFieldData($this->fields);
        $sql = 'SELECT ' . implode(',', $fieldNameArr) . ' '.
            'FROM taxadescrblock WHERE tid = ' . (int)$tid . ' '.
            'ORDER BY displaylevel ';
        //echo $sql;
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

    public function updateTaxonDescriptionBlockRecord($tdbid, $editData): int
    {
        $retVal = 0;
        $sqlPartArr = array();
        if($tdbid && $editData){
            foreach($this->fields as $field => $fieldArr){
                if($field !== 'tdbid' && $field !== 'uid' && $field !== 'initialtimestamp' && array_key_exists($field, $editData)){
                    if($field === 'source' || $field === 'language'){
                        $fieldStr = '`' . $field . '`';
                    }
                    else{
                        $fieldStr = $field;
                    }
                    $sqlPartArr[] = $fieldStr . ' = ' . SanitizerService::getSqlValueString($this->conn, $editData[$field], $fieldArr['dataType']);
                }
            }
            $sql = 'UPDATE taxadescrblock SET ' . implode(', ', $sqlPartArr) . ' '.
                'WHERE tdbid = ' . (int)$tdbid . ' ';
            //echo "<div>".$sql."</div>";
            if($this->conn->query($sql)){
                $retVal = 1;
            }
        }
        return $retVal;
    }
}
