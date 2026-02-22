<?php
include_once(__DIR__ . '/../services/DbService.php');
include_once(__DIR__ . '/../services/SanitizerService.php');

class TaxonDescriptionStatements{

	private $conn;

    private $fields = array(
        'tdsid' => array('dataType' => 'number', 'length' => 10),
        'tdbid' => array('dataType' => 'number', 'length' => 10),
        'heading' => array('dataType' => 'string', 'length' => 75),
        'statement' => array('dataType' => 'text', 'length' => 0),
        'displayheader' => array('dataType' => 'number', 'length' => 10),
        'notes' => array('dataType' => 'string', 'length' => 250),
        'sortsequence' => array('dataType' => 'number', 'length' => 10),
        'initialtimestamp' => array('dataType' => 'timestamp', 'length' => 0)
    );

    public function __construct(){
        $connection = new DbService();
	    $this->conn = $connection->getConnection();
	}

 	public function __destruct(){
        $this->conn->close();
	}

    public function createTaxonDescriptionStatementRecord($data): int
    {
        $newID = 0;
        $fieldNameArr = array();
        $fieldValueArr = array();
        foreach($this->fields as $field => $fieldArr){
            if($field !== 'tdsid' && $field !== 'initialtimestamp' && $field !== 'displayheader' && array_key_exists($field, $data)){
                $fieldNameArr[] = $field;
                $fieldValueArr[] = SanitizerService::getSqlValueString($this->conn, $data[$field], $fieldArr['dataType']);
            }
        }
        $fieldNameArr[] = 'displayheader';
        $fieldValueArr[] = (int)$data['displayheader'] === 1 ? '1' : '0';
        $fieldNameArr[] = 'initialtimestamp';
        $fieldValueArr[] = '"' . date('Y-m-d H:i:s') . '"';
        $sql = 'INSERT INTO taxadescrstmts(' . implode(',', $fieldNameArr) . ') '.
            'VALUES (' . implode(',', $fieldValueArr) . ') ';
        //echo "<div>".$sql."</div>";
        if($this->conn->query($sql)){
            $newID = $this->conn->insert_id;
        }
        return $newID;
    }

    public function deleteTaxonDescriptionStatementRecord($tdsid): int
    {
        $retVal = 1;
        $sql = 'DELETE FROM taxadescrstmts WHERE tdsid = ' . (int)$tdsid . ' ';
        if(!$this->conn->query($sql)){
            $retVal = 0;
        }
        return $retVal;
    }

    public function getTaxonDescriptionStatements($tid): array
    {
        $retArr = array();
        $fieldNameArr = (new DbService)->getSqlFieldNameArrFromFieldData($this->fields, 'ts');
        $sql = 'SELECT ' . implode(',', $fieldNameArr) . ' '.
            'FROM taxadescrblock AS tb LEFT JOIN taxadescrstmts AS ts ON tb.tdbid = ts.tdbid '.
            'WHERE tb.tid = ' . (int)$tid . ' '.
            'ORDER BY ts.tdbid, ts.sortsequence ';
        //echo $sql;
        if($result = $this->conn->query($sql)){
            $fields = mysqli_fetch_fields($result);
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $index => $row){
                if(!array_key_exists($row['tdbid'], $retArr)){
                    $retArr[$row['tdbid']] = array();
                }
                $nodeArr = array();
                foreach($fields as $val){
                    $name = $val->name;
                    $nodeArr[$name] = $row[$name];
                }
                $retArr[$row['tdbid']][] = $nodeArr;
                unset($rows[$index]);
            }
        }
        return $retArr;
    }

    public function updateTaxonDescriptionStatementRecord($tdsid, $editData): int
    {
        $retVal = 0;
        $sqlPartArr = array();
        if($tdsid && $editData){
            foreach($this->fields as $field => $fieldArr){
                if($field !== 'tdsid' && $field !== 'initialtimestamp' && $field !== 'displayheader' && array_key_exists($field, $editData)){
                    $sqlPartArr[] = $field . ' = ' . SanitizerService::getSqlValueString($this->conn, $editData[$field], $fieldArr['dataType']);
                }
            }
            $sqlPartArr[] = 'displayheader = ' . ((int)$editData['displayheader'] === 1 ? '1' : '0');
            $sql = 'UPDATE taxadescrstmts SET ' . implode(', ', $sqlPartArr) . ' '.
                'WHERE tdsid = ' . (int)$tdsid . ' ';
            //echo "<div>".$sql."</div>";
            if($this->conn->query($sql)){
                $retVal = 1;
            }
        }
        return $retVal;
    }
}
