<?php
include_once(__DIR__ . '/../services/DbService.php');

class ChecklistTaxa{

	private $conn;

    private $fields = array(
        "tid" => array("dataType" => "number", "length" => 10),
        "clid" => array("dataType" => "number", "length" => 10),
        "morphospecies" => array("dataType" => "string", "length" => 45),
        "familyoverride" => array("dataType" => "number", "length" => 50),
        "habitat" => array("dataType" => "string", "length" => 250),
        "abundance" => array("dataType" => "string", "length" => 50),
        "notes" => array("dataType" => "string", "length" => 2000),
        "explicitexclude" => array("dataType" => "number", "length" => 6),
        "source" => array("dataType" => "string", "length" => 250),
        "nativity" => array("dataType" => "string", "length" => 50),
        "endemic" => array("dataType" => "string", "length" => 45),
        "invasive" => array("dataType" => "string", "length" => 45),
        "internalnotes" => array("dataType" => "string", "length" => 250),
        "dynamicproperties" => array("dataType" => "text", "length" => 0),
        "initialtimestamp" => array("dataType" => "timestamp", "length" => 0)
    );

    public function __construct(){
        $connection = new DbService();
	    $this->conn = $connection->getConnection();
	}

 	public function __destruct(){
        $this->conn->close();
	}

    public function createChecklistTaxonRecord($clid, $data): int
    {
        $newID = 0;
        $fieldNameArr = array();
        $fieldValueArr = array();
        foreach($this->fields as $field => $fieldArr){
            if($field !== 'clid' && $field !== 'initialtimestamp' && array_key_exists($field, $data)){
                $fieldNameArr[] = $field;
                $fieldValueArr[] = SanitizerService::getSqlValueString($this->conn, $data[$field], $fieldArr['dataType']);
            }
        }
        $fieldNameArr[] = 'clid';
        $fieldValueArr[] = (int)$clid;
        $fieldNameArr[] = 'initialtimestamp';
        $fieldValueArr[] = '"' . date('Y-m-d H:i:s') . '"';
        $sql = 'INSERT INTO fmchklsttaxalink(' . implode(',', $fieldNameArr) . ') '.
            'VALUES (' . implode(',', $fieldValueArr) . ') ';
        //echo "<div>".$sql."</div>";
        if($this->conn->query($sql)){
            $newID = $this->conn->insert_id;
        }
        return $newID;
    }

    public function deleteChecklistTaxonRecord($clid, $tid): int
    {
        $retVal = 1;
        $sql = 'DELETE FROM fmchklsttaxalink WHERE tid = ' . (int)$tid . ' AND clid = ' . (int)$clid . ' ';
        if(!$this->conn->query($sql)){
            $retVal = 0;
        }
        return $retVal;
    }

    public function getChecklistTaxa($clid): array
    {
        $retArr = array();
        $sql = 'SELECT t.tid, t.sciname, t.author, t.family, c.habitat, c.abundance, c.notes '.
            'FROM fmchklsttaxalink AS c LEFT JOIN taxa AS t ON c.tid = t.tid '.
            'WHERE c.clid = ' . (int)$clid . ' ';
        //echo '<div>'.$sql.'</div>';
        if($result = $this->conn->query($sql)){
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $index => $row){
                $nodeArr = array();
                $nodeArr['tid'] = $row['tid'];
                $nodeArr['sciname'] = $row['sciname'];
                $nodeArr['author'] = $row['author'];
                $nodeArr['family'] = $row['family'] ?: '[Incertae Sedis]';
                $nodeArr['habitat'] = $row['habitat'];
                $nodeArr['abundance'] = $row['abundance'];
                $nodeArr['notes'] = $row['notes'];
                $retArr[] = $nodeArr;
                unset($rows[$index]);
            }
        }
        return $retArr;
    }

    public function getChecklistTaxonData($clid, $tid): array
    {
        $retArr = array();
        $fieldNameArr = (new DbService)->getSqlFieldNameArrFromFieldData($this->fields);
        $sql = 'SELECT ' . implode(',', $fieldNameArr) . ' '.
            'FROM fmchklsttaxalink WHERE tid = ' . (int)$tid . ' AND clid = ' . (int)$clid . ' ';
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

    public function updateChecklistTaxonRecord($clid, $tid, $editData): int
    {
        $retVal = 0;
        $sqlPartArr = array();
        if($clid && $tid && $editData){
            foreach($this->fields as $field => $fieldArr){
                if($field !== 'initialtimestamp' && array_key_exists($field, $editData)){
                    $sqlPartArr[] = $field . ' = ' . SanitizerService::getSqlValueString($this->conn, $editData[$field], $fieldArr['dataType']);
                }
            }
            $sql = 'UPDATE fmchklsttaxalink SET ' . implode(', ', $sqlPartArr) . ' '.
                'WHERE tid = ' . (int)$tid . ' AND clid = ' . (int)$clid . ' ';
            //echo "<div>".$sql."</div>";
            if($this->conn->query($sql)){
                $retVal = 1;
            }
        }
        return $retVal;
    }
}
