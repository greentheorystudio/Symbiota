<?php
include_once(__DIR__ . '/Checklists.php');
include_once(__DIR__ . '/KeyCharacterStates.php');
include_once(__DIR__ . '/../services/DbService.php');

class ChecklistTaxa{

	private $conn;

    private $fields = array(
        'cltlid' => array('dataType' => 'number', 'length' => 10),
        'tid' => array('dataType' => 'number', 'length' => 10),
        'clid' => array('dataType' => 'number', 'length' => 10),
        'habitat' => array('dataType' => 'string', 'length' => 250),
        'abundance' => array('dataType' => 'string', 'length' => 50),
        'notes' => array('dataType' => 'string', 'length' => 2000),
        'source' => array('dataType' => 'string', 'length' => 250),
        'nativity' => array('dataType' => 'string', 'length' => 50),
        'endemic' => array('dataType' => 'string', 'length' => 45),
        'invasive' => array('dataType' => 'string', 'length' => 45),
        'initialtimestamp' => array('dataType' => 'timestamp', 'length' => 0)
    );

    public function __construct(){
        $connection = new DbService();
	    $this->conn = $connection->getConnection();
	}

 	public function __destruct(){
        $this->conn->close();
	}

    public function batchCreateChecklistTaxaRecordsFromTidArr($clid, $tidArr): int
    {
        $recordsCreated = 0;
        $valueArr = array();
        if($clid && count($tidArr) > 0){
            foreach($tidArr as $tid){
                $valueArr[] = '(' . (int)$clid . ', ' . (int)$tid . ', "' . date('Y-m-d H:i:s') . '")';
            }
            if(count($valueArr) > 0){
                $sql = 'INSERT INTO fmchklsttaxalink(clid, tid, initialtimestamp) '.
                    'VALUES ' . implode(',', $valueArr) . ' ';
                //echo "<div>".$sql."</div>";
                if($this->conn->query($sql)){
                    $recordsCreated = $this->conn->affected_rows;
                }
            }
        }
        return $recordsCreated;
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

    public function deleteChecklistTaxonRecord($cltlid): int
    {
        $retVal = 1;
        $sql = 'DELETE FROM fmchklsttaxalink WHERE cltlid = ' . (int)$cltlid . ' ';
        if(!$this->conn->query($sql)){
            $retVal = 0;
        }
        return $retVal;
    }

    public function getChecklistTaxa($clidArr, $includeKeyData = false): array
    {
        $retArr = array();
        $tempArr = array();
        if(count($clidArr) > 0){
            $fieldNameArr = (new DbService)->getSqlFieldNameArrFromFieldData($this->fields, 'c');
            $fieldNameArr[] = 't.sciname';
            $fieldNameArr[] = 't.author';
            $fieldNameArr[] = 't.family';
            $sql = 'SELECT ' . implode(',', $fieldNameArr) . ' '.
                'FROM fmchklsttaxalink AS c LEFT JOIN taxa AS t ON c.tid = t.tid '.
                'WHERE c.clid IN(' . implode(',', $clidArr) . ') ';
            //echo '<div>'.$sql.'</div>';
            if($result = $this->conn->query($sql)){
                $fields = mysqli_fetch_fields($result);
                $tidArr = array();
                $rows = $result->fetch_all(MYSQLI_ASSOC);
                $result->free();
                foreach($rows as $index => $row){
                    if($includeKeyData && !in_array((int)$row['tid'], $tidArr, true)){
                        $tidArr[] = (int)$row['tid'];
                    }
                    $nodeArr = array();
                    foreach($fields as $val){
                        $name = $val->name;
                        if($name === 'family'){
                            $nodeArr[$name] = $row['family'] ?: '[Incertae Sedis]';
                        }
                        else{
                            $nodeArr[$name] = $row[$name];
                        }
                    }
                    if($includeKeyData){
                        $tempArr[] = $nodeArr;
                    }
                    else{
                        $retArr[] = $nodeArr;
                    }
                    unset($rows[$index]);
                }
                if($includeKeyData && count($tidArr) > 0){
                    $keyDataArr = (new KeyCharacterStates)->getTaxaKeyCharacterStates($tidArr);
                    foreach($tempArr as $taxonArr){
                        if(array_key_exists($taxonArr['tid'], $keyDataArr)){
                            $taxonArr['keyData'] = array_key_exists((int)$taxonArr['tid'], $keyDataArr) ? $keyDataArr[$taxonArr['tid']] : array();
                            $retArr[] = $taxonArr;
                        }
                    }
                }
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

    public function updateChecklistTaxonRecord($cltlid, $editData): int
    {
        $retVal = 0;
        $sqlPartArr = array();
        if($cltlid && $editData){
            foreach($this->fields as $field => $fieldArr){
                if($field !== 'initialtimestamp' && array_key_exists($field, $editData)){
                    $sqlPartArr[] = $field . ' = ' . SanitizerService::getSqlValueString($this->conn, $editData[$field], $fieldArr['dataType']);
                }
            }
            $sql = 'UPDATE fmchklsttaxalink SET ' . implode(', ', $sqlPartArr) . ' '.
                'WHERE cltlid = ' . (int)$cltlid . ' ';
            //echo "<div>".$sql."</div>";
            if($this->conn->query($sql)){
                $retVal = 1;
            }
        }
        return $retVal;
    }
}
